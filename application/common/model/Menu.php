<?php
/**
 * Created by PhpStorm.
 * Author: sitenv@aliyun.com
 * CreateTime: 2020/10/12 9:17 下午
 * Blog：www.myblogs.xyz
 */

namespace app\common\model;


class Menu extends Common
{
    public $orders = 'sort asc,menu_id asc';

    public function initialize()
    {
        parent::initialize();
        $this->pk = 'menu_id';
        $this->name = 'menu';
        $this->table = config('database.prefix') . $this->name;
    }

    public function getParents($menu)
    {
        $menus = '';
        if ($menu > 0){
            $parents_id = $this->parents($menu, 1);
            $parents_ids = empty($parents_id)? '' : implode(',', $parents_id) . ',';
            $menus = $parents_ids . $menu;
        }
        return $menus;
    }

    /**
     * 查找parent_id
     * @param $menu
     * @param int $new
     * @return array
     */
    public function parents($menu, $new = 0)
    {
        static $list = array();
        if ($new == 1){
            $list = array();
        }
        if ($menu > 0){
            $parent_id = $this->where([$this->pk=>$menu])->value('parent_id');
            if ($parent_id > 0){
                $list[] = $parent_id;
                $this->parents($parent_id);
            }
        }
        krsort($list);
        return $list;
    }

    public function getMenuNameArray($menu = '')
    {
        if (empty($menu)) return [];
        $menu = explode(',', $menu);
        $menu_data = [];
        if (is_array($menu)){
            foreach ($menu as $key=>$val){
                $menu_data[] = $this->getFind($val, 'menu_id,menu_name');
            }
        }
        return $menu_data;
    }

    public function getMenuNameStr($menu = '')
    {
        if (empty($menu)) return [];
        $menu = explode(',', $menu);
        $menu_name = '';
        if (is_array($menu)){
            foreach ($menu as $key=>$val){
                $menu_name .= $this->getField($val, 'name');
                $menu_name .=  $key < 3 ? '、' : '';
            }
        }
        return $menu_name;
    }

}