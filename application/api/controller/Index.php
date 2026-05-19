<?php

namespace app\api\controller;

use app\common\controller\ApiBase;

class Index extends ApiBase
{
    protected function _initialize()
    {
        parent::_initialize();
    }

    public function index()
    {
        $userId = $this->userId;

        return $this->sendSuccess('用户ID：' . $userId);
    }
}
