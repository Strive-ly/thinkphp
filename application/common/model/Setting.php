<?php
/**
 * Created by PhpStorm.
 * Author: sitenv@aliyun.com
 * CreateTime: 2020/10/12 9:21 下午
 * Blog：www.myblogs.xyz
 */

namespace app\common\model;


class Setting extends Common
{
    public function initialize()
    {
        parent::initialize();
        $this->table = Config('database.prefix') . 'setting';
    }

    public function settingFind($key = '')
    {
        if (empty($key)) return [];
        $exist = $this->where('key', $key)->count();
        if ($exist){
            $field_path = APP_PATH . 'setting/' . $key .'.json';
            $json_data = file_get_contents($field_path);
            $data = json_decode($json_data,true);
            $encrypt_val = $data[$key];
            // 数据信息解密
            $aes_esa = new AesRsa();
            $decrypt_val = $aes_esa->decryption($encrypt_val);
            return $decrypt_val;
        }
        return [];
    }

    public function insetSetting($key = '', $val = [])
    {
        if (empty($key) || empty($val)) return false;
        // 数据信息加密
        $aes_esa = new AesRsa();
        $encrypt_val = $aes_esa->encryption($val);
        $exist = $this->where('key', $key)->count();
        if ($exist){
            $rows = $this->where('key', $key)->setField('val', $encrypt_val);
            if (empty($rows)){
                return false;
            }
        }else{
            $rows = $this->insert(['key'=>$key,'val'=>$encrypt_val]);
            if (empty($rows)){
                return false;
            }
        }
        $field_path = APP_PATH . 'setting/' . $key .'.json';
        file_put_contents($field_path, json_encode([$key=>$encrypt_val]));
        return true;
    }

    /**
     * 极光信息推送
     * @param $alert
     * @param $alias
     * @param array $extras
     * @return bool
     */
    public function ruleExec($alert, $alias, $extras = [])
    {
        try{
            import('push.api.Push');
            $setting_model = new Setting();
            $setting_data = $setting_model->settingFind('push');
            if (empty($setting_data)){
                return false;
            }
            $push = new \Push($setting_data['app_key'], $setting_data['master_secret']);
            $push_data = [
                'title'=>$alert,
                'extras'=>$extras
            ];
            if (empty($setting_data['environment'])){
                $push->send($alert, $alias, $push_data, $push_data);
            }else{
                $push->alerts($alert, $alias, $push_data, $push_data);
            }
        } catch(\Exception $e){

        }
        return true;
    }

}