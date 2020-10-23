<?php
/**
 * Created by PhpStorm.
 * Author: sitenv@aliyun.com
 * CreateTime: 2020/10/12 9:14 下午
 * Blog：www.myblogs.xyz
 */

namespace app\admin\controller;


use app\common\model\Admin;
use app\common\model\Menu;
use app\common\model\Role;
use app\common\model\RoleMaps;
use app\common\model\Setting;
use think\Controller;

class Common extends Controller
{
    protected $adminId = 0;
    protected $adminData = [];
    protected $p = 0;
    protected $menuList = [];

    public function initialize()
    {
        $admin_id = session('admin_id');
        $this->adminId = empty($admin_id) ? 0 : session('admin_id');
        if (empty($this->adminId)){
            $this->redirect('Login/index');
        }
        $admin_model = new Admin();
        $this->adminData = $admin_model->getFind($this->adminId, 'admin_id,account,nickname,role_id');
        if (empty($this->adminData)){
            $this->redirect('Login/index');
        }
        // language
        $lang = input('param.lang', 'cn');
        if (!empty($lang)){
            session('lang', $lang);
        }
        $this->p = input('param.p', 1, 'intval');
        // 获取当前访问模块信息
        $url = request()->pathinfo();
        if ($url == '/'){
            $this->redirect('Index/index');
        }
        $url_info = explode('/', $url);
        if (!empty($url_info) && count($url_info) > 1){
            $url_one = explode_list('/',$url);
            $url_two = explode_list('.',explode_list('/',$url, 1));
            $menu_action = posUrl($url_one) . '/' . $url_two;
            // 验证权限
            $menu_model = new Menu();
            $menu_data = $menu_model->getFind(['menu_action'=>ucfirst($menu_action)], 'menu_id,menu_name');
            if ($this->adminData['role_id'] != 1){
                $role_maps = new RoleMaps();
                $this->adminData['menu_list'] = $role_maps->getMenuIdsByRoleId($this->adminData['role_id']);
                if (empty($this->adminData['menu_list'])){
                    session('admin_id', null);
                    session('admin_data', null);
                    $this->error('很抱歉您的账号没有访问权限', url('Login/index'), '', 10);
                }
                if (!empty($menu_data)){
                    if ($url_two != 'index'){
                        if (!$role_maps->exists(['role_id'=>$this->adminData['role_id'], 'menu_id'=>$menu_data['menu_id']])){
                            $this->error('很抱歉您没有权限操作模块：' . $menu_data['menu_name']);
                        }
                    }
                }
            }
            // 获取当前访问目录
            if (!empty($menu_data['menu_id'])){
                $menu_ids = $menu_model->getParents($menu_data['menu_id']);
                $menu_list = $menu_model->getMenuNameArray($menu_ids);
                $this->menuList = $menu_list;
            }
            // 记录操作日志
            if (!empty($menu_data['menu_id'])){
                $title = $menu_model->where('menu_id', $menu_data['menu_id'])->value('menu_name');
                $param = input('param.');
                if (!empty($param)){
                    $request_data = [
                        'admin_id'=>$this->adminId,
                        'title'=>$title,
                        'url'=>$url,
                        'param'=>json_encode($param),
                        'account'=>$this->adminData['account'],
                        'create_time'=>NEW_TIME,
                        'create_ip'=>get_client_ip()
                    ];
                    $request_log = new \app\common\model\RequestLog();
                    $request_log->insert($request_data);
                }
            }
        }
        // 记录权限身份
        $role_model = new Role();
        $this->adminData['role_name'] = $role_model->where('role_id', $this->adminData['role_id'])->value('role_name');
        $admin_data = session('admin_data');
        if (empty($admin_data)){
            session('admin_data', $this->adminData);
        }
        // 其它基本配置
        $style = 'style="width:800px;height:360px;"';
        $history_go = '<a href="javascript:history.go(-1);" class="smtQrIpt" style="background-image: none;padding: 0;display: inline-block;text-align: center;">返回上级</a>';
        $history_back = '<a href="javascript:history.back(-1);" class="remberBtn" style="display: inline-block;text-align: center;color: #ffffff;">返回上级</a>';
        // 获取站点配置信息
        $setting_model = new Setting();
        $site = $setting_model->settingFind('site');
        // 加载当前控制器语言包
        $controller_name = strtolower(request()->controller());
        $this->loadLang($controller_name);

        $this->assign([
            'p'=>$this->p,
            'site'=>$site,
            'style'=>$style,
            'new_time'=>NEW_TIME,
            'menu_list'=>$this->menuList,
            'history_go'=>$history_go,
            'history_back'=>$history_back,
            'admin_data'=>$this->adminData
        ]);
    }

    /**
     * 加载语言文件
     * @param $name
     */
    protected function loadLang($name)
    {
        $lang = session('lang');
        if (!empty($lang) && $lang == 'cn'){
            \think\facade\Lang::load(APP_PATH . 'common/lang/' . request()->module() . '.php');
        }
        // \think\facade\Lang::load(APP_PATH . request()->module() . '/lang/' . request()->langset() . '/' . str_replace('.', '/', $name) . '.php');
    }

    protected function getCity()
    {
        $province_id = input('post.province_id',0,'intval');
        $city_id = input('post.city_id',0,'intval');
        $county_id = input('post.county_id',0,'intval');
        $city_data = $province_id . ',' . $city_id . ',' . $county_id;
        return $city_data;
    }

    protected function classify($data)
    {
        if (empty($data)) return '';
        $new_array = [];
        if (is_array($data)){
            foreach ($data as $key=>$val){
                if (!empty($val)){
                    $new_array[] = $val;
                }
            }
        }
        return $new_array;
    }

    /**
     * @param $message
     * @param bool $close
     * @param int $time
     */
    protected function returnError($message, $close = false, $time = 3000)
    {
        $str = '<script>';
        $str .= 'parent.error("' . L($message) . '",' . $time . ',' . $close . ');';
        $str .= '</script>';
        exit($str);
    }

    /**
     * @param $message
     * @param string $jumpUrl
     * @param bool $close
     * @param int $time
     */
    protected function returnSuccess($message, $jumpUrl = '', $close = false, $time = 3000)
    {
        $str = '<script>';
        $str .= 'parent.success("' . L($message) . '",' . $time . ',\'jumpUrl("' . $jumpUrl . '","' . $close . '")\');';
        $str .= '</script>';
        exit($str);
    }

    /**
     * @param int $status 返回状态
     * @param string $data 返回成功数据
     * @param string $message 返回信息
     * @return string
     */
    protected function jsonReturn($status = 0, $data = '', $message = '')
    {
        header('Content-Type:application/json; charset=utf-8');
        $jsonData = [
            'status'   => $status,
            'data'     => $data,
            'message'  => L($message)
        ];
        exit(json_encode($jsonData));
    }

    /**
     * 图片上传
     * @param string $img
     * @param string $type
     * @return string
     */
    protected function uploads($img = '', $type = 'face')
    {
        if (empty($img)) return '';
        // 重组路径
        $file_path = './public/upload/' . $type . '/';
        $time_path = date('Y/m/d/', time());
        $server_path = $file_path . $time_path;
        $filename = uniqid() . '.jpg';
        $file = $server_path . $filename;
        // 判断目录书否创建
        if(!is_dir($server_path)){
            mkdir($server_path,0777,true);
        }
        $rows = file_put_contents($file,base64_decode($img));
        // 判断图片是否上传成功
        if(is_null($rows)){
            $this->jsonReturn(0, '', 'uploadFailed');
        }
        return $time_path . $filename;
    }

    /**
     * POST请求数据
     * @param $url
     * @param $jsonData
     * @return mixed
     */
    public function curlPost($url, $jsonData)
    {
        $header[] = "Content-type: text/json";
        // POST发送数据
        $ch = curl_init(); // 初始化CURL会话
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $jsonData);
        $result = curl_exec($ch); // 获取结果
        curl_close($ch);// 关闭会话
        return $result;
    }

    /**
     * 创建二维码
     * @param $data
     * @return string
     */
    protected function qrCode($data)
    {
        import('phpqrcode.phpqrcode');
        // 纠错级别：L、M、Q、H
        $level = 'H';
        // 点的大小：1到10,用于手机端4就可以了
        $size = 4;
        $time_path = date('Y/m/d/', time());
        $server_path = './upload/code/' . $time_path;
        $filename = uniqid() . '.png';
        $file = $server_path . $filename;
        // 判断目录书否创建
        if(!is_dir($server_path)){
            mkdir($server_path,0777,true);
        }
        $qr_code = new \QRcode();
        $qr_code::png($data, $file, $level, $size);
        return $time_path . $filename;
    }

}