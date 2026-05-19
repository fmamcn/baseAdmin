<?php

namespace app\admin\validate;

use think\Validate;

class Ad extends Validate
{
    protected $rule = [
        'image'    => 'require',
        'title'     => 'require',
    ];

    protected $message = [
        'image.require'    => '请上传广告图片',
        'title.require'     => '名称不能为空',
    ];
}
