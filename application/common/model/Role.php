<?php
/**
 * Created by PhpStorm.
 * Author: sitenv@aliyun.com
 * CreateTime: 2020/10/12 9:18 下午
 * Blog：www.myblogs.xyz
 */

namespace app\common\model;


class Role extends Common
{
    public function initialize()
    {
        parent::initialize();
        $this->pk = 'role_id';
        $this->name = 'role';
        $this->table = config('database.prefix') . $this->name;
    }

}