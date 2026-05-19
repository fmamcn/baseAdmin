<?php

namespace app\admin\controller;

use app\common\controller\AdminBase;

class Auth extends AdminBase
{
    protected $noAuth = [
        'dataList'
    ];

    protected function _initialize()
    {
        parent::_initialize();
        $authRule = collection(model('authRule')->where(['status' => 1])->order('sort_order asc')->select())->toArray();
        $authRule = list_to_level($authRule);
        foreach ($authRule as &$value){
            $segment = empty($value['level'] - 1) ? '' : ' ';
            $value['name'] = str_repeat('······', $value['level'] - 1) . $segment . $value['name'];
        }
        $this->assign('authRule', $authRule);
    }

    public function group()
    {
        return $this->fetch('group');
    }

    public function addGroup()
    {
        if ($this->request->isPost()) {
            if ($this->insert('authGroup', $this->request->param()) === true) {
                $this->success('添加成功', url('admin/auth/group'));
            } else {
                $this->error($this->errorMsg);
            }
        }
        $authRule = collection(model('authRule')->where(['status' => 1])->order('sort_order asc')->select())->toArray();
        foreach ($authRule as $k => $v) {
            // $authRule[$k]['open'] = true;
        }
        return $this->fetch('saveGroup', ['authRule' => json_encode(list_to_tree($authRule))]);
    }

    public function editGroup()
    {
        if ($this->request->isPost()) {
            if ($this->update('authGroup', $this->request->param(), input('_verify', true)) === true) {
                $this->success('修改成功', url('admin/auth/group'));
            } else {
                $this->error($this->errorMsg);
            }
        }
        $data     = model('authGroup')->where('id', input('id'))->find();
        $authRule = collection(model('authRule')->where(['status' => 1])->order('sort_order asc')->select())->toArray();
        foreach ($authRule as $k => $v) {
            // $authRule[$k]['open'] = true;
            $authRule[$k]['checked'] = in_array($v['id'], explode(',', $data['rules']));
        }
        return $this->fetch('saveGroup', ['data' => $data, 'authRule' => json_encode(list_to_tree($authRule))]);
    }

    public function delGroup()
    {
        if ($this->request->isPost()) {
            if ($this->delete('authGroup', $this->request->param()) === true) {
                $this->success('删除成功');
            } else {
                $this->error($this->errorMsg);
            }
        }
    }

    public function dataList()
    {
        $size = input('limit', 20);
        $page = input('page', 1);

        $list = model('authGroup')->page($page, $size)->select();
        $count = model('authGroup')->count();

        return tableData($list, $count);
    }

    public function rule()
    {
        $authRule = collection(model('authRule')->where(['status' => 1])->order('sort_order asc')->select())->toArray();
        $authRule = list_to_level($authRule);

        foreach ($authRule as &$value){
            $segment = empty($value['level'] - 1) ? '' : ' ';
            $value['name'] = str_repeat('······', $value['level'] - 1) . $segment . $value['name'];
        }
        return $this->fetch('rule', ['authRule' => $authRule]);
    }

    public function addRule()
    {
        if ($this->request->isPost()) {
            if ($this->insert('authRule', $this->request->param()) === true) {
                $this->success('添加成功', url('admin/auth/rule'));
            } else {
                $this->error($this->errorMsg);
            }
        }
        return $this->fetch('saveRule');
    }

    public function editRule()
    {
        if ($this->request->isPost()) {
            if ($this->update('authRule', $this->request->param(), input('_verify', 1)) === true) {
                $this->success('修改成功', url('admin/auth/rule'));
            } else {
                $this->error($this->errorMsg);
            }
        }
        return $this->fetch('saveRule', ['data' => model('authRule')->where('id', input('id'))->find()]);
    }

    public function delRule()
    {
        if ($this->request->isPost()) {
            model('authRule')->where('pid', input('id'))->count() && $this->error('请先删除子节点');
            if ($this->delete('authRule', $this->request->param()) === true) {
                $this->success('删除成功');
            } else {
                $this->error($this->errorMsg);
            }
        }
    }
}
