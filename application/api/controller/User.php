<?php

namespace app\api\controller;

use app\common\controller\ApiBase;
use app\common\service\WechatService;
use app\common\service\JwtAuth;

class User extends ApiBase
{
    protected function _initialize()
    {
        parent::_initialize();
    }

    public function login()
    {        
        // 1. 验证用户（伪代码）
        $user = model('User')->where('id', 1)->find();

        $accessToken = JwtAuth::createToken($user['id'], 'access');
        $refreshToken = JwtAuth::createToken($user['id'], 'refresh');
        
        return $this->sendSuccess([
            'access_token' => $accessToken,
            'refresh_token' => $refreshToken
        ]);
    }

    // 刷新 refresh_token
    public function refresh()
    {
        $refreshToken = $this->request->post('refresh_token');
        if (!$refreshToken) {
            return $this->sendError('缺少刷新Token', 401);
        }
        // 校验 Refresh Token 是否有效
        $result = JwtAuth::verifyToken($refreshToken, 'refresh');

        if (is_array($result)) {
            return $this->sendError($result['msg'], 401);
        }
        // Refresh Token 有效，签发新的 Access Token
        $newAccessToken = JwtAuth::createToken($result->uid, 'access');

        return $this->sendSuccess(['access_token' => $newAccessToken]);
    }
}
