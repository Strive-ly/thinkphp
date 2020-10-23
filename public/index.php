<?php
/**
 * Created by PhpStorm.
 * Author: sitenv@aliyun.com
 * CreateTime: 2019/2/14 0014 10:11
 * Blog：www.myblogs.xyz
 */

// 绑定前台模块
define('BIND_MODULE', 'index');

// 设置跨域请求头
header("Access-Control-Allow-Origin: *");

// 如果需要自定义请求头时，直接把请求头添加到下面的参数里面即可
header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept, Authorization");

// 加载公共引导文件
require './public.php';
