// 此vm参数为页面的实例，可以通过它引用vuex中的变量
module.exports = (vm) => {
    // === 无感刷新Token相关 ===
    let isRefreshing = false; // 是否正在刷新Token的锁
    let requestsQueue = []; // 存储等待刷新Token后重试的请求队列

    // 将请求加入刷新队列
    function addToQueue(callback) {
        requestsQueue.push(callback);
    }

    // 执行队列中的所有请求
    function flushQueue(newToken) {
        requestsQueue.forEach(callback => callback(newToken));
        requestsQueue = [];
    }

    // 初始化请求配置
    uni.$u.http.setConfig((config) => {
        config.baseURL = 'https://e.zztuku.com/api.php'; // 根域名
        return config
    })

    // 请求拦截
    uni.$u.http.interceptors.request.use((config) => {
        config.data = config.data || {}

        // 如果正在刷新中且有refresh_token，则等待刷新完成后再发请求
        if (isRefreshing) {
            return new Promise((resolve) => {
                addToQueue((newToken) => {
                    config.header['Authorization'] = 'Bearer ' + newToken;
                    resolve(uni.$u.http.request(config));
                });
            });
        }

        const token = uni.getStorageSync('access_token');
        if (token) {
            config.header['Authorization'] = 'Bearer ' + token;
        }
        return config
    }, config => {
        return Promise.reject(config)
    })

    // 响应拦截
    uni.$u.http.interceptors.response.use(async (response) => {
        const data = response.data;
        const config = response.config;

        // Token过期
        if (data.code === 401) {
            const refreshToken = uni.getStorageSync('refresh_token');

            if (!refreshToken) {
                handleLogout();
                return Promise.reject(data);
            }

            // 如果已经在刷新中，将请求加入队列等待
            if (isRefreshing) {
                return new Promise((resolve, reject) => {
                    addToQueue((newToken) => {
                        config.header['Authorization'] = 'Bearer ' + newToken;
                        uni.$u.http.request(config)
                            .then(res => resolve(res))
                            .catch(err => reject(err));
                    });
                });
            }

            // 开始刷新
            isRefreshing = true;

            try {
                const refreshRes = await uni.$u.http.post('/user/refresh', {
                    refresh_token: refreshToken
                });

                if (refreshRes && refreshRes.access_token) {
                    const newAccessToken = refreshRes.access_token;
                    uni.setStorageSync('access_token', newAccessToken);

                    // 刷新成功后，更新正在排队的请求的Token并重试
                    flushQueue(newAccessToken);

                    // 重试当前请求
                    config.header['Authorization'] = 'Bearer ' + newAccessToken;
                    const retryRes = await uni.$u.http.request(config);
                    return retryRes.data === undefined ? {} : retryRes.data;
                } else {
                    handleLogout();
                    return Promise.reject(data);
                }
            } catch (e) {
                handleLogout();
                return Promise.reject(data);
            } finally {
                isRefreshing = false;
            }
        }

        // 其他业务逻辑处理
        if (data.code !== 200) {
            const custom = config?.custom;
            if (custom.toast !== false) {
                uni.$u.toast(data.message || '请求失败');
            }
            if (custom?.catch) {
                return Promise.reject(data);
            } else {
                return new Promise(() => {});
            }
        }

        return data.data === undefined ? {} : data.data;
    }, (response) => {
        return Promise.reject(response);
    })

    // 统一退出登录
    function handleLogout() {
        requestsQueue = []; // 清空等待队列
        isRefreshing = false;
        uni.removeStorageSync('access_token');
        uni.removeStorageSync('refresh_token');
        uni.reLaunch({ url: '/pages/login/login' });
    }
}