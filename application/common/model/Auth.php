<?php
/**
 * Created by PhpStorm.
 * Author: sitenv@aliyun.com
 * CreateTime: 2020/10/15 7:26 下午
 * Blog：www.myblogs.xyz
 */

namespace app\common\model;


class Auth extends Common
{
    public function __construct($table, $pk = '')
    {
        parent::initialize();
        if ($pk !== false){
            $table_pk = empty($pk) ? $table . '_id' : $pk;
            $this->pk = $table_pk;
        }
        $this->name = $table;
        $this->table = config('database.prefix') . $this->name;
    }
}