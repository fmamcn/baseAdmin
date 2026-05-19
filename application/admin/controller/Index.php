<?php

namespace app\admin\controller;

use app\common\controller\AdminBase;

class Index extends AdminBase
{
    protected $noLogin = [
        'login',
        'captcha'
    ];
    protected $noAuth = [
        'index',
        'logout',
        'main',
        'nav'
    ];

    protected function _initialize()
    {
        parent::_initialize();
    }

    public function index()
    {
        return $this->fetch('index');
    }

    public function main()
    {
        // 服务器信息
        $server = [
            'os'                  => PHP_OS, // 服务器操作系统
            'sapi'                => PHP_SAPI, // 服务器软件
            'version'             => PHP_VERSION, // PHP版本
            'mysql'               => db()->query('select VERSION() as version'), // mysql 版本
            'root'                => $_SERVER['DOCUMENT_ROOT'], // 当前运行脚本所在的文档根目录
            'max_execution_time'  => ini_get('max_execution_time') . 's', // 最大执行时间
            'upload_max_filesize' => ini_get('upload_max_filesize'), // 文件上传限制
            'memory_limit'        => ini_get('memory_limit') // 允许内存大小
        ];
        return $this->fetch('main', ['server' => $server]);
    }

    public function login()
    {
        is_admin_login() && $this->redirect('admin/index/index'); // 登录直接跳转
        if ($this->request->isPost()) {
            $param  = $this->request->param();
            $result = $this->validate($param, 'login');
            if ($result !== true) {
                $this->error($result);
            }
            $admin = model('admin')->where('username', $param['username'])->find();
            empty($admin) && $this->error('账号不存在');
            $admin['status'] != 1 && $this->error('账号已禁用');
            if ($admin && password_verify($param['password'], $admin['password'])) {
                $admin['status'] != 1 && $this->error('账号已禁用');
                // 保存状态
                $auth = [
                    'admin_id' => $admin['id'],
                    'username' => $admin['username'],
                ];
                session('admin_auth', $auth);
                session('admin_auth_sign', data_auth_sign($auth));
                // 更新信息
                model('admin')->where('id', $admin['id'])->setInc('login_count');
                insert_admin_log('登录了后台系统');
                $this->success('登录成功', url('admin/index/index'));
            } else {
                $this->error('密码错误');
            }
        }
        return $this->fetch('login');
    }

    public function captcha()
    {
        $config = [
            // 验证码字符集合
            'codeSet'  => '23456789ABCDEFGHJKLMNPQRTUVWXY',
            // 验证码字体大小(px)
            'fontSize' => 16,
            // 是否画混淆曲线
            'useCurve' => false,
            // 验证码图片高度
            'imageH'   => 42,
            // 验证码图片宽度
            'imageW'   => 135,
            // 验证码位数
            'length'   => 4,
            // 验证成功后是否重置
            'reset'    => true,
            // 字体
            'fontttf'  => '4.ttf'
        ];
        return captcha('', $config);
    }

    // 修改密码
    public function editPassword()
    {
        if ($this->request->isPost()) {
            $param = $this->request->param();

            $admin = model('admin')->where('id', session('admin_auth.admin_id'))->find();
            empty($param['password']) && $this->error('请输入旧密码');
            !password_verify($param['password'], $admin['password']) && $this->error('密码错误');

            if (isset($param['username']) && $param['username'] != session('admin_auth.username')) {
                $user = ['id' => session('admin_auth.admin_id'), 'username' => $param['username']];
                model('system')->where('name', 'administrator')->update(['value' => $param['username']]);
                if ($this->update('admin', $user, false) === true) {
                    clear_cache();
                    session('admin_auth', null);
                    session('admin_auth_sign', null);
                    $this->success('修改成功', url('admin/index/index'));
                } else {
                    $this->error($this->errorMsg);
                }
            }

            if (!empty($param['new_password']) && !empty($param['rep_password'])) {
                !check_password($param['new_password'], 6, 16) && $this->error('请输入6-16位的密码');
                $param['new_password'] != $param['rep_password'] && $this->error('两次密码不一致');
                $admin = model('admin')->where('id', session('admin_auth.admin_id'))->find();
                $data = ['id' => session('admin_auth.admin_id'), 'password' => $param['new_password']];
                if ($this->update('admin', $data, false) === true) {
                    $this->success('更新成功', url('admin/index/index'));
                } else {
                    $this->error($this->errorMsg);
                }
            }
        }
        return $this->fetch('editPassword');
    }

    // 退出登录
    public function logout()
    {
        session('admin_auth', null);
        session('admin_auth_sign', null);
        $this->redirect('admin/index/login');
    }

    // 清除缓存
    public function clear()
    {
        clear_cache();
        $this->success('清除成功');
    }

    public function nav()
    {
        $navPid = model('authRule')->where('id', input('id'))->value('pid');
        $nav = model('authRule')->where(['pid' => 0, 'status' => 1])->order('sort_order asc')->column('id');
        
        $sort = '';
        foreach ($nav as $key => $value) {
            if ($navPid == $value) {
                $sort = $key;
            }
        }
        return $sort;
    }
}
