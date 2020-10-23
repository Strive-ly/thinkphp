<?php
/**
 * Created by PhpStorm.
 * Author: sitenv@aliyun.com
 * CreateTime: 2020/10/12 9:17 下午
 * Blog：www.myblogs.xyz
 */

namespace app\common\model;


class RequestLog extends Common
{
    public function initialize()
    {
        parent::initialize();
        $this->pk = 'log_id';
        $this->name = 'request_log';
        $this->table = config('database.prefix') . $this->name;
    }

}