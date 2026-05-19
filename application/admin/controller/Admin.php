<?php

namespace app\admin\controller;

use app\common\controller\AdminBase;

class Admin extends AdminBase
{
    protected $noAuth = [
        'dataList',
        'dataList_log'
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

        $list = model('admin')->with('authGroupAccess,authGroup')->where('username', 'neq', 'admin')->page($page, $size)->select();
        $count = model('admin')->where('username', 'neq', 'admin')->count();

        return tableData($list, $count);
    }

    public function add()
    {
        if ($this->request->isPost()) {
            $param = $this->request->param();
            empty($param['password']) && $this->error('密码不能为空');
            if ($this->insert('admin', $param) === true) {
                model('authGroupAccess')->save(['uid' => $this->insertId, 'group_id' => $param['group_id']]);
                $this->success('添加成功');
            } else {
                $this->error($this->errorMsg);
            }
        }
        return $this->fetch('save', ['authGroup' => model('authGroup')->where('status', 1)->select()]);
    }

    public function edit()
    {
        if ($this->request->isPost()) {
            $param  = $this->request->param();
            $verify = input('_verify', true);
            if (empty($param['password'])) {
                unset($param['password']);
            }
            if ($this->update('admin', $param, $verify) === true) {
                $verify && model('authGroupAccess')->save(['group_id' => $param['group_id']], ['uid' => $param['id']]);
                $this->success('修改成功');
            } else {
                $this->error($this->errorMsg);
            }
        }
        return $this->fetch('save', [
            'data'      => model('admin')->with('authGroupAccess')->where('id', input('id'))->find(),
            'authGroup' => model('authGroup')->where('status', 1)->select(),
        ]);
    }

    public function del()
    {
        if ($this->request->isPost()) {
            $param = $this->request->param();
            if ($this->delete('admin', $param) === true) {
                model('authGroupAccess')->where('uid', $param['id'])->delete();
                $this->success('删除成功');
            } else {
                $this->error($this->errorMsg);
            }
        }
    }

    public function log()
    {
        return $this->fetch('log');
    }

    public function dataList_log()
    {
        $size = input('limit', 20);
        $page = input('page', 1);

        $list = model('adminLog')->order('create_time desc')->page($page, $size)->select();
        $count = model('adminLog')->count();

        return tableData($list, $count);
    }

    public function truncate()
    {
        if ($this->request->isPost()) {
            db()->query('TRUNCATE ' . config('database.prefix') . 'admin_log');
            $this->success('操作成功');
        }
    }
}
