<?php

/**
 * Created by PhpStorm.
 * Author: sitenv@aliyun.com
 * CreateTime: 2017/10/19 0019 15:40
 * Blog：www.myblogs.xyz
 */
require_once dirname(__DIR__) . '/autoload.php';


class Push
{
    protected $app_key;
    protected $master_secret;
    protected $client;

    /**
     * 构造器
     *
     * Push constructor.
     * @param string $app_key 必填，AppKey
     * @param string $master_secret 必填，Master Secret
     */
    public function __construct($app_key,$master_secret)
    {
        $this->app_key = $app_key;
        $this->master_secret = $master_secret;
        // 初始化
        $this->client = new JPush\Client($app_key,$master_secret);
    }

    /**
     * @param $data
     * @return mixed
     */
    protected function json_array($data)
    {
        return json_decode(json_encode($data),true);
    }

    /**
     * 提示消息
     * Audience 听众
     * addAllAudience 所有听众
     * addTag 标签听众（并集）addAlias 别名听众 addTagAnd 标签听众（交集） addTagNot 标签听众（补集）
     * addRegistrationId 注册ID
     * @param string $alert
     * @param string $form
     * @return mixed
     */
    public function alert($alert = '', $form = 'all')
    {
        $result = $this->client->push()
            ->setPlatform($form)
            ->addAllAudience()
            ->setNotificationAlert($alert)
            ->send();
        return $this->json_array($result);
    }

    /**
     * 提示消息（生产环境）
     * @param string $alert
     * @param string $alias
     * @param array $ios_notification
     * @param array $android_notification
     * @param string $form
     * @return mixed
     */
    public function alerts($alert = '', $alias = '', $ios_notification = [], $android_notification = [], $form = 'all')
    {
        $result = $this->client->push()
            ->setPlatform($form)
            ->addAlias($alias)
            ->options(['apns_production'=>true])
            ->iosNotification($alert, $ios_notification)
            ->androidNotification($alert, $android_notification)
            ->send();
        return $this->json_array($result);
    }

    /**
     * 提示消息（开发环境）
     * @param string $alert
     * @param string $alias
     * @param array $ios_notification
     * @param array $android_notification
     * @param string $form
     * @return mixed
     */
    public function send($alert = '', $alias = '', $ios_notification = [], $android_notification = [], $form = 'all')
    {
        $result = $this->client->push()
            ->setPlatform($form)
            ->addAlias($alias)
            ->options(['apns_production'=>false])
            ->iosNotification($alert, $ios_notification)
            ->androidNotification($alert, $android_notification)
            ->send();
        return $this->json_array($result);
    }

    public function into($cid = '', $platform = 'all', $tag = '', $regId = '', $alert = '', $ios_notification = [], $android_notification = [], $content = '', $message = '', $options = '')
    {
        $result = $this->client->push()
            ->setCid($cid)
            ->setPlatform($platform)
            ->addTag($tag)
            ->addRegistrationId($regId)
            ->iosNotification($alert, $ios_notification)
            ->androidNotification($alert, $android_notification)
            ->message($content, $message)
            ->options($options)
            ->send();
        return $this->json_array($result);
    }




}