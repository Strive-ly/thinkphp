<?php
/**
 * Created by PhpStorm.
 * Author: sitenv@aliyun.com
 * CreateTime: 2020/10/12 8:34 下午
 * Blog：www.myblogs.xyz
 */

namespace app\common\model;


class Demo extends Common
{
    public function initialize()
    {
        parent::initialize();
        $this->pk = 'dome_id';
        $this->name = 'dome';
        $this->table = config('database.prefix') . $this->name;
    }

}