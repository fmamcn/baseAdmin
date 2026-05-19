<?php

namespace app\common\controller;

use think\Request;
use think\Response;
use think\response\Redirect;
use app\common\service\JwtAuth;
use think\exception\HttpResponseException;

class ApiBase extends Base
{
    protected $userId;

    // 定义不需要鉴权的白名单（格式：控制器名/方法名，统一小写）
    protected $noNeedLogin = [
        'user/login',          // 登录接口
        'user/refresh'    // 刷新Token接口
    ];

    protected function _initialize()
    {
        parent::_initialize();

        $config = cache('db_config_data');
        if (!$config) {
            $config = [];
            foreach (model('config')->select() as $v) {
                $config[$v['group']][$v['name']] = $v['value'];
            }
            cache('db_config_data', $config);
        }
        config($config);

        // 1. 获取当前访问路径并转小写，用于白名单判断
        $controller = strtolower($this->request->controller());
        $action = strtolower($this->request->action());
        $path = $controller . '/' . $action;

        // 2. 白名单直接放行
        if (in_array($path, $this->noNeedLogin)) {
            return;
        }

        $authHeader = $this->request->header('Authorization');
        if (!$authHeader) {
            throw new HttpResponseException($this->sendError('缺少认证Token', 401));
        }

        // 【关键】去掉 "Bearer " 前缀，提取纯 Token
        $token = str_replace('Bearer ', '', $authHeader);
        // 顺便过滤掉可能存在的多余空格
        $token = trim($token);
        
        if (!$token) {
            throw new HttpResponseException($this->sendError('缺少Token', 401));
        }
        $result = JwtAuth::verifyToken($token, 'access');
        if (is_array($result)) {
            if ($result['status'] === 'expired') {
                // 返回 401，触发前端的无感刷新逻辑
                throw new HttpResponseException($this->sendError($result['msg'], 401));
            } else {
                // 其他错误（签名不对、格式错误等）
                throw new HttpResponseException($this->sendError($result['msg'], 401));
            }
        }        
        $this->userId = $result->uid;
    }

    /**
     * 成功响应
     * @param array $data
     * @param string $msg
     * @param int $code
     * @return Response|\think\response\Json|\think\response\Jsonp|Redirect|\think\response\Xml
     */
    protected function sendSuccess($data = null, $msg = 'success', $code = 200)
    {
        return Response::create([
            'code' => $code,
            'msg'  => $msg,
            'data' => $data
        ], 'json');
    }


    /**
     * 失败响应
     * @param string $msg
     * @param int $code
     * @param array $data
     * @return Response|\think\response\Json|\think\response\Jsonp|\think\response\Xml
     */
    protected function sendError($msg = 'error', $code = 400, $data = null)
    {
        return Response::create([
            'code' => $code,
            'msg'  => $msg,
            'data' => $data
        ], 'json');
    }
}
