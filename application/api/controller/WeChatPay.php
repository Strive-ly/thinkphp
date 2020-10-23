<?php
/**
 * Created by PhpStorm.
 * Author: sitenv@aliyun.com
 * CreateTime: 2018/6/29 0029 16:58
 * Blog：www.myblogs.xyz
 */

namespace app\api\controller;
use app\common\model\Setting;


class WeChatPay extends Common
{
    /**
     * 微信APP支付案例（统一下单）
     * @param array $param
     */
    public function app_pay($param = [])
    {
        // 基本信息
        $body = $param['body'];
        $order_code = $param['order_code']; // 订单号
        $money = floatval($param['money']) * 100; // 微信金额为分因此*100
        $nonce_str = self::getNonceStr(); // 随机字符串
        $notify_url = $param['notify_url'];
        // 获取微信配置
        $sms_model = new Setting();
        $sms_data = $sms_model->settingFind('we_chat');
        if (empty($sms_data['app_id']) || empty($sms_data['app_id']) || empty($sms_data['key'])){
            $this->jsonError('微信支付通道暂时关闭');
        }
        // 发送数据
        $post_data = [
            'appid'            => $sms_data['app_id'],
            'mch_id'           => $sms_data['mch_id'],
            'device_info'      => 'web',
            'nonce_str'        => $nonce_str,
            'body'             => $body,
            'out_trade_no'     => $order_code,
            'total_fee'        => $money,
            'spbill_create_ip' => get_client_ip(),
            'trade_type'       => 'APP',
            'notify_url'       => $notify_url,
        ];
        // 生成签名
        $post_data['sign'] = $this->MakeSign($post_data, $sms_data['key']);
        // 组合XML数据
        $xmlData = $this->MakeXml($post_data);
        // 发送请求
        $url = 'https://api.mch.weixin.qq.com/pay/unifiedorder';
        $result = $this->curl_post($url, $xmlData);
        // 解析xml数据
        $payment = $this->FromXml($result);
        // 是否请求成功
        if($payment['return_code'] != 'SUCCESS'){
            $this->jsonError($payment['return_msg']);
        }
        if ($payment['result_code'] == 'SUCCESS'){
            $payment['package'] = 'Sign=WXPay';
            $payment['timestamp'] = NEW_TIME;
            //创建用户订单信息

            // 创建统一订单信息
            $sign_data = [
                'appid'=>$sms_data['app_id'],
                'partnerid'=>$sms_data['mch_id'],
                'prepayid'=>$payment['prepay_id'],
                'package'=>$payment['package'],
                'noncestr'=>$nonce_str,
                'timestamp'=>(string)$payment['timestamp']
            ];
            $sign_data['sign'] = $this->MakeSign($sign_data , $sms_data['key']);
            $this->jsonResult($sign_data);
        }
        $this->jsonError($body . '失败，请稍后再试！');
    }

    /**
     * 小程序和微信JSAPI支付（统一下单）
     * @param array $param
     */
    public function routine_pay($param = [])
    {
        // 基本信息
        $body = $param['body'];
        $openid = $param['openid']; // 用户openid
        $order_code = $param['order_code']; // 订单号
        $money = floatval($param['money']) * 100; // 微信金额为分因此*100
        $nonce_str = self::getNonceStr(); // 随机字符串
        $notify_url = $param['notify_url'];
        // 获取微信配置
        $sms_model = new Setting();
        $sms_data = $sms_model->settingFind('we_applet');
        // 发送数据
        $post_data = [
            'appid'            => $sms_data['app_id'],
            'mch_id'           => $sms_data['mch_id'],
            'nonce_str'        => $nonce_str,
            'body'             => $body,
            'out_trade_no'     => $order_code,
            'total_fee'        => $money,
            'spbill_create_ip' => get_client_ip(),
            'trade_type'       => 'JSAPI',
            'openid'           => $openid,
            'notify_url'       => $notify_url
        ];
        // 生成签名
        $post_data['sign'] = $this->MakeSign($post_data, $sms_data['key']);
        // 组合XML数据
        $xmlData = $this->MakeXml($post_data);
        // 发送请求
        $url = 'https://api.mch.weixin.qq.com/pay/unifiedorder';
        $result = $this->curl_post($url, $xmlData);
        // 解析xml数据
        $payment = $this->FromXml($result);
        // 是否请求成功
        if($payment['return_code'] != 'SUCCESS'){
            $this->jsonError($payment['return_msg']);
        }
        if ($payment['result_code'] == 'SUCCESS'){
            $payment['timestamp'] = NEW_TIME;
            //创建用户订单信息

            // 创建统一订单信息
            $sign_data = [
                'appId'=>$payment['appid'],
                'nonceStr'=>$payment['nonce_str'],
                'package'=>'prepay_id=' . $payment['prepay_id'],
                'signType'=>'MD5',
                'timeStamp'=>$payment['timestamp']
            ];
            $sign_data['paySign'] = $this->MakeSign($sign_data , $sms_data['key']);
            $this->jsonResult($sign_data);
        }
        $this->jsonError($body . '失败');
    }

    /**
     * 微信二维码支付
     * @param array $param
     */
    public function code_pay($param = [])
    {
        // 基本信息
        $body = $param['body'];
        $order_code = $param['order_code']; // 订单号
        $money = floatval($param['money']) * 100; // 微信金额为分因此*100
        $nonce_str = self::getNonceStr(); // 随机字符串
        $notify_url = $param['notify_url'];
        $product_id = md5($order_code); // 商品ID
        // 获取微信配置
        $sms_model = new Setting();
        $sms_data = $sms_model->settingFind('we_chat');
        // 发送数据
        $post_data = [
            'appid'            => $sms_data['app_id'],
            'mch_id'           => $sms_data['mch_id'],
            'nonce_str'        => $nonce_str,
            'body'             => 'xxx-' . $body,
            'out_trade_no'     => $order_code,
            'total_fee'        => $money,
            'spbill_create_ip' => get_client_ip(),
            'trade_type'       => 'NATIVE',
            'product_id'       => $product_id,
            'notify_url'       => $notify_url
        ];
        // 生成签名
        $post_data['sign'] = $this->MakeSign($post_data, $sms_data['key']);
        // 组合XML数据
        $xmlData = $this->MakeXml($post_data);
        // 发送请求
        $url = 'https://api.mch.weixin.qq.com/pay/unifiedorder';
        $result = $this->curl_post($url, $xmlData);
        // 解析xml数据
        $payment = $this->FromXml($result);
        // 是否请求成功
        if($payment['return_code'] != 'SUCCESS'){
            $this->jsonError($payment['return_msg']);
        }
        // 生成二维码
        import("phpqrcode.phpqrcode");
        $Object = new \QRcode();
        $level = 'L';
        $size = 4;
        $Object->png($payment['code_url'], false, $level, $size);
    }

    /**
     * 微信企业付款到零钱
     * @param array $param
     */
    public function withdraw_pay($param = [])
    {
        // 基本信息
        $body = $param['body'];
        $openid = $param['openid']; // 用户openid
        $order_code = $param['order_code']; // 订单号
        $money = floatval($param['money']) * 100; // 微信金额为分因此*100
        $nonce_str = self::getNonceStr(); // 随机字符串
        // 获取微信配置
        $sms_model = new Setting();
        $sms_data = $sms_model->settingFind('we_chat');
        // 发送数据
        $post_data = [
            'mch_appid'        => $sms_data['app_id'],
            'mchid'            => $sms_data['mch_id'],
            'nonce_str'        => $nonce_str,
            'partner_trade_no' => $order_code,
            'openid'           => $openid,
            'check_name'       => 'NO_CHECK',
            'amount'           => $money,
            'desc'             => $body,
            'spbill_create_ip' => get_client_ip()
        ];
        // 生成签名
        $post_data['sign'] = $this->MakeSign($post_data, $sms_data['key']);
        // 组合XML数据
        $xmlData = $this->MakeXml($post_data);
        // 发送请求
        $url = 'https://api.mch.weixin.qq.com/mmpaymkttransfers/promotion/transfers';
        $result = $this->curl_post($url, $xmlData);
        // 解析xml数据
        $payment = $this->FromXml($result);
        // 是否请求成功
        if($payment['return_code'] != 'SUCCESS'){
            $this->jsonError($payment['return_msg']);
        }
        if ($payment['result_code'] == 'SUCCESS'){
            // 业务处理
            $this->jsonSuccess();
        }
        $this->jsonError($payment['return_msg']);
    }

    /**
     * 退款查询订单
     * @param $order_code
     * @return bool
     */
    public function refundQuery($order_code)
    {
        // 获取微信配置
        $sms_model = new Setting();
        $sms_data = $sms_model->settingFind('we_chat');
        $nonce_str = self::getNonceStr(); // 随机字符串
        // 订单退款信息
        $post_data = [
            'appid'        => $sms_data['app_id'],
            'mch_id'       => $sms_data['mch_id'],
            'nonce_str'    => $nonce_str,
            'transaction_id' => $order_code,
        ];
        // 生成签名
        $post_data['sign'] = $this->MakeSign($post_data, $sms_data['key']);
        // 组合XML数据
        $xmlData = $this->MakeXml($post_data);
        // 发送请求
        $url = 'https://api.mch.weixin.qq.com/pay/refundquery';
        $result = $this->curl_post($url, $xmlData, true);
        // 解析xml数据
        $payment = $this->FromXml($result);
        //判断是否请求成功
        if ($payment['return_code'] != 'SUCCESS') {
            return false;
        }
        // 判断是否支付
        if ($payment['result_code'] == 'SUCCESS') {
            return $payment['trade_state'];
        }
        return false;
    }

    /**
     * @param $order_code string 商家退款订单号
     * @param $trade_code string 微信支付订单号
     * @param $total double 订单金额
     * @param $money double 退款金额
     * @param string $reason
     * @return bool
     */
    public function refund($order_code, $trade_code, $total, $money, $reason = '押金退款')
    {
        $total = floatval($total) * 100; // 订单金额
        $amount = floatval($money) * 100; // 退款金额
        $nonce_str = self::getNonceStr(); // 随机字符串
        // 获取微信配置
        $setting_model = new Setting();
        $wx_setting = $setting_model->settingFind('we_chat');
        // 订单退款信息
        $post_data = [
            'appid'          => $wx_setting['app_id'],
            'mch_id'         => $wx_setting['mch_id'],
            'nonce_str'      => $nonce_str,
            'out_trade_no'   => $trade_code,
            'out_refund_no'  => $order_code,
            'total_fee'      => $total,
            'refund_fee'     => $amount,
            'refund_desc'    => $reason
        ];
        // 生成签名
        $post_data['sign'] = $this->MakeSign($post_data, $wx_setting['key']);
        // 组合XML数据
        $xmlData = $this->MakeXml($post_data);
        // 发送请求
        $url = 'https://api.mch.weixin.qq.com/secapi/pay/refund';
        $result = $this->curl_post($url, $xmlData, true);
        // 解析xml数据
        $payment = $this->FromXml($result);
        // 是否请求成功
        if($payment['return_code'] != 'SUCCESS'){
            return false;
        }
        if ($payment['result_code'] == 'SUCCESS'){
            sleep(2);
            // 查询结果
            $post_data = [
                'appid'     => $wx_setting['app_id'],
                'mch_id'    => $wx_setting['mch_id'],
                'nonce_str' => $nonce_str,
                'refund_id' => $payment['refund_id'],
            ];
            // 生成签名
            $post_data['sign'] = $this->MakeSign($post_data, $wx_setting['key']);
            // 组合XML数据
            $xmlData = $this->MakeXml($post_data);
            // 发送请求
            $url = 'https://api.mch.weixin.qq.com/pay/refundquery';
            $result = $this->curl_post($url, $xmlData, true);
            // 解析xml数据
            $rows = $this->FromXml($result);
            //判断是否请求成功
            if($rows['return_code'] == 'SUCCESS'){
                // 判断是否支付
                if($rows['result_code'] == 'SUCCESS'){
                    if ($rows['refund_status_0'] == 'SUCCESS' || $rows['refund_status_0'] == 'PROCESSING'){
                        return true;
                    }
                }

            }
        }
        return false;
    }

    /*************************需要使用到的方法*******************************/
    /**
     * 产生随机字符串，不长于32位
     * @param int $length
     * @return string
     */
    protected static function getNonceStr($length = 32)
    {
        $chars = "abcdefghijklmnopqrstuvwxyz0123456789";
        $str ="";
        for ( $i = 0; $i < $length; $i++ )  {
            $str .= substr($chars, mt_rand(0, strlen($chars)-1), 1);
        }
        return $str;
    }

    /**
     * 将xml转为array
     * @param $xml
     * @return mixed
     */
    protected function FromXml($xml)
    {
        if(!$xml){
            $this->jsonReturn(0,'','XML解析错误！');
        }
        libxml_disable_entity_loader(true);
        $values = json_decode(json_encode(simplexml_load_string($xml, 'SimpleXMLElement', LIBXML_NOCDATA)), true);
        return $values;
    }

    /**
     * 生成签名 签名，本函数不覆盖sign成员变量，如要设置签名需要调用SetSign方法赋值
     * @param $sign
     * @param string $keys
     * @return string
     */
    protected function MakeSign($sign, $keys = '')
    {
        ksort($sign); // 数组排序
        $str = '';
        foreach($sign as $key=>$val){
            if($val != ''){
                $str .= $key . "=" . $val . "&";
            }
        }
        $str .= "key=" . $keys;
        $sign = strtoupper(md5($str));
        return $sign;
    }

    /**
     * 生成XML数据
     * @param $data
     * @return string
     */
    protected function MakeXml($data)
    {
        $xmlData = "<xml>";
        foreach($data as $key=>$val){
            $xmlData.="<".$key.">".$val."</".$key.">";
        }
        $xmlData.= "</xml>";
        return $xmlData;
    }

    /**
     * POST请求数据
     * @param $url
     * @param $xmlData
     * @param bool $useCert
     * @return mixed
     */
    public function curl_post($url,$xmlData, $useCert = false)
    {
        $header[] = "Content-type: text/xml";
        // POST发送数据
        $ch = curl_init(); // 初始化CURL会话
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        if($useCert == true){
            //设置证书
            //使用证书：cert 与 key 分别属于两个.pem文件
            //证书文件请放入服务器的非web目录下
            $sslCertPath = getcwd() . "/cert/apiclient_cert.pem";
            $sslKeyPath = getcwd() . "/cert/apiclient_key.pem";
            curl_setopt($ch,CURLOPT_SSLCERTTYPE,'PEM');
            curl_setopt($ch,CURLOPT_SSLCERT, $sslCertPath);
            curl_setopt($ch,CURLOPT_SSLKEYTYPE,'PEM');
            curl_setopt($ch,CURLOPT_SSLKEY, $sslKeyPath);
        }
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $xmlData);
        $result = curl_exec($ch); // 获取结果
        curl_close($ch);// 关闭会话
        return $result;
    }

}