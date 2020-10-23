<?php
/**
 * Created by PhpStorm.
 * Author: sitenv@aliyun.com
 * CreateTime: 2017/10/19 0019 15:53
 * Blog：www.myblogs.xyz
 */

require __DIR__ . '/../autoload.php';

use JPush\Client as JPush;

//$app_key = getenv('app_key');
$app_key = '3f292084d71349aee466109d';
//$master_secret = getenv('master_secret');
$master_secret = 'd994b2e8a5036b64970ae586';
//$registration_id = getenv('registration_id');

$client = new JPush($app_key, $master_secret);