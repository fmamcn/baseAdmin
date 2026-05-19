<?php

namespace app\admin\controller;

use app\common\controller\AdminBase;

class User extends AdminBase
{
    protected $noAuth = [
        'dataList'
    ];

    protected function _initialize()
    {
        parent::_initialize();
    }

    public function index()
    {
        return $this->fetch('index');
    }

    public function dataList()
    {
        $size = input('limit', 20);
        $page = input('page', 1);

        $where = [];

        $keyword = input('keyword');
        if ($keyword) {
            $where['nickname|mobile'] = ['like', '%' . trim($keyword) . '%'];
        }

        $list = model('user')->where($where)->order('id desc')->page($page, $size)->select();
        $count = model('user')->where($where)->count();

        return tableData($list, $count);
    }

    public function edit()
    {
        if ($this->request->isPost()) {
            $param = $this->request->param();
            if ($this->update('user', $param, input('_verify', true)) === true) {
                $this->success('修改成功', url('admin/user/index'));
            } else {
                $this->error($this->errorMsg);
            }
        }
        return $this->fetch('save', ['data' => model('user')->where('id', input('id'))->find()]);
    }
}
