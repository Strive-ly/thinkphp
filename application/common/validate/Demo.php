<?php
/**
 * Created by PhpStorm.
 * Author: sitenv@aliyun.com
 * CreateTime: 2020/10/12 8:32 下午
 * Blog：www.myblogs.xyz
 */

namespace app\common\validate;


class Demo extends Common
{
    protected $rule = [
        'mobile'=>'require|isMobile:',
        'password'=>'require|length:6,18|alphaDash|confirm',
    ];

    protected $message = [
        'mobile.require'=>'手机号不能为空',
        'number.isMobile:'=>'手机号输入有误',
        'password.require'=>'密码不能为空',
        'password.length'=>'密码长度为6-18位',
        'password.alphaDash'=>'密码为字母和数字，下划线_及破折号-',
        'password.confirm'=>'两次密码不一致！'
    ];

    protected function isMobile($value)
    {
        if(preg_match('/^[1]+[0-9]+\d{9}$/', $value))
            return true;
        return false;
    }

}