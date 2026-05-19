<?php
namespace app\common\service;

use think\Cache;

/**
 * 微信服务类
 * 封装所有与微信服务端交互的逻辑
 */
class WechatService
{    
    private $appid;
    private $appsecret;

    public function __construct()
    {
        // 读取配置
        $this->appid = config('wechat.small_appid');
        $this->appsecret = config('wechat.small_appsecret');
    }

    /**
     * 1. 获取小程序openid（通过code）
     * @param string $code 小程序wx.login获取的code
     * @return array|bool
     */
    public function getOpenid($code)
    {
        if (empty($code)) {
            return ['code' => 0, 'msg' => 'code不能为空'];
        }

        $url = "https://api.weixin.qq.com/sns/jscode2session";
        $param = [
            'appid' => $this->appid,
            'secret' => $this->appsecret,
            'js_code' => $code,
            'grant_type' => 'authorization_code'
        ];

        $res = $this->sendHttpRequest($url, $param);
        $arr = json_decode($res, true);

        return $arr;
        
        // 返回成功：包含 openid、session_key
        // if (isset($arr['openid'])) {
        //     return $arr;
        // }
    }

    /**
     * 2. 获取 access_token（缓存 7000 秒）
     */
    public function getAccessToken()
    {
        $key = "wx_access_token";
        $token = Cache::get($key);
        
        if ($token) return $token;

        $url = "https://api.weixin.qq.com/cgi-bin/token";
        $param = [
            'appid' => $this->appid,
            'secret' => $this->appsecret,
            'grant_type' => 'client_credential'
        ];

        $res = $this->sendHttpRequest($url, $param);
        $arr = json_decode($res, true);

        if (isset($arr['access_token'])) {
            Cache::set($key, $arr['access_token'], 7000);
            return $arr['access_token'];
        }

        // 打印错误，方便你排查
        dump($arr);
        return false;
    }

    /**
     * 3. 发送小程序订阅消息（服务消息）
     * @param $openid
     * @param $templateId 模板ID
     * @param $data 模板数据
     * @return array
     */
    public function sendSubscribeMessage($openid, $templateId, $data, $page)
    {
        $accessToken = $this->getAccessToken();
        if (!$accessToken) {
            return ['code' => 0, 'msg' => '获取access_token失败'];
        }

        $url = "https://api.weixin.qq.com/cgi-bin/message/subscribe/send?access_token=" . $accessToken;

        $postData = [
            'touser' => $openid,
            'page' => $page,
            'template_id' => $templateId,
            'data' => $data
        ];

        $res = $this->sendHttpRequest($url, json_encode($postData, JSON_UNESCAPED_UNICODE));
        return json_decode($res, true);
    }

    /**
     * 通用 HTTP 请求方法（兼容GET/POST参数）
     * @param string $url 请求地址
     * @param array|string $data POST数据
     * @return bool|string
     */
    private function sendHttpRequest($url, $data)
    {
        $cl = curl_init();
        
        // HTTPS处理
        if (stripos($url, 'https://') !== FALSE) {
            curl_setopt($cl, CURLOPT_SSL_VERIFYPEER, FALSE);
            curl_setopt($cl, CURLOPT_SSL_VERIFYHOST, FALSE);
        }
        
        curl_setopt($cl, CURLOPT_URL, $url);
        curl_setopt($cl, CURLOPT_RETURNTRANSFER, 1);
        
        // POST参数
        if (!empty($data)) {
            curl_setopt($cl, CURLOPT_POST, true);
            curl_setopt($cl, CURLOPT_POSTFIELDS, $data);
        }
        
        $content = curl_exec($cl);
        $status = curl_getinfo($cl);
        curl_close($cl);

        if (isset($status['http_code']) && $status['http_code'] == 200) {
            return $content;
        } else {
            return FALSE;
        }
    }
}