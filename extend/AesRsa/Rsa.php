<?php

class Rsa {

    // 私钥文件路径
    public $rsaPrivateKeyFilePath;
    // 公钥文件路径
    public $rsaPublicKeyFilePath;

    /**
     * 设置密钥文件路径
     * @param $file_path
     */
    public function setPrivateKey($file_path)
    {
        $this->rsaPrivateKeyFilePath = $file_path;
    }

    /**
     * 设置公钥文件路径
     * @param $file_path
     */
    public function setPublicKey($file_path)
    {
        $this->rsaPublicKeyFilePath = $file_path;
    }

    /**
     * 获取私钥
     * @return bool|resource
     */
    public function getPrivateKey()
    {
        if (!empty($this->rsaPrivateKeyFilePath)){
            $abs_path = $this->rsaPrivateKeyFilePath;
        }else{
            $abs_path = dirname(__FILE__) . '/rsa_private_key.pem';
        }
        $content = file_get_contents($abs_path);
        return openssl_pkey_get_private($content);
    }

    /**
     * 获取公钥
     * @return bool|resource
     */
    public function getPublicKey()
    {
        if (!empty($this->rsaPublicKeyFilePath)){
            $abs_path = $this->rsaPublicKeyFilePath;
        }else{
            $abs_path = dirname(__FILE__) . '/rsa_public_key.pem';
        }
        $content = file_get_contents($abs_path);
        return openssl_pkey_get_public($content);
    }

    /**
     * 私钥加密
     * @param string $data
     * @return null|string
     */
    public function privateEncrypt($data = '')
    {
        if (!is_string($data)) {
            return null;
        }
        return openssl_private_encrypt($data,$encrypted,$this->getPrivateKey()) ? base64_encode($encrypted) : null;
    }

    /**
     * 公钥加密
     * @param string $data
     * @return null|string
     */
    public function publicEncrypt($data = '')
    {
        if (!is_string($data)) {
            return null;
        }
        return openssl_public_encrypt($data,$encrypted,$this->getPublicKey()) ? base64_encode($encrypted) : null;
    }

    /**
     * 私钥解密
     * @param string $encrypted
     * @return null
     */
    public function privateDecrypt($encrypted = '')
    {
        if (!is_string($encrypted)) {
            return null;
        }
        return (openssl_private_decrypt(base64_decode($encrypted), $decrypted, $this->getPrivateKey())) ? $decrypted : null;
    }

    /**
     * 公钥解密
     * @param string $encrypted
     * @return null
     */
    public function publicDecrypt($encrypted = '')
    {
        if (!is_string($encrypted)) {
            return null;
        }
        return (openssl_public_decrypt(base64_decode($encrypted), $decrypted, $this->getPublicKey())) ? $decrypted : null;
    }

}