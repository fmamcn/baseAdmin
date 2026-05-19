<?php

namespace app\admin\controller;

use app\common\controller\AdminBase;

class Ad extends AdminBase
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

        $list = model('ad')->order('sort_order asc,id desc')->page($page, $size)->select();
        $count = model('ad')->count();

        return tableData($list, $count);
    }

    public function add()
    {
        if ($this->request->isPost()) {
            if ($this->insert('ad', $this->request->param()) === true) {
                $this->success('添加成功', url('admin/ad/index'));
            } else {
                $this->error($this->errorMsg);
            }
        }
        return $this->fetch('save');
    }

    public function edit()
    {
        if ($this->request->isPost()) {
            if ($this->update('ad', $this->request->param(), input('_verify', true)) === true) {
                $this->success('修改成功', url('admin/ad/index'));
            } else {
                $this->error($this->errorMsg);
            }
        }
        return $this->fetch('save', ['data' => model('ad')->where('id', input('id'))->find()]);
    }

    public function del()
    {
        if ($this->request->isPost()) {
            if ($this->delete('ad', $this->request->param()) === true) {
                $this->success('删除成功');
            } else {
                $this->error($this->errorMsg);
            }
        }
    }
}
