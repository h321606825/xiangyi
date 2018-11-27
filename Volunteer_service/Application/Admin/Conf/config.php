<?php
return array(
    //'配置项'=>'配置值'
    'LAYOUT_ON'             =>  true, // 是否启用布局
    'LAYOUT_NAME'           =>  '_layout',
    //'URL_HTML_SUFFIX'       =>  '',  // URL伪静态后缀设置
    'URL_PARAMS_BIND_TYPE'  =>  0, // URL变量绑定的类型 0 按变量名绑定 1 按变量顺序绑定
    'HTML_CACHE_ON'         =>  false, // 开启静态缓存
    'HTML_CACHE_TIME'       =>  120,   // 全局静态缓存有效期（秒）
    'HTML_FILE_SUFFIX'      =>  '.shtml', // 设置静态缓存文件后缀
    'HTML_CACHE_RULES'      =>  array(  // 定义静态缓存规则
         'login:index'      =>  'login'
    ),

    'TMPL_ACTION_ERROR'     =>  APP_PATH.'/Common/View/dispatch_jump.php', // 默认错误跳转对应的模板文件
    'TMPL_ACTION_SUCCESS'   =>  APP_PATH.'/Common/View/dispatch_jump.php', // 默认成功跳转对应的模板文件
    'TMPL_EXCEPTION_FILE'   =>  APP_PATH.'/Common/View/exception.php',// 异常页面的模板文件
    
    'AUTH_ON'               =>  true,       // 开启权限认证
    'USER_AUTH_TYPE'        =>  2,      // 默认认证类型0不认证； 1 登录认证； 2 实时认证
    'USER_AUTH_KEY'         =>  'authId',   // 用户认证SESSION标记
    'ADMIN_AUTH_KEY'        =>  'administrator',
    'NOT_AUTH_MODULE'       =>  '',     // 默认无需认证模块
    'REQUIRE_AUTH_MODULE'   =>  '',     // 默认需要认证模块
    'NOT_AUTH_ACTION'       =>  '',     // 默认无需认证操作
    'REQUIRE_AUTH_ACTION'   =>  '',     // 默认需要认证操作

    'AUTH_TABLE_MENU'       =>  'bs_menu',    // 菜单表名称
    'AUTH_TABLE_NODE'       =>  'bs_node',    // 权限节点表名称
    'AUTH_TABLE_ROLE'       =>  'bs_role',    // 角色表名称
    'AUTH_TABLE_ACCESS'     =>  'bs_role_user',    // 用户角色表名称
    'AUTH_TABLE_DEPT'       =>  'bs_dept',    // 用户角色表名称
    'GUEST_AUTH_ID'         =>   0,     // 游客的用户ID
    'SESSION_PREFIX'        => 'admin',
);