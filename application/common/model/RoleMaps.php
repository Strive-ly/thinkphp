<?php
/**
 * Created by PhpStorm.
 * Author: sitenv@aliyun.com
 * CreateTime: 2020/10/12 9:19 下午
 * Blog：www.myblogs.xyz
 */

namespace app\common\model;


class RoleMaps extends Common
{
    public function initialize()
    {
        parent::initialize();
        $this->name = 'role_maps';
        $this->table = config('database.prefix') . $this->name;
    }

    public function getMenuIdsByRoleId($role_id = 0)
    {
        $role_id = (int)$role_id;
        $data = $this->getList(['role_id'=>$role_id]);
        $return = [];
        foreach($data as $val){
            $return[$val['menu_id']] = $val['menu_id'];
        }
        return $return;
    }

}