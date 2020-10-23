<?php
/**
 * Created by PhpStorm.
 * Author: sitenv@aliyun.com
 * CreateTime: 2020/10/12 8:41 下午
 * Blog：www.myblogs.xyz
 */

namespace app\api\controller;

/**
 * API接口案例
 * Class Dome
 * @package app\api\controller
 */
class Demo extends Common
{
    /**
     * 案例1
     */
    public function domeOne()
    {
        // 设定必传参数
        $this->require = ['mobile', 'password'];

        // 验证参数
        $this->jsonVerify();

        // 业务处理
        /*......*/

        // 返回结果
        $this->jsonError('requestFailed');

        // 返回结果
        $this->jsonSuccess('requestSuccess');
    }

    /**
     * 案例2
     */
    public function domeTwo()
    {
        // 设置必传参数
        $this->require = ['mobile', 'password'];

        // 设置非必传参数
        $this->selectable = ['verify_code'];

        // 验证参数
        $this->jsonVerify();

        // 业务处理
        /*......*/

        // 返回结果
        $this->jsonError('requestFailed');

        // 返回结果
        $this->jsonSuccess('requestSuccess');
    }

    /**
     * 案例3
     */
    public function domeTwoOne()
    {
        // 设置全部参数非必传
        $this->selectable = ['mobile', 'password', 'verify_code', 'reserved'];

        // 验证参数
        $this->jsonVerify();

        // 业务处理
        /*......*/

        // 返回结果
        $this->jsonError('requestFailed');

        // 返回结果
        $this->jsonSuccess('requestSuccess');
    }

    /**
     * 高级案例1：保存数据
     */
    public function highRegisterDome()
    {
        // 设置全部参数非必传
        $this->require = ['mobile', 'password', 'verify_code', 'reserved'];

        // 验证参数
        $this->jsonVerify();

        // 业务处理
        /*......*/
        // 获取模型
        $dome_model = new \app\common\model\Demo();
        // 保存更新
        if (!$dome_model->isUpdate(true)->insert($this->data)){
            $this->jsonError($dome_model->getError());
        }

        // 返回结果
        $this->jsonError('requestFailed');

        // 返回结果
        $this->jsonSuccess('requestSuccess');
    }

    /**
     * 高级案例2：获取数据
     */
    public function highFindDome()
    {
        // 设置全部参数非必传
        $this->require = ['mobile'];

        // 验证参数
        $this->jsonVerify();

        // 业务处理
        /*......*/
        // 获取模型
        $dome_model = new \app\common\model\Demo();
        // 获取数据（单条）
        $result = $dome_model->getFind($this->data);
        // 获取数据（多条）
        $result = $dome_model->getList($this->data);
        // 获取数据（多条分页）
        $result = $dome_model->getList($this->data, 15);

        // 返回结果
        $this->jsonError('requestFailed');

        // 返回结果
        $this->jsonSuccess($result);
        // $this->jsonSuccess($result, '请求成功');
    }

    /**
     * 高级案例3：更新数据
     */
    public function highUpdateDome()
    {
        // 设置全部参数非必传
        $this->require = ['mobile', 'password', 'verify_code', 'reserved'];

        // 验证参数
        $this->jsonVerify();

        // 业务处理
        /*......*/
        // 获取模型
        $dome_model = new \app\common\model\DemoAll();
        // 保存更新
        if (!$dome_model->domeOperation($this->data)){
            $this->jsonError($dome_model->getError());
        }

        // 返回结果
        $this->jsonError('requestFailed');

        // 返回结果
        $this->jsonSuccess('requestSuccess');
    }

    /**
     * 高级案例4：获取数据
     */
    public function highFindsDome()
    {
        // 设置全部参数非必传
        $this->require = ['mobile'];

        // 验证参数
        $this->jsonVerify();

        // 业务处理
        /*......*/
        // 获取模型
        $dome_model = new \app\common\model\Demo();

        // 查询条件参数设置方式（1）参数较多时推荐使用，或者根据业务需求和在模型中设置默认值
        $dome_model->where = '';
        $dome_model->fields = '';
        $dome_model->orders = '';
        $dome_model->whereOr = '';
        // 获取数据（单条）
        $result = $dome_model->getFind($this->data);
        // 获取数据（多条）
        $result = $dome_model->getList($this->data);
        // 获取数据（多条分页）
        $result = $dome_model->getList($this->data, 15);

        // 查询条件参数设置方式（2）
        $dome_where = $this->data;
        $dome_fields = '';
        $dome_orders = '';
        $dome_whereOr = '';
        // 获取数据（单条）
        $result = $dome_model->getFind($dome_where, $dome_fields);
        // 获取数据（多条）
        $result = $dome_model->getList($dome_where, 15, $dome_orders, $dome_fields, $dome_whereOr);
        // 获取数据（多条分页）
        $result = $dome_model->getList($dome_where, 15, $dome_orders, $dome_fields, $dome_whereOr);

        // 返回结果
        $this->jsonError('requestFailed');

        // 返回结果
        $this->jsonSuccess($result);
        // $this->jsonSuccess($result, '请求成功');
    }

    /**
     * 其它项自行查看，如批量删除
     * $this->domeRemove($where);
     */

}