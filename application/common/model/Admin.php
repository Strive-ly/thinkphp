<?php
/**
 * Created by PhpStorm.
 * Author: sitenv@aliyun.com
 * CreateTime: 2020/10/12 9:16 下午
 * Blog：www.myblogs.xyz
 */

namespace app\common\model;


class Admin extends Common
{
    public function initialize()
    {
        parent::initialize();
        $this->pk = 'admin_id';
        $this->name = 'admin';
        $this->table = config('database.prefix') . $this->name;
    }

}