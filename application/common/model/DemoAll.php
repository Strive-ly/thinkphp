<?php
/**
 * Created by PhpStorm.
 * Author: sitenv@aliyun.com
 * CreateTime: 2020/10/12 8:34 下午
 * Blog：www.myblogs.xyz
 */

namespace app\common\model;


class DemoAll extends Common
{
    public function initialize()
    {
        parent::initialize();
        $this->pk = 'all_id';
        $this->name = 'dome_all';
        $this->table = config('database.prefix') . $this->name;
    }

    public function domeFind($where = '', $field = '', $whereOr = '')
    {
        return $this->getFind($where, $field, $whereOr);
    }

    public function domeField($where = '', $field = '', $whereOr = '')
    {
        return $this->getField($where, $field, $whereOr);
    }

    public function domeOperation($data = '', $validate = true, $all = false, $getLastInsID = false)
    {
        return $this->operation($data, $validate, $all, $getLastInsID);
    }

    public function domeStatus($where = '', $field = 'status', $value = 0 , $default = 1)
    {
        return $this->status($where, $field, $value, $default);
    }

    public function domeRemove($where = '')
    {
        return $this->remove($where);
    }

    public function domeExists($where = '')
    {
        return $this->exists($where);
    }

    public function domeList($where = '', $limit = '', $order = '', $field = '', $whereOr = '')
    {
        return $this->getList($where, $limit, $order, $field, $whereOr);
    }

    public function domeLists($where = '', $limit = 1, $order = '', $field = '', $whereOr = '')
    {
        $data = $this->getLists($where, $limit, $order, $field, $whereOr);
        $list = $data['list'];
        if (is_array($list)){
            foreach ($list as $key=>$val){
                if (!empty($val['create_time'])) {
                    $list[$key]['create_time'] = date('Y-m-d H:i:s',$val['create_time']);
                }
                if (!empty($val['update_time'])) {
                    $list[$key]['update_time'] = date('Y-m-d H:i:s',$val['update_time']);
                }
            }
        }
        $data['list'] = $list;
        return $data;
    }

}