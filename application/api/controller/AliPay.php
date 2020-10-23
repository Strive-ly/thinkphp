<?php
/**
 * Created by PhpStorm.
 * Author: sitenv@aliyun.com
 * CreateTime: 2018/6/29 0029 16:51
 * Blog：www.myblogs.xyz
 */

namespace app\api\controller;


use app\common\model\Setting;

class AliPay extends Common
{
    protected $app_id = ''; // 支付宝AppId
    protected $applet_id = ''; // 小程序AppId
    protected $seller = ''; // 收款人账号
    protected $private_key = './pm/private_key.pem'; // 支付宝商户私钥
    protected $public_key = './pm/public_key.pem'; // 支付宝公钥
    protected $appCertPath = ''; // APP应用证书路径
    protected $appletCertPath = ''; // 小程序应用证书路径
    protected $aliPayCertPath = './cert/alipayCertPublicKey_RSA2.crt'; // 支付宝公钥证书路径
    protected $rootCertPath = './cert/alipayRootCert.crt'; // 支付宝根证书路径

    public function initialize()
    {
        parent::initialize();
        $setting_model = new Setting();
        $setting_data = $setting_model->settingFind('ali_pay');
        $this->app_id = $setting_data['app_id'];
        $this->applet_id = $setting_data['applet_id'];
        $this->seller = $setting_data['seller'];
        $this->appCertPath = './cert/appCertPublicKey_' . $this->app_id . '.crt';
        $this->appletCertPath = './cert/appCertPublicKey_' . $this->applet_id . '.crt';
    }

    /**
     * APP支付宝支付
     * @param $body
     * @param $subject
     * @param $order_code
     * @param $money
     * @param $notify_url
     * @return string
     */
    public function aliPayApp($body, $subject, $order_code, $money, $notify_url)
    {
        /**
         * 调用支付宝接口。
         */
        import('alipay.aop.AopCertClient');
        import('alipay.aop.request.AlipayTradeAppPayRequest');

        $aop = new \AopCertClient();
        $aop->gatewayUrl = "https://openapi.alipay.com/gateway.do";
        $aop->appId = $this->app_id;
        $aop->rsaPrivateKeyFilePath = $this->private_key;
        $aop->format = "json";
        $aop->postCharset = "UTF-8";
        $aop->signType = "RSA2";
        $aop->isCheckAlipayPublicCert = true;
        $aop->alipayrsaPublicKey = $aop->getPublicKey($this->aliPayCertPath);
        $aop->appCertSN = $aop->getCertSN($this->appCertPath);//调用getCertSN获取证书序列号
        $aop->alipayRootCertSN = $aop->getRootCertSN($this->rootCertPath);//调用getRootCertSN获取支付宝根证书序列号

        $request = new \AlipayTradeAppPayRequest();
        $content = [
            'body'=>$body,
            'subject'=>$subject,
            'out_trade_no'=>$order_code,
            'timeout_express'=>'30m',
            'total_amount'=>floatval($money),
            'product_code'=>'QUICK_MSECURITY_PAY'
        ];
        $request->setNotifyUrl($notify_url);
        $request->setBizContent(json_encode($content));
        $response = $aop->sdkExecute($request);

        return $response;
    }

    /**
     * PC端支付宝支付
     * @param $body
     * @param $subject
     * @param $order_code
     * @param $money
     * @param $notify_url
     * @param $return_url
     * @return string|\提交表单HTML文本
     * @throws \Exception
     */
    public function aliPayWeb($body, $subject, $order_code, $money, $notify_url, $return_url)
    {
        import('alipay.aop.AopCertClient');
        import('alipay.aop.request.AlipayTradePagePayRequest');

        $aop = new \AopCertClient();
        $aop->gatewayUrl = "https://openapi.alipay.com/gateway.do";
        $aop->appId = $this->app_id;
        $aop->rsaPrivateKeyFilePath = $this->private_key;
        $aop->format = "json";
        $aop->postCharset = "UTF-8";
        $aop->signType = "RSA2";
        $aop->isCheckAlipayPublicCert = true;
        $aop->alipayrsaPublicKey = $aop->getPublicKey($this->aliPayCertPath);
        $aop->appCertSN = $aop->getCertSN($this->appCertPath);//调用getCertSN获取证书序列号
        $aop->alipayRootCertSN = $aop->getRootCertSN($this->rootCertPath);//调用getRootCertSN获取支付宝根证书序列号

        $request = new \AlipayTradePagePayRequest();
        $content = [
            'body'=>$body,
            'subject'=>$subject,
            'out_trade_no'=>$order_code,
            'total_amount'=>floatval($money),
            'product_code'=>'FAST_INSTANT_TRADE_PAY',
            'qr_pay_mode'=>2,
            'integration_type'=>'PCWEB',
            'notify_url'=>$notify_url,
            'return_url'=>$return_url
        ];
        $request->setBizContent(json_encode($content));
        //请求
        $result = $aop->pageExecute($request, "GET");
        //输出
        return $result;
    }

    /**
     * WAP端支付宝支付
     * @param $body
     * @param $subject
     * @param $order_code
     * @param $money
     * @return string|\提交表单HTML文本
     * @throws \Exception
     */
    public function aliPayWap($body, $subject, $order_code, $money)
    {
        /**
         * 调用支付宝接口。
         */
        import('alipay.aop.AopCertClient');
        import('alipay.aop.request.AlipayTradeWapPayRequest');

        $aop = new \AopCertClient();
        $aop->gatewayUrl = "https://openapi.alipay.com/gateway.do";
        $aop->appId = $this->app_id;
        $aop->rsaPrivateKeyFilePath = $this->private_key;
        $aop->format = "json";
        $aop->postCharset = "UTF-8";
        $aop->signType = "RSA2";
        $aop->isCheckAlipayPublicCert = true;
        $aop->alipayrsaPublicKey = $aop->getPublicKey($this->aliPayCertPath);
        $aop->appCertSN = $aop->getCertSN($this->appCertPath);//调用getCertSN获取证书序列号
        $aop->alipayRootCertSN = $aop->getRootCertSN($this->rootCertPath);//调用getRootCertSN获取支付宝根证书序列号

        $request = new \AlipayTradeWapPayRequest();
        $content = [
            'body'=>$body,
            'subject'=>$subject,
            'out_trade_no'=>$order_code,
            'timeout_express'=>'90m',
            'total_amount'=>floatval($money),
            'product_code'=>'QUICK_WAP_WAY'
        ];
        $request->setBizContent(json_encode($content));
        $result = $aop->pageExecute($request, 'GET');
        return $result;
    }

    /**
     * 支付宝预支付
     * @param $order_code
     * @param $money
     * @return string
     */
    public function appFreeze($order_code, $money)
    {
        /**
         * 调用支付宝接口。
         */
        import('alipay.aop.AopCertClient');
        import('alipay.aop.request.AlipayFundAuthOrderAppFreezeRequest');

        $aop = new \AopCertClient();
        $aop->gatewayUrl = "https://openapi.alipay.com/gateway.do";
        $aop->appId = $this->app_id;
        $aop->rsaPrivateKeyFilePath = $this->private_key;
        $aop->format = "json";
        $aop->postCharset = "UTF-8";
        $aop->signType = "RSA2";
        $aop->isCheckAlipayPublicCert = true;
        $aop->alipayrsaPublicKey = $aop->getPublicKey($this->aliPayCertPath);
        $aop->appCertSN = $aop->getCertSN($this->appCertPath);//调用getCertSN获取证书序列号
        $aop->alipayRootCertSN = $aop->getRootCertSN($this->rootCertPath);//调用getRootCertSN获取支付宝根证书序列号

        $request = new \AlipayFundAuthOrderAppFreezeRequest();
        $content = [
            'out_order_no'=>$order_code,
            'out_request_no'=>$order_code,
            'order_title'=>'预授权冻结押金',
            'amount'=>floatval($money),
            'product_code'=>'PRE_AUTH_ONLINE'
        ];
        $request->setBizContent(json_encode($content));
        $response = $aop->sdkExecute($request);

        return $response;
    }

    /**
     * 支付宝解除冻结
     * @param $order_code
     * @param $trade_code
     * @param $money
     * @return bool
     * @throws \Exception
     */
    public function unfreeze($order_code, $trade_code, $money)
    {
        /**
         * 调用支付宝接口。
         */
        import('alipay.aop.AopCertClient');
        import('alipay.aop.request.AlipayFundAuthOrderUnfreezeRequest');

        $aop = new \AopCertClient();
        $aop->gatewayUrl = "https://openapi.alipay.com/gateway.do";
        $aop->appId = $this->app_id;
        $aop->rsaPrivateKeyFilePath = $this->private_key;
        $aop->format = "json";
        $aop->postCharset = "UTF-8";
        $aop->signType = "RSA2";
        $aop->isCheckAlipayPublicCert = true;
        $aop->alipayrsaPublicKey = $aop->getPublicKey($this->aliPayCertPath);
        $aop->appCertSN = $aop->getCertSN($this->appCertPath);//调用getCertSN获取证书序列号
        $aop->alipayRootCertSN = $aop->getRootCertSN($this->rootCertPath);//调用getRootCertSN获取支付宝根证书序列号

        $request = new \AlipayFundAuthOrderUnfreezeRequest();
        $content = [
            'auth_no'=>$trade_code,
            'out_request_no'=>$order_code,
            'amount'=>floatval($money),
            'remark'=>'预授权解除冻结押金'
        ];
        $request->setBizContent(json_encode($content));
        $result = $aop->execute($request);
        $responseNode = str_replace(".", "_", $request->getApiMethodName()) . "_response";
        $resultCode = $result->$responseNode->code;
        if(!empty($resultCode)&&$resultCode == 10000){
            $status = $result->$responseNode->status;
            if ($status == 'SUCCESS'){
                $out_order_no = empty($result->$responseNode->out_request_no) ? '' : $result->$responseNode->out_request_no;
                if ($out_order_no == $order_code){
                    return true;
                }
            }
        }
        return false;
    }

    /**
     * 支付宝预支付授权查询
     * @param $order_code
     * @return bool|\SimpleXMLElement
     * @throws \Exception
     */
    public function authQuery($order_code)
    {
        /**
         * 调用支付宝接口。
         */
        import('alipay.aop.AopCertClient');
        import('alipay.aop.request.AlipayFundAuthOperationDetailQueryRequest');

        $aop = new \AopCertClient();
        $aop->gatewayUrl = "https://openapi.alipay.com/gateway.do";
        $aop->appId = $this->app_id;
        $aop->rsaPrivateKeyFilePath = $this->private_key;
        $aop->format = "json";
        $aop->postCharset = "UTF-8";
        $aop->signType = "RSA2";
        $aop->isCheckAlipayPublicCert = true;
        $aop->alipayrsaPublicKey = $aop->getPublicKey($this->aliPayCertPath);
        $aop->appCertSN = $aop->getCertSN($this->appCertPath);//调用getCertSN获取证书序列号
        $aop->alipayRootCertSN = $aop->getRootCertSN($this->rootCertPath);//调用getRootCertSN获取支付宝根证书序列号

        $request = new \AlipayFundAuthOperationDetailQueryRequest();
        $content = [
            'out_order_no'=>$order_code,
            'out_request_no'=>$order_code
        ];
        $request->setBizContent(json_encode($content));
        $result = $aop->execute($request);
        $responseNode = str_replace(".", "_", $request->getApiMethodName()) . "_response";
        $resultCode = $result->$responseNode->code;
        if (!empty($resultCode) && $resultCode == 10000){
            return (array)$result->$responseNode;
        }
        return false;
    }

    /**
     * 资金授权撤销
     * @param $param
     * @return bool
     * @throws \Exception
     */
    public function cancelUnfreeze($param)
    {
        /**
         * 调用支付宝接口。
         */
        import('alipay.aop.AopCertClient');
        import('alipay.aop.request.AlipayFundAuthOperationCancelRequest');

        $aop = new \AopCertClient();
        $aop->gatewayUrl = "https://openapi.alipay.com/gateway.do";
        $aop->appId = $this->app_id;
        $aop->rsaPrivateKeyFilePath = $this->private_key;
        $aop->format = "json";
        $aop->postCharset = "UTF-8";
        $aop->signType = "RSA2";
        $aop->isCheckAlipayPublicCert = true;
        $aop->alipayrsaPublicKey = $aop->getPublicKey($this->aliPayCertPath);
        $aop->appCertSN = $aop->getCertSN($this->appCertPath);//调用getCertSN获取证书序列号
        $aop->alipayRootCertSN = $aop->getRootCertSN($this->rootCertPath);//调用getRootCertSN获取支付宝根证书序列号

        $request = new \AlipayFundAuthOperationCancelRequest();
        $content = [
            'auth_no'=>$param['auth_no'],
            'out_order_no'=>$param['auth_no'],
            'operation_id'=>$param['operation_id'],
            'out_request_no'=>$param['auth_no'],
            'remark'=>'授权撤销'
        ];
        $request->setBizContent(json_encode($content));
        $result = $aop->execute($request);
        $responseNode = str_replace(".", "_", $request->getApiMethodName()) . "_response";
        $resultCode = $result->$responseNode->code;
        if(!empty($resultCode) && $resultCode == 10000){
            return true;
        }
        return false;
    }

    /**
     * 订单退款
     * @param $order_code
     * @param $trade_code
     * @param $money
     * @param string $reason
     * @return bool
     * @throws \Exception
     */
    public function refund($order_code, $trade_code, $money, $reason = '押金退款')
    {
        /**
         * 调用支付宝接口。
         */
        import('alipay.aop.AopCertClient');
        import('alipay.aop.request.AlipayTradeRefundRequest');

        $aop = new \AopCertClient();
        $aop->gatewayUrl = "https://openapi.alipay.com/gateway.do";
        $aop->appId = $this->app_id;
        $aop->rsaPrivateKeyFilePath = $this->private_key;
        $aop->format = "json";
        $aop->postCharset = "UTF-8";
        $aop->signType = "RSA2";
        $aop->isCheckAlipayPublicCert = true;
        $aop->alipayrsaPublicKey = $aop->getPublicKey($this->aliPayCertPath);
        $aop->appCertSN = $aop->getCertSN($this->appCertPath);//调用getCertSN获取证书序列号
        $aop->alipayRootCertSN = $aop->getRootCertSN($this->rootCertPath);//调用getRootCertSN获取支付宝根证书序列号

        $request = new \AlipayTradeRefundRequest();
        $content = [
            'out_trade_no'=>$trade_code,
            'refund_amount'=>$money,
            'refund_reason'=>$reason,
            'out_request_no'=>$order_code,
            'operator_id'=>'OP001',
            'store_id'=>'NJ_S_001',
            'terminal_id'=>'',
            'goods_detail'=>[]
        ];
        $request->setBizContent(json_encode($content));
        $result = $aop->execute($request);
        $responseNode = str_replace(".", "_", $request->getApiMethodName()) . "_response";
        $resultCode = $result->$responseNode->code;
        if (!empty($resultCode) && $resultCode == 10000){
            return true;
        }else{
            return false;
        }
    }

    /**
     * 支付宝转账
     * @param $order_code
     * @param $payee_account
     * @param $money
     * @return bool
     * @throws \Exception
     */
    public function transfer($order_code, $payee_account, $money)
    {
        /**
         * 调用支付宝接口。
         */
        import('alipay.aop.AopCertClient');
        import('alipay.aop.request.AlipayFundTransToaccountTransferRequest');

        $aop = new \AopCertClient();
        $aop->gatewayUrl = "https://openapi.alipay.com/gateway.do";
        $aop->appId = $this->app_id;
        $aop->rsaPrivateKeyFilePath = $this->private_key;
        $aop->format = "json";
        $aop->postCharset = "UTF-8";
        $aop->signType = "RSA2";
        $aop->isCheckAlipayPublicCert = true;
        $aop->alipayrsaPublicKey = $aop->getPublicKey($this->aliPayCertPath);
        $aop->appCertSN = $aop->getCertSN($this->appCertPath);//调用getCertSN获取证书序列号
        $aop->alipayRootCertSN = $aop->getRootCertSN($this->rootCertPath);//调用getRootCertSN获取支付宝根证书序列号

        $request = new \AlipayFundTransToaccountTransferRequest();
        $content = [
            'out_biz_no'=>$order_code,
            'payee_type'=>'ALIPAY_LOGONID',
            'payee_account'=>$payee_account,
            'amount'=>$money,
            'payer_show_name'=>'易行中国',
            'payee_real_name'=>'',
            'remark'=>''
        ];
        $request->setBizContent(json_encode($content));
        $result = $aop->execute ($request);
        $responseNode = str_replace(".", "_", $request->getApiMethodName()) . "_response";
        $resultCode = $result->$responseNode->code;
        if ($resultCode == 10000){
            return true;
        }else{
            return false;
        }
    }

    /**
     * 获取支付宝小程序授权用户ID
     * @param $auth_code
     * @return bool|mixed
     * @throws \Exception
     */
    public function getAuthToken($auth_code)
    {
        import('alipay.aop.AopClient');
        import('alipay.aop.request.AlipaySystemOauthTokenRequest');
        $aop = new \AopClient();
        $aop->gatewayUrl = 'https://openapi.alipay.com/gateway.do';
        $aop->appId = $this->applet_id;
        $aop->rsaPrivateKey = 'MIIEpAIBAAKCAQEAnL8e8eLbkRdLjhTdryX/qMrNbDTFmYiTIqc9Ay0H8mDfSMB75QBKyJ2VXH/wU3wBZRIWGqqa8wDxjeahoYBafFN45CDIb9NOtqVSN07+wH2pjnC6rogDK05Lcjsb/BDMQra3lRIyy9NkDMXE19nHWLbbvMhDUePk4BITtTFXZc5o/kyhHJwCBr5RpW6xiK8+pDp4Lim+8ib9UlQ28o0wE7vekMBz9BB6qM45jEyG8jcLXgk3uKmDhn61mUMO7Xw0Ez/aLak8/ex0VFSlC61Pk6WC3wffr4Ty2aY52byKVhxMFhqSY6Txm6Fy32LZJIlo1tGhhWyx6By4HeX6EMv+pQIDAQABAoIBAAVtRIgBX7K1SMNhi49N1H0mkFgnt5OqO9XeTml2vKiym+hr6Z4zFWO+uPYb49kwGOhD6QoBUB4LsVFFY+EwjXFyfmP/kpD0kuwV6zPJA17sutsYog363Yk5S7lnvH9MoZXJt69skt+lsPgUPM1wV8uFgTQXBuWl6z2qwo3qaGk43zZUzeq7ywmZKidYzn6pQ7FNkSrZC09fnfs1rwxrEXBQZaZymHqXctmrvAa+/HoNL7rMa22xZBRtZa3Rn5D8hIns33K9DA8Dq6BbHi1cEd7I1a3xfjbWx869B0hEVh2RL8KEAzyjspdQCNUlO1pu4aw8RRygXkHSvpyb2/OD9L0CgYEA6cZsvvzSyEMfGPp/gJvxzifl69gSRx44Mmwk0getHgPTO7oajXfG8V2xxU0p4ut9+IPoSjQEzDvOr8TQVOVfTobwET+r9Oyd7IReTqj3q6tu0BWv9lXT3vez0BRpI8jK2iHVq8kQaqWeFDCfHwgvchJG6KZREzlH92FQEuIfrBcCgYEAq6X9alsqKw1/4kyBF3yBmiq6fSvCJ4/oUk8JkZWt4Spw7smIWTUaU1l8nXyCvhWnYoIbtlWAwBzTqdpkzkV67vm0m7sMpgs+qagp83859UU2/Aj1Dxv6HYbqZncuWdBNWLcH4+9ul4uMb5YMcWBbwdbwVpzTT+jSrEaEdv3xdKMCgYEAsv7fXQ0wR249bV7AKbU93F/oSEDVHechmFn9h2OhRg4fuuewVQmVJlJbP3rpy3oMpjA5uDdERSy2ARdtfNNzuijPwCVEgPB7qxFfNFNj7+n/mwxAAxmMdHitEXHPXzrrN0IY3MPC8iWFeGgSLiySR4F/Ebvm8BU36U51hT/miFkCgYEAmg5Mt9xCqIlhMfaAFlhE6d1Xq2hOxbs/REBz0hqJCQJSgb6XhZlHm29BjQdnQLumk586yBdNix9USaodt5RclYfANdVXsN9+yj6ICGcz2ZLZmrNfXsQ5Y67nSjfFfg6anPkJN+Z5V8KDtNLzL/7P29XRt6rhbWMFUmGoGjQpNgkCgYA/enm9JKG1FPvb+fqaU7diMR0lYPbRVAmJxdXei0ppwF0hu6+uYvRu/klxpGC9/8o0qVYHeSU35i0T3XiITvBu2/vAfD05udsIIDlyvykLVxSGG7jGISiQu7aD9PD3l33/9712+kvStjdv77xWgW/1eyt0tOTRcZVrcgZxcIxWcw==';
        $aop->alipayrsaPublicKey='MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEAnL8e8eLbkRdLjhTdryX/qMrNbDTFmYiTIqc9Ay0H8mDfSMB75QBKyJ2VXH/wU3wBZRIWGqqa8wDxjeahoYBafFN45CDIb9NOtqVSN07+wH2pjnC6rogDK05Lcjsb/BDMQra3lRIyy9NkDMXE19nHWLbbvMhDUePk4BITtTFXZc5o/kyhHJwCBr5RpW6xiK8+pDp4Lim+8ib9UlQ28o0wE7vekMBz9BB6qM45jEyG8jcLXgk3uKmDhn61mUMO7Xw0Ez/aLak8/ex0VFSlC61Pk6WC3wffr4Ty2aY52byKVhxMFhqSY6Txm6Fy32LZJIlo1tGhhWyx6By4HeX6EMv+pQIDAQAB';
        $aop->apiVersion = '1.0';
        $aop->signType = 'RSA2';
        $aop->postCharset='UTF-8';
        $aop->format='json';
        $request = new \AlipaySystemOauthTokenRequest();
        $request->setGrantType("authorization_code");
        $request->setCode($auth_code);
        // $request->setRefreshToken("201208134b203fe6c11548bcabd8da5bb087a83b");
        $result = $aop->execute ( $request);

        $responseNode = str_replace(".", "_", $request->getApiMethodName()) . "_response";
        $resultCode = $result->$responseNode->code;
        sp($resultCode);die;
        if(!empty($resultCode)&&$resultCode != 10000){
            echo "失败";
        } else {
            echo "成功";
        }

        /**
         * 调用支付宝接口。
         */
        import('alipay.aop.AopCertClient');
        import('alipay.aop.request.AlipaySystemOauthTokenRequest');

        $aop = new \AopCertClient();
        $aop->gatewayUrl = "https://openapi.alipay.com/gateway.do";
        $aop->appId = $this->applet_id;
        $aop->rsaPrivateKeyFilePath = $this->private_key;
        $aop->format = "json";
        $aop->postCharset = "UTF-8";
        $aop->signType = "RSA2";
        $aop->alipayPublicKey= $this->public_key;
        // $aop->isCheckAlipayPublicCert = true;
        // $aop->alipayrsaPublicKey = $aop->getPublicKey($this->aliPayCertPath);
        // $aop->appCertSN = $aop->getCertSN($this->appCertPath);//调用getCertSN获取证书序列号
        // $aop->alipayRootCertSN = $aop->getRootCertSN($this->rootCertPath);//调用getRootCertSN获取支付宝根证书序列号

        $request = new \AlipaySystemOauthTokenRequest();
        $request->setGrantType("authorization_code");
        $request->setCode($auth_code);
        $result = $aop->execute($request);
        sp($result);die;

        $responseNode = str_replace(".", "_", $request->getApiMethodName()) . "_response";
        $resultCode = $result->$responseNode->code;
        sp($result);
        sp($resultCode);die;
        if(!empty($resultCode)&&$resultCode != 10000){
            return false;
        }
        $resultCode = (array)$result->$responseNode;
        if(!empty($resultCode['user_id'])){
            return $resultCode['user_id'];
        }
        return false;
    }

    /**
     * 小程序支付接口
     * @param $body
     * @param $subject
     * @param $order_code
     * @param $money
     * @param $notify_url
     * @param $buyer_id
     * @return string
     */
    public function aliPayApplet($body, $subject, $order_code, $money, $notify_url, $buyer_id)
    {
        /**
         * 调用支付宝接口。
         */
        import('alipay.aop.AopCertClient');
        import('alipay.aop.request.AlipayTradeCreateRequest');

        $aop = new \AopCertClient();
        $aop->gatewayUrl = "https://openapi.alipay.com/gateway.do";
        $aop->appId = $this->applet_id;
        $aop->rsaPrivateKeyFilePath = $this->private_key;
        $aop->format = "json";
        $aop->postCharset = "UTF-8";
        $aop->signType = "RSA2";
        $aop->isCheckAlipayPublicCert = true;
        $aop->alipayrsaPublicKey = $aop->getPublicKey($this->aliPayCertPath);
        $aop->appCertSN = $aop->getCertSN($this->appletCertPath);//调用getCertSN获取证书序列号
        $aop->alipayRootCertSN = $aop->getRootCertSN($this->rootCertPath);//调用getRootCertSN获取支付宝根证书序列号

        $request = new \AlipayTradeCreateRequest();
        $content = [
            'body'=>$body,
            'subject'=>$subject,
            'out_trade_no'=>$order_code,
            'timeout_express'=>'30m',
            'total_amount'=>floatval($money),
            'buyer_id'=>$buyer_id,
        ];
        $request->setNotifyUrl($notify_url);
        $request->setBizContent(json_encode($content));
        $result = $aop->execute($request);
        $responseNode = str_replace(".", "_", $request->getApiMethodName()) . "_response";
        $resultCode = $result->$responseNode->code;
        if(!empty($resultCode) && $resultCode == 10000){
            return $result->$responseNode->trade_no;
        }
        return false;
    }

    // 支付宝授权参数
    public function get_auth()
    {
        $data = [
            'apiname'=>'com.alipay.account.auth',
            'method'=>'alipay.open.auth.sdk.code.get',
            'app_id'=>$this->applet_id,
            'app_name'=>'mc',
            'biz_type'=>'openservice',
            'pid'=>'2088421318174638',
            'product_id'=>'APP_FAST_LOGIN',
            'scope'=>'kuaijie',
            'target_id'=>md5(order_code() . uniqid()),
            'auth_type'=>'AUTHACCOUNT',
            'sign_type'=>'RSA2',
        ];
        ksort($data);
        import('alipay.aop.AopCertClient');
        $aop = new \AopClient();
        $aop->rsaPrivateKeyFilePath = $this->private_key;
        $aop->postCharset = "UTF-8";
        $sign =  urlencode($aop->generateSign($data, 'RSA2'));
        $data['sign'] = $sign;
        $data['contents'] = http_build_query($data);

        return $data;
    }
    
}