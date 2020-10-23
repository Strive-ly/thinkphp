<?php
/**
 * Created by PhpStorm.
 * Author: sitenv@aliyun.com
 * CreateTime: 2019/2/14 0014 9:58
 * Blog：www.myblogs.xyz
 */

// 绑定API模块
define('BIND_MODULE', 'api');

// 设置跨域请求头
header("Access-Control-Allow-Origin: *");

// 如果需要自定义请求头时，直接把请求头添加到下面的参数里面即可
header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept, Authorization");

// 前端VUE框架自定义Header头请求API时，会发起一次预请求，thinkphp默认是拒绝OPTIONS类型的请求的
// 判断是否是OPTIONS请求，是的话直接exit结束，直接让浏览器返回200
if($_SERVER['REQUEST_METHOD'] == 'OPTIONS'){
    exit;
}

require './public.php';