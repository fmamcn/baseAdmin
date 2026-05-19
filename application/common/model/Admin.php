<?php

namespace app\common\model;

use think\Model;

class Admin extends Model
{
    protected $autoWriteTimestamp = true;

    public function setLastLoginIpAttr()
    {
        return request()->ip();
    }

    public function setLastLoginTimeAttr()
    {
        return time();
    }
    
    public function setPasswordAttr($value)
    {
        return password_hash($value, PASSWORD_BCRYPT);
    }

    public function authGroupAccess()
    {
        return $this->belongsTo('authGroupAccess', 'id', 'uid')->bind('group_id');
    }

    public function authGroup()
    {
        return $this->belongsTo('authGroup', 'group_id', 'id')->bind('name');
    }

    public function getCreateTimeAttr($value)
    {
        return date('Y-m-d H:i:s', $value);
    }

    public function getLastLoginTimeAttr($value)
    {
        return date('Y-m-d H:i:s', $value);
    }
}