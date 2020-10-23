<?php
/**
 * Created by PhpStorm.
 * Author: sitenv@aliyun.com
 * CreateTime: 2020/10/12 6:50 下午
 * Blog：www.myblogs.xyz
 */

namespace app\common\model;


use think\Db;
use think\Model;
use think\Validate;

class Common extends Model
{
    // 指定AND查询条件
    public $where = '';
    // 字段截取
    public $fields = '';
    // 排序
    public $orders = '';
    // 指定OR查询条件
    public $whereOr = '';

    /**
     * 获取单条数据
     * @param string $where
     * @param string $field
     * @param string $order
     * @param string $whereOr
     * @return array|false|mixed|\PDOStatement|string|Model
     */
    public function getFind($where = '', $field = '', $order = '', $whereOr = '')
    {
        $wheres = empty($where) ? $this->where : $where;
        $fields = empty($field) ? $this->fields : $field;
        $orders = empty($order) ? $this->orders : $order;
        $whereOrs = empty($whereOr) ? $this->whereOr : $whereOr;
        if (empty($wheres) && empty($whereOrs)) return [];
        // 排除查询
        $except = false;
        if (is_array($fields)){
            $except = end($fields);
            $fields = reset($fields);
        }
        // 主键查询
        if (is_numeric($wheres)){
            $data = Db::table($this->table)->where($this->pk, $wheres)->whereOr($whereOrs)->field($fields, $except)->order($orders)->find();
        }else{
            $data = Db::table($this->table)->where($wheres)->whereOr($whereOrs)->field($fields, $except)->order($orders)->find();
        }
        if (empty($data)) return [];
        return $data;
    }

    /**
     * 获取单个字段数据
     * @param string $where
     * @param string $field
     * @param string $whereOr
     * @return mixed|string
     */
    public function getField($where = '', $field = '', $whereOr = '')
    {
        $wheres = empty($where) ? $this->where : $where;
        $fields = empty($field) ? $this->fields : $field;
        $whereOrs = empty($whereOr) ? $this->whereOr : $whereOr;
        if (empty($wheres) && empty($whereOrs)) return '';
        if (empty($fields)) return '';
        // 主键查询
        if (is_numeric($wheres)){
            $value = Db::table($this->table)->where($this->pk, $wheres)->value($fields);
        }else{
            $value = Db::table($this->table)->where($wheres)->value($fields);
        }
        return $value;
    }

    /**
     * 更新和修改操作
     * @param array $data
     * @param bool $auto_validate
     * @param bool $all
     * @param bool $getLastInsID
     * @return bool|string
     */
    public function operation($data = [] , $auto_validate = true, $all = false, $getLastInsID = false)
    {
        // $all 是否批量操作
        if ($auto_validate){
            if (is_array($auto_validate)){
                if (empty($auto_validate['rule'])){
                    $this->error = 'Validate fail';
                    return false;
                }
                $validate = Validate::make($auto_validate['rule'], $auto_validate['message']);
                // 是否批量验证
                if ($all){
                    foreach ($data as $key=>$val){
                        if (!$validate->check($val)){
                            $this->error = $validate->getError();
                            return false;
                        }
                    }
                }else{
                    if (!$validate->check($data)){
                        $this->error = $validate->getError();
                        return false;
                    }
                }
            }else{
                $class = '\\app\\common\\validate\\' . posUrl($this->name);
                if (class_exists($class)) {
                    $validate = new $class();
                    // 是否批量验证
                    if ($all){
                        foreach ($data as $key=>$val){
                            if (!$validate->check($val)){
                                $this->error = $validate->getError();
                                return false;
                            }
                        }
                    }else{
                        if (!$validate->check($data)){
                            $this->error = $validate->getError();
                            return false;
                        }
                    }
                }else{
                    $this->error = 'Validate fail';
                    return false;
                }
            }
        }
        if ($all){
            $rows = $this->saveAll($data);
        }else{
            $rows = $this->save($data);
        }
        if (empty($rows)){
            return false;
        }
        if ($getLastInsID == true && $all == false){
            return $this->getLastInsID();
        }
        return true;
    }

    /**
     * 状态操作设置
     * @param string $where
     * @param string $field
     * @param int $value
     * @param int $default
     * @return bool
     */
    public function status($where = '', $field = 'status', $value = 0 , $default = 1)
    {
        $wheres = empty($where) ? $this->where : $where;
        $fields = empty($field) ? $this->fields : $field;
        if (empty($wheres)) return false;
        // 批量修改
        if (is_array($wheres)){
            $keys = array_keys($wheres);
            if ($keys['0'] == 0){
                foreach ($wheres as $key=>$val){
                    $status = $this->getField($val, $fields);
                    $new_value = $status == $default ? $value : $default;
                    // 主键查询
                    if (is_numeric($val)){
                        $rows = Db::table($this->table)->where($this->pk, $val)->setField($fields, $new_value);
                    }else{
                        $rows = Db::table($this->table)->where($val)->setField($fields, $new_value);
                    }
                    if (empty($rows)){
                        return false;
                    }
                }
            }else{
                $status = $this->getField($wheres, $fields);
                $new_value = $status == $default ? $value : $default;
                $rows = Db::table($this->table)->where($wheres)->setField($fields, $new_value);
            }
        }elseif (is_numeric($wheres)){
            // 主键查询
            $status = $this->getField($wheres, $fields);
            $new_value = $status == $default ? $value : $default;
            $rows = Db::table($this->table)->where($this->pk, $wheres)->setField($fields, $new_value);
        }else{
            $status = $this->getField($wheres, $fields);
            $new_value = $status == $default ? $value : $default;
            $rows = Db::table($this->table)->where($wheres)->setField($fields, $new_value);
        }
        if (empty($rows)){
            return false;
        }
        return true;
    }

    /**
     * 删除操作
     * @param string $where
     * @return bool
     */
    public function remove($where = '')
    {
        $wheres = empty($where) ? $this->where : $where;
        if (empty($wheres)) return false;
        // 批量删除
        if (is_array($wheres)){
            $keys = array_keys($wheres);
            if ($keys['0'] == 0){
                foreach ($wheres as $key=>$val){
                    // 主键查询
                    if (is_numeric($val)){
                        $rows = Db::table($this->table)->where($this->pk, $val)->delete();
                    }else{
                        $rows = Db::table($this->table)->where($val)->delete();
                    }
                    if (empty($rows)){
                        return false;
                    }
                }
            }else{
                $rows = Db::table($this->table)->where($wheres)->delete();
            }
        }elseif (is_numeric($wheres)){
            // 主键查询
            $rows = Db::table($this->table)->where($this->pk, $wheres)->delete();
        }else{
            $rows = Db::table($this->table)->where($wheres)->delete();
        }
        if (empty($rows)){
            return false;
        }
        return true;
    }

    /**
     * 检测是否存在
     * @param string $where
     * @return bool
     */
    public function exists($where = '')
    {
        $wheres = empty($where) ? $this->where : $where;
        if (empty($wheres)) return false;
        // 主键查询
        if (is_numeric($wheres)){
            $rows = Db::table($this->table)->where($this->pk, $wheres)->count();
        }else{
            $rows = Db::table($this->table)->where($wheres)->count();
        }
        if (empty($rows)){
            return false;
        }
        return true;
    }

    /**
     * 获取列表数据
     * @param string $where
     * @param string $limit
     * @param string $order
     * @param string $field
     * @param string $whereOr
     */
    public function getList($where = '', $limit = '', $order = '', $field = '', $whereOr = '')
    {
        $wheres = empty($where) ? $this->where : $where;
        $fields = empty($field) ? $this->fields : $field;
        $orders = empty($order) ? $this->orders : $order;
        $whereOrs = empty($whereOr) ? $this->whereOr : $whereOr;
        // 排除查询
        $except = false;
        if (is_array($fields)){
            $except = end($fields);
            $fields = reset($fields);
        }
        $list = Db::table($this->table)->where($wheres)->whereOr($whereOrs)->order($orders)->field($fields, $except)->limit($limit)->select();
        return $list;
    }

    /**
     * 获取列表分页数据
     * @param string $where
     * @param int $limit
     * @param string $order
     * @param string $field
     * @param string $whereOr
     * @return array
     */
    public function getLists($where = '', $limit = 1, $order = '', $field = '', $whereOr = '')
    {
        $wheres = empty($where) ? $this->where : $where;
        $fields = empty($field) ? $this->fields : $field;
        $orders = empty($order) ? $this->orders : $order;
        $whereOrs = empty($whereOr) ? $this->whereOr : $whereOr;
        $count = $this->where($wheres)->count();
        $page = new \Page($count, $limit);
        $param = \think\facade\Request::param();
        if (!empty($param)){
            if (!empty($param['start_time'])){
                $param['start_time'] = str_replace('%2B', ' ', $param['start_time']);
            }
            if (!empty($param['end_time'])){
                $param['end_time'] = str_replace('%2B', ' ', $param['end_time']);
            }
            $page->parameter = $param;
        }
        $show = $page->show();
        // 排除查询
        $except = false;
        if (is_array($fields)){
            $except = end($fields);
            $fields = reset($fields);
        }
        $list = Db::table($this->table)->where($wheres)->whereOr($whereOrs)->order($orders)->field($fields, $except)->limit($page->firstRow . ',' . $page->listRows)->select();
        return [
            'count'=>$count,
            'page'=>$show,
            'list'=>$list
        ];
    }

    // 启动事务
    public function DbStartTrans()
    {
        Db::startTrans();
    }

    // 提交事务
    public function DbCommit()
    {
        Db::commit();
    }

    // 数据回滚
    public function DbRollback()
    {
        Db::rollback();
    }

}