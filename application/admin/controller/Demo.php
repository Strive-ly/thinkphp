<?php
/**
 * Created by PhpStorm.
 * Author: sitenv@aliyun.com
 * CreateTime: 2020/10/12 9:23 下午
 * Blog：www.myblogs.xyz
 */

namespace app\admin\controller;


class Demo extends Common
{
    /**
     * 批量替换数据步骤
     * 1。替换方法。Demo()
     * 2。替换变量。$demo_model
     * 3。替换方法前缀。->demo
     * 4。替换对应主键。demo_
     * 5。修改对应模板。save_demo
     * 6。修改对应跳转。Demo/
     */

    // 列表数据
    public function index()
    {
        $this->demoList();
        return $this->fetch();
    }

    // 选择类型列表数据
    public function selectDemo()
    {
        $this->demoList(1); // 获取不同数据
        return $this->fetch();
    }

    // 列表数据(具体操作根据实际情况)
    protected function demoList($state = 0)
    {
        $param = input('param.');
        $list_where = [];
        $keywords = empty($param['keywords']) ? '' : $param['keywords'];
        if (!empty($keywords)){
            $list_where['mobile'] = $keywords;
        }
        // 处理下一页数据跳转时间参数地址自动加+号 str_replace
        $start_time = empty($param['start_time']) ? '' : str_replace('+', ' ',$param['start_time']);
        $end_time = empty($param['end_time']) ? '' : str_replace('+', ' ', $param['end_time']);
        // 时间查询
        if (!empty($start_time)){
            if (strtotime($start_time)){
                $beg = strtotime($start_time);
                if (!empty($end_time) && strtotime($end_time)){
                    $end = strtotime($end_time);
                }else{
                    $date = date('Y-m-d', NEW_TIME) . ' 23:59:59';
                    $end = strtotime($date);
                }
                $list_where['create_time'] = [['egt',$beg],['elt',$end]];
            }
        }
        // 不同界面数据操作
        if ($state == 1){
            $list_where['state'] = 1;
        }
        $demo_model = new \app\common\model\Demo();
        $data = $demo_model->getListS($list_where, 15);

        $this->assign([
            'keywords'=>$keywords,
            'start_time'=>$start_time,
            'end_time'=>$end_time,
            'count'=>$data['count'],
            'list'=>$data['list'],
            'page'=>$data['page']
        ]);

    }

    // 增加
    public function insertDemo()
    {
        $this->saveDemo();
        return $this->fetch('save_demo');
    }

    // 删除
    public function updateDemo()
    {
        $this->saveDemo();
        return $this->fetch('save_demo');
    }

    // 数据操作
    protected function saveDemo()
    {
        $demo_model = new \app\common\model\Demo();
        if (request()->isPost())
        {
            $data = input('post.');
            // 其它数据根据实际情况

            if (empty($data[$demo_model->getPk()])){
                $data['create_time'] = NEW_TIME;
                if (!$demo_model->operation($data)){
                    // 此处返回类型可根据实际情况调整
                    $message = $demo_model->getError() == false ? 'saveFailed' : $demo_model->getError();
                    $this->returnError($message);
                }
            }else{
                $data['update_time'] = NEW_TIME;
                if (!$demo_model->isUpdate(true)->operation($data)){
                    // 此处返回类型可根据实际情况调整
                    $message = $demo_model->getError() == false ? 'saveFailed' : $demo_model->getError();
                    $this->returnError($message);
                }
            }
            // $this->p 当前正在列表第几页
            $this->returnSuccess('saveSuccess', url('Demo/index', ['p'=>$this->p]));
        }

        $demo_id = input('param.demo_id', 0, 'intval');
        $oldData = $demo_model->getFind($demo_id);
        if (!empty($oldData)){
            // 有数据时处理列如时间
            $oldData['create_time'] = date('Y-m-d H:i:s', $oldData['create_time']);
        }

        $this->assign('oldData', $oldData);
    }

    // 审核 or 修改状态操作(单个或者批量操作)
    public function auditDemo()
    {
        $param = input('param.');
        if (empty($param['demo_id'])){
            $this->returnError('operationFailed');
        }
        $demo_model = new \app\common\model\Demo();
        if (!$demo_model->status($param['demo_id'], 'status')){
            $this->returnError('operationFailed');
        }
        $this->returnSuccess('operationSuccess', url('Demo/index', ['p'=>$this->p]));
    }

    // 删除操作(单个或者批量操作)
    public function deleteDemo()
    {
        $param = input('param.');
        if (empty($param['demo_id'])){
            $this->returnError('deleteFailed');
        }
        // 注如果：删除会设计到相关信息，自己在模型里添加删除方法操作
        $demo_model = new \app\common\model\Demo();
        if (!$demo_model->remove($param['demo_id'])){
            $this->returnError('deleteFailed');
        }
        $this->returnSuccess('deleteSuccess', url('Demo/index', ['p'=>$this->p]));
    }
    
}