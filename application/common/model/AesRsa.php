<?php
/**
 * Created by PhpStorm.
 * Author: sitenv@aliyun.com
 * CreateTime: 2020/9/27 7:07 下午
 * Blog：www.myblogs.xyz
 */

namespace app\common\model;


class AesRsa
{
    // 私钥文件路径
    public $private_key = '';
    // 公钥文件路径
    public $public_key = '';

    public function encryption($encrypt_data)
    {
        import('AesRsa.Aes');
        import('AesRsa.Rsa');
        $aes = new \Aes();
        $rsa = new \Rsa();
        $rsa->setPrivateKey($this->private_key);
        $rsa->setPublicKey($this->public_key);
        // 密码
        $key = $this->getSeqNo(16);
        // md5加密
        $md5 = md5($key . json_encode($encrypt_data));
        $aes->setKey($key);
        // AES加密
        $aes_str = $aes->encrypt(json_encode($encrypt_data));
        // RSA加密
        $rsa_str = $rsa->publicEncrypt($key);
        $delimiter = chr("123") . chr("123");
        $encrypt = $md5 . $delimiter . $aes_str . $delimiter .  $rsa_str;
        return $encrypt;
    }

    public function decryption($decrypt_data)
    {
        $delimiter = chr("123") . chr("123");
        $result = explode($delimiter, $decrypt_data);
        if (count($result) != 3){
            return [];
        }
        import('AesRsa.Aes');
        import('AesRsa.Rsa');
        $aes = new \Aes();
        $rsa = new \Rsa();
        $rsa->setPrivateKey($this->private_key);
        $rsa->setPublicKey($this->public_key);
        $response_md5 = $result['0'];
        $aesCipherText = $result['1'];
        $rsaCipherText = $result['2'];
        $aesKey = $rsa->privateDecrypt($rsaCipherText);
        $aes->setKey($aesKey);
        $param_data = $aes->decrypt($aesCipherText);
        $info_data = json_decode($param_data, true);
        if (empty($info_data)){
            return [];
        }
        $md5 = md5($aesKey . json_encode($info_data));
        if ($response_md5 != $md5){
            return [];
        }
        return $info_data;
    }

    /**
     * 生成16位随机字符串
     * @return
     */
    public function getSeqNo($len)
    {
        $randomStr = "qwertyuiopasdfghjklzxcvbnm1234567890QWERTYUIOPASDFGHJKLZXCVBNM";
        $str = '';
        for ($i = 0; $i < $len; $i++) {
            $str .= $randomStr[mt_rand(0, strlen($randomStr)-1)];
        }
        return $str;
    }

}