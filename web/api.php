<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2018 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------
// 

// 定义应用目录
define('APP_PATH', __DIR__ . '/../application/');
// 覆盖系统常量
define('RUNTIME_PATH', __DIR__ . '/../data/runtime/');
define('EXTEND_PATH', __DIR__ . '/../core/extend/');
define('VENDOR_PATH', __DIR__ . '/../core/vendor/');
// 绑定后台
define('BIND_MODULE', 'api');
// 加载框架引导文件
require __DIR__ . '/../core/thinkphp/base.php';
// 关闭模块的路由
\think\App::route(false);
// 执行应用
\think\App::run()->send();