<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2014 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------

// 应用入口文件
//ini_set('session.cookie_domain', '.zzgdapp.com');

// 开启调试模式 建议开发阶段开启 部署阶段注释或者设为false
define('APP_DEBUG', true);

// 通用设置PATH_INFO，如果保证PHP能正常获取到
if (! isset($_SERVER['PATH_INFO']) || empty($_SERVER['PATH_INFO'])) {
    $parse_url = parse_url($_SERVER['REQUEST_URI']);
    $_SERVER['PATH_INFO'] = $parse_url['path'];
}

// 是否为APP
define('IS_APP', preg_match('/(Mobile ).*(Appcan)/', $_SERVER['HTTP_USER_AGENT']));
define('IS_WEIXIN', preg_match('/(MicroMessenger)/', $_SERVER['HTTP_USER_AGENT']));

// 定义应用目录
define('APP_PATH','../Application/');

// 引入ThinkPHP入口文件
require '../ThinkPHP/ThinkPHP.php';

// 亲^_^ 后面不需要任何代码了 就是如此简单