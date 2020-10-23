<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2018 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------

// [ 应用入口文件 ]
namespace think;

// PHP版本验证需要大于5.6.0
if (version_compare(PHP_VERSION, '7.0.1', '<')) {
    die('Require PHP > 7.0.1 !');
}

// 定义时间
define('NEW_TIME', time());

// 定义应用目录
define('APP_PATH', __DIR__ . '/../application/');

// 定义缓存目录
define('RUNTIME_PATH', __DIR__ . '/../runtime/');

// 加载基础文件
require __DIR__ . '/../thinkphp/base.php';

// 支持事先使用静态方法设置Request对象和Config对象

// 执行应用并响应
Container::get('app')->bind(BIND_MODULE)->run()->send();