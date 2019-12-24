<?php
namespace app\index\model;

use think\Model;

class User extends Model
{
    protected $pk = 'user_id';
    public function getUserSexAttr($value)
    {
        $status = [0=>'女',1=>'男',2=>'未知'];
        return $status[$value];
    }
    public function getUserStatusAttr($value)
    {
        $status = [-1=>'账户注销',0=>'未激活',1=>'正常',2=>'忘记密码',3=>'用户冻结'];
        return $status[$value];
    }
    public function getUserAuthorityAttr($value)
    {
        $status = [0=>'普通用户',1=>'管理员'];
        return $status[$value];
    }
    public function getUserAccountTypeAttr($value)
    {
        $status = [0=>'QQ注册',1=>'网站注册'];
        return $status[$value];
    }
    public function getUserOnlineAttr($value)
    {
        $status = [0=>'未上线',1=>'pc在线',2=>'安卓在线',3=>'苹果在线' ,4=>"其他在线"];
        return $status[$value];
    }
}