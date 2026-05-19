<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2016 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 流年 <liu21st@gmail.com>
// +----------------------------------------------------------------------

!\think\Config::get('app_debug') && error_reporting(E_ERROR | E_PARSE);

// 应用公共文件

/**
 * 发送邮件
 * @param $email
 * @param $title
 * @param $content
 * @param null $config
 * @return bool
 */
function send_email($email, $title, $content, $config = null)
{
    $config = empty($config) ? unserialize(config('email_server')) : $config;
    $mail   = new \PHPMailer\PHPMailer\PHPMailer(true); // Passing `true` enables exceptions
    try {
        //Server settings
        $mail->SMTPDebug = 0;                           // Enable verbose debug output
        $mail->isSMTP();                                // Set mailer to use SMTP
        $mail->Host       = $config['host'];            // Specify main and backup SMTP servers
        $mail->SMTPAuth   = true;                       // Enable SMTP authentication
        $mail->Username   = $config['username'];        // SMTP username
        $mail->Password   = $config['password'];        // SMTP password
        $mail->SMTPSecure = $config['secure'];          // Enable TLS encryption, `ssl` also accepted
        $mail->Port       = $config['port'];            // TCP port to connect to
        $mail->CharSet    = 'UTF-8';
        //Recipients
        $mail->setFrom($config['username'], $config['fromname']);
        $mail->addAddress($email);                      // Name is optional
        //Content
        $mail->isHTML(true);                            // Set email format to HTML
        $mail->Subject = $title;
        $mail->Body    = $content;
        if ($mail->send()) {
            return true;
        } else {
            return false;
        }
    } catch (Exception $e) {
        return false;
    }
}

/**
 * http请求
 * @param string $url 请求的地址
 * @param array $data 发送的参数
 */
function https_request($url, $data = null)
{
    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
    if (!empty($data)) {
        curl_setopt($curl, CURLOPT_POST, 1);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
    }
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
    $output = curl_exec($curl);
    curl_close($curl);
    return $output;
}

/**
 * 格式化字节大小
 * @param  number $size 字节数
 * @param  string $delimiter 数字和单位分隔符
 * @return string            格式化后的带单位的大小
 */
function format_bytes($size, $delimiter = '')
{
    $units = array('B', 'KB', 'MB', 'GB', 'TB', 'PB');
    for ($i = 0; $size >= 1024 && $i < 5; $i++) $size /= 1024;
    return round($size, 2) . $delimiter . $units[$i];
}

/**
 * 把json字符串转数组
 * @param json $p
 * @return array
 */
function json_to_array($p)
{
    if (mb_detect_encoding($p, array('ASCII', 'UTF-8', 'GB2312', 'GBK')) != 'UTF-8') {
        $p = iconv('GBK', 'UTF-8', $p);
    }
    return json_decode($p, true);
}

// 生成唯一订单号
function build_order_no()
{
    return date('Ymd') . substr(implode(null, array_map('ord', str_split(substr(uniqid(), 7, 13), 1))), 0, 8);
}

/**
 * 获取随机位数数字
 * @param  integer $len 长度
 * @return string
 */
function rand_number($len = 6)
{
    return substr(str_shuffle(str_repeat('0123456789', 10)), 0, $len);
}

/**
 * 验证手机号是否正确
 * @param number $mobile
 */
function check_mobile($mobile)
{
    if (!is_numeric($mobile)) {
        return false;
    }
    return preg_match('#^13[\d]{9}$|^14[5,7]{1}\d{8}$|^15[^4]{1}\d{8}$|^17[0,6,7,8]{1}\d{8}$|^18[\d]{9}$|^19[\d]{9}$#', $mobile) ? true : false;
}

/**
 * 验证邮箱格式
 * @param string $email 邮箱
 * @return boolean
 */
function check_email($email)
{
    $chars = "/^[0-9a-zA-Z]+(?:[\_\.\-][a-z0-9\-]+)*@[a-zA-Z0-9]+(?:[-.][a-zA-Z0-9]+)*\.[a-zA-Z]+$/i";
    if (preg_match($chars, $email)) {
        return true;
    } else {
        return false;
    }
}

/**
 * 验证密码长度
 * @param string $password 需要验证的密码
 * @param int $min 最小长度
 * @param int $max 最大长度
 */
function check_password($password, $min, $max)
{
    if (strlen($password) < $min || strlen($password) > $max) {
        return false;
    }
    return true;
}

/**
 * 配置值解析成数组
 * @param string $value 配置值
 * @return array|string
 */
function parse_attr($value)
{
    if (is_array($value)) {
        return $value;
    }
    $array = preg_split('/[,;\r\n]+/', trim($value, ",;\r\n"));
    if (strpos($value, ':')) {
        $value = array();
        foreach ($array as $val) {
            list($k, $v) = explode(':', $val);
            $value[$k] = $v;
        }
    } else {
        $value = $array;
    }
    return $value;
}

/**
 * 数组层级缩进转换
 * @param array $array 源数组
 * @param int $pid
 * @param int $level
 * @return array
 */
function list_to_level($array, $pid = 0, $level = 1)
{
    static $list = [];
    foreach ($array as $k => $v) {
        if ($v['pid'] == $pid) {
            $v['level'] = $level;
            $list[]     = $v;
            unset($array[$k]);
            list_to_level($array, $v['id'], $level + 1);
        }
    }
    return $list;
}

/**
 * 把返回的数据集转换成Tree
 * @param array $list 要转换的数据集
 * @param string $pid parent标记字段
 * @param string $level level标记字段
 * @return array
 * @author 麦当苗儿 <zuojiazi@vip.qq.com>
 */
function list_to_tree($list, $pk = 'id', $pid = 'pid', $child = 'children', $root = 0)
{
    // 创建Tree
    $tree = array();
    if (is_array($list)) {
        // 创建基于主键的数组引用
        $refer = array();
        foreach ($list as $key => $data) {
            $refer[$data[$pk]] = &$list[$key];
        }
        foreach ($list as $key => $data) {
            // 判断是否存在parent
            $parentId = $data[$pid];
            if ($root == $parentId) {
                $tree[] = &$list[$key];
            } else {
                if (isset($refer[$parentId])) {
                    $parent           = &$refer[$parentId];
                    $parent[$child][] = &$list[$key];
                }
            }
        }
    }
    return $tree;
}

/**
 * 将list_to_tree的树还原成列表
 * @param  array $tree 原来的树
 * @param  string $child 孩子节点的键
 * @param  string $order 排序显示的键，一般是主键 升序排列
 * @param  array $list 过渡用的中间数组，
 * @return array        返回排过序的列表数组
 * @author yangweijie <yangweijiester@gmail.com>
 */
function tree_to_list($tree, $child = 'children', $order = 'id', &$list = array())
{
    if (is_array($tree)) {
        $refer = array();
        foreach ($tree as $key => $value) {
            $reffer = $value;
            if (isset($reffer[$child])) {
                unset($reffer[$child]);
                tree_to_list($value[$child], $child, $order, $list);
            }
            $list[] = $reffer;
        }
        $list = list_sort_by($list, $order, $sortby = 'asc');
    }
    return $list;
}

/**
 * 对查询结果集进行排序
 * @access public
 * @param array $list 查询结果
 * @param string $field 排序的字段名
 * @param array $sortby 排序类型
 * asc正向排序 desc逆向排序 nat自然排序
 * @return array
 */
function list_sort_by($list, $field, $sortby = 'asc')
{
    if (is_array($list)) {
        $refer = $resultSet = array();
        foreach ($list as $i => $data) {
            $refer[$i] = &$data[$field];
        }

        switch ($sortby) {
            case 'asc': // 正向排序
                asort($refer);
                break;
            case 'desc': // 逆向排序
                arsort($refer);
                break;
            case 'nat': // 自然排序
                natcasesort($refer);
                break;
        }
        foreach ($refer as $key => $val) {
            $resultSet[] = &$list[$key];
        }

        return $resultSet;
    }
    return false;
}

// 驼峰命名法转下划线风格
function to_under_score($str)
{
    $array = array();
    for ($i = 0; $i < strlen($str); $i++) {
        if ($str[$i] == strtolower($str[$i])) {
            $array[] = $str[$i];
        } else {
            if ($i > 0) {
                $array[] = '_';
            }
            $array[] = strtolower($str[$i]);
        }
    }
    $result = implode('', $array);
    return $result;
}

/**
 * hashids加密函数
 * @param $id
 * @param string $salt
 * @param int $min_hash_length
 * @return bool|string
 * @throws Exception
 */
function hashids_encode($id, $salt = '', $min_hash_length = 6)
{
    return (new Hashids\Hashids($salt, $min_hash_length))->encode($id);
}

/**
 * hashids解密函数
 * @param $id
 * @param string $salt
 * @param int $min_hash_length
 * @return null
 * @throws Exception
 */
function hashids_decode($id, $salt = '', $min_hash_length = 6)
{
    $id = (new Hashids\Hashids($salt, $min_hash_length))->decode($id);
    if (empty($id)) {
        return null;
    }
    return $id['0'];
}

/**
 * 保存后台用户行为
 * @param string $remark 日志备注
 */
function insert_admin_log($remark)
{
    if (session('?admin_auth')) {
        db('adminLog')->insert([
            'admin_id'    => session('admin_auth.admin_id'),
            'username'    => session('admin_auth.username'),
            'useragent'   => request()->server('HTTP_USER_AGENT'),
            'ip'          => request()->ip(),
            'url'         => request()->url(true),
            'method'      => request()->method(),
            'type'        => request()->type(),
            'param'       => json_encode(request()->param()),
            'remark'      => $remark,
            'create_time' => time(),
        ]);
    }
}

/**
 * 检测管理员是否登录
 * @return integer 0/管理员ID
 */
function is_admin_login()
{
    $admin = session('admin_auth');
    if (empty($admin)) {
        return 0;
    } else {
        return session('admin_auth_sign') == data_auth_sign($admin) ? $admin['admin_id'] : 0;
    }
}

/**
 * 检测会员是否登录
 * @return integer 0/管理员ID
 */
function is_user_login()
{
    $user = session('user_auth');
    if (empty($user)) {
        return 0;
    } else {
        return session('user_auth_sign') == data_auth_sign($user) ? $user['user_id'] : 0;
    }
}

/**
 * 数据签名认证
 * @param  array $data 被认证的数据
 * @return string       签名
 */
function data_auth_sign($data)
{
    // 数据类型检测
    if (!is_array($data)) {
        $data = (array)$data;
    }
    ksort($data); // 排序
    $code = http_build_query($data); // url编码并生成query字符串
    $sign = sha1($code); // 生成签名
    return $sign;
}

/**
 * 清除系统缓存
 * @param null $directory
 * @return bool
 */
function clear_cache($directory = null)
{
    $directory = empty($directory) ? RUNTIME_PATH . 'cache/' : $directory;
    if (is_dir($directory) == false) {
        return false;
    }
    $handle = opendir($directory);
    while (($file = readdir($handle)) !== false) {
        if ($file != "." && $file != "..") {
            is_dir($directory . '/' . $file) ?
                clear_cache($directory . '/' . $file) :
                unlink($directory . '/' . $file);
        }
    }
    if (readdir($handle) == false) {
        closedir($handle);
        rmdir($directory);
    }
}

/**
 * 返回LayUI-table数据格式
 * @param array $data  列表数据
 * @param string $count  总条数
 * @param array $totalRow  附加数据
 * @return array
 */
function tableData($data = [], $count = 0, $totalRow = []) {
    return ['code' => 0, 'msg' => '', 'totalRow' => $totalRow, 'count' => $count, 'data' => $data];
}

/**
 * [makeImageUrls 补全URL地址]
 * @param  string $html [内容]
 * @return string       补全后的HTML
 */
function makeImageUrls($html) {
    $domain = request()->domain();
    // 正则表达式匹配所有img标签中的src属性
    $pattern = '/<img[^>]*src=[\'"]?([^\'"]*)[\'"]?[^>]*>/i';
    // 使用preg_replace_callback进行替换
    return preg_replace_callback($pattern, function ($matches) use ($domain) {
        // 如果URL已经是完整的，则不处理
        if (parse_url($matches[1], PHP_URL_SCHEME)) {
            return $matches[0];
        }
        // 拼接完整的URL
        return str_replace($matches[1], $domain . '/' . ltrim($matches[1], '/'), $matches[0]);
    }, $html);
}


/**
 * [makeUrl 补全URL地址]
 * @param  string $url url
 * @return string      补全后的url
 */
function makeUrl($url) {
    if (empty($url)) return '';
    // 定义匹配http和https两种协议的正则表达式
    $pattern = '/^http(s)?:\\/\\/.+/';
    // 使用preg_match函数进行正则表达式匹配
    if (preg_match($pattern, $url)) {
        return $url;
    } else {
        return request()->domain() . $url;
    }
}

/**
 * [getAccessToken 获取微信 AccessToken]
 * @param  string $appid AppID
 * @param  string $appsecret AppSecret
 * @return string AccessToken
 */
function getAccessToken($appid, $appsecret){
    $tokenfile = RUNTIME_PATH . 'token.txt';
    if (file_exists($tokenfile)) {
        $filemtime = filemtime($tokenfile);
        $time = time() - $filemtime;
        if ($time > 7190) {
            $url = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=".$appid."&secret=".$appsecret;
            $ch = curl_init();
            curl_setopt($ch,CURLOPT_URL,$url);
            curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
            curl_setopt($ch,CURLOPT_SSL_VERIFYPEER,false);
            curl_setopt($ch,CURLOPT_SSL_VERIFYHOST,false);
            $data = curl_exec($ch);
            $http_code = curl_getinfo($ch,CURLINFO_HTTP_CODE);
            curl_close($ch);
            if ($http_code == 200) {
                $access_token = json_decode($data)->access_token;
                file_put_contents(RUNTIME_PATH . 'token.txt', $access_token);
                return $access_token;
            } else {
                return 'token 请求错误';
            }
        } else {
            return file_get_contents($tokenfile);
        }
    } else {
        echo "文件不存在";
    }
}

/**
 * [getMobile 获取小程序授权手机号码]
 * @param  string $token AccessToken
 * @param  string $code code
 * @return string 手机号码
 */
function getMobile($token, $code){
    $res = https_request('https://api.weixin.qq.com/wxa/business/getuserphonenumber?access_token=' . $token, json_encode(['code' => $code]));
    $res = json_decode($res, true);
    if (empty($res['errcode'])) {
        return $res['phone_info']['purePhoneNumber'];
    } else {
        return $res['errmsg'];
    }
}