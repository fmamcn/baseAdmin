// 此vm参数为页面的实例，可以通过它引用vuex中的变量
module.exports = (vm) => {
    // === 新增：定义全局变量 ===
    let isRefreshing = false; // 是否正在刷新Token的锁
    let requestsQueue = []; // 存储等待刷新Token后重试的请求队列

    // 初始化请求配置
    uni.$u.http.setConfig((config) => {
        config.baseURL = 'https://e.zztuku.com/api.php'; // 根域名
        return config
    })

    // 请求拦截
    uni.$u.http.interceptors.request.use((config) => {
        config.data = config.data || {}
		
		const token = uni.getStorageSync('access_token');
		if (token) {
			config.header['Authorization'] = 'Bearer ' + token; // 加上 Bearer 前缀更符合标准
		}
        return config 
    }, config => {
        return Promise.reject(config)
    })

    // 响应拦截
    uni.$u.http.interceptors.response.use(async (response) => {
        const data = response.data;
        const config = response.config; // 获取原始请求配置，用于重试

        // 1. 假设后端返回 code: 401 表示 Token 过期
        if (data.code === 401) {
            // 修改点2：使用 uni.getStorageSync 获取 Refresh Token
            const refreshToken = uni.getStorageSync('refresh_token');
            
            // 如果没有 Refresh Token，直接跳转登录
            if (!refreshToken) {
                handleLogout();
                return Promise.reject(data);
            }

            // 2. 关键逻辑：无感刷新
            return new Promise((resolve, reject) => {
                
                // 将本次失败的请求加入队列，等待新 Token
                requestsQueue.push(() => {
                    // 队列中的请求会用新 Token 重新发起
                    uni.$u.http.request({
                        ...config,
                        custom: { ...config.custom, auth: false } 
                    }).then(res => {
                        if (res.code === 200) {
                            resolve(res.data);
                        } else {
                            reject(res);
                        }
                    }).catch(err => {
                        reject(err);
                    });
                });

                // 3. 如果当前没有在刷新，才发起刷新请求（避免并发）
                if (!isRefreshing) {
                    isRefreshing = true;
                    // 调用刷新接口
                    uni.$u.http.post('/user/refresh', { 
                        refresh_token: refreshToken 
                    }).then(refreshRes => {
                        if (refreshRes && refreshRes.access_token) {
                            const newAccessToken = refreshRes.access_token;                            
                            // 修改点3：使用 uni.setStorageSync 将新 Token 持久化保存到本地
                            uni.setStorageSync('access_token', newAccessToken);
                            // 如果你的后端刷新时连 refresh_token 也一起更新了，记得这里也要保存：
                            // uni.setStorageSync('refresh_token', refreshRes.data.refresh_token);

                            // 解锁并执行队列中的所有请求
                            requestsQueue.forEach(callback => callback());
                            requestsQueue = [];
                            
                        } else {
                            // 刷新失败，可能是 Refresh Token 也过期了
                            handleLogout();
                        }
                    }).catch(() => {
                        handleLogout();
                    }).finally(() => {
                        isRefreshing = false;
                    });
                }
            });
        }

        // 4. 其他业务逻辑处理
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
        // 对响应错误做点什么
        return Promise.reject(response);
    })

    // === 新增：统一退出登录方法 ===
    function handleLogout() {
        // 修改点4：退出登录时，清除本地缓存的 Token
        uni.removeStorageSync('access_token');
        uni.removeStorageSync('refresh_token');
        
        // 跳转登录页
        uni.reLaunch({ url: '/pages/login/login' });
        // 清空等待队列
        requestsQueue = [];
    }
}