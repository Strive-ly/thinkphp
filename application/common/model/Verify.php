<?php
/**
 * Created by PhpStorm.
 * Author: sitenv@aliyun.com
 * CreateTime: 2020/10/12 9:22 下午
 * Blog：www.myblogs.xyz
 */

namespace app\common\model;


class Verify extends Common
{
    public function initialize()
    {
        parent::initialize();
        $this->name = 'verify';
        $this->table = config('database.prefix') . $this->name;
    }

}