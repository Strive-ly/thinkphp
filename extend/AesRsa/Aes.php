<?php


class Aes
{
    # converted JAVA byte code in to HEX and placed it here
    private $hex_iv = '1234567812345678';
    // CRYPTO_CIPHER_BLOCK_SIZE 16
    private $secret_key = '1234567812345678';

    public function setKey($key) {
        $this->secret_key = $key;
    }

    public function encrypt($input)
    {
        $str_padded = $input;
        $data = openssl_encrypt($str_padded, 'AES-128-CBC', $this->secret_key, OPENSSL_RAW_DATA, $this->hex_iv);
        $data = base64_encode($data);
        return $data;
    }

    public function decrypt($input)
    {
        $decrypted = openssl_decrypt(base64_decode($input), 'AES-128-CBC', $this->secret_key, OPENSSL_RAW_DATA, $this->hex_iv);
        return rtrim(rtrim($decrypted, chr(0)), chr(7));
    }

    // For PKCS7 padding
    private function addpadding($string, $blocksize = 16) {
        $len = strlen($string);
        $pad = $blocksize - ($len % $blocksize);
        $string .= str_repeat(chr($pad), $pad);
        return $string;
    }

    private function strippadding($string) {
        $slast = ord(substr($string, -1));
        $slastc = chr($slast);
        $pcheck = substr($string, -$slast);
        if (preg_match("/$slastc{" . $slast . "}/", $string)) {
            $string = substr($string, 0, strlen($string) - $slast);
            return $string;
        } else {
            return false;
        }
    }

    public function hexToStr($hex)
    {
        $string='';
        for ($i=0; $i < strlen($hex)-1; $i+=2)
        {
            $string .= chr(hexdec($hex[$i].$hex[$i+1]));
        }
        return $string;
    }

}