<?php
/**
 *
 *
 * @author hebidu <hebiduhebi@126.com>
 * @date  15/6/29 20:17
 * @copyright by itboye.com
 */

if (version_compare(PHP_VERSION, '5.3.0', '<')) die('require PHP > 5.3.0 !');

//绑定安装模块
define('BIND_MODULE','Install');

define("PROJECT_NAME","201507shuiguo");

// 是否调试模式
define('APP_DEBUG', true);
// 插件目录
define('ADDON_PATH', './Addons/');

// 应用目录
define("APP_PATH", "./Application/");
// 包含应用元数据
require_once(APP_PATH . '/Common/Conf/appmeta.php');

// 静态缓存目录
define('HTML_PATH', "../../Runtime/".PROJECT_NAME."/htmlCache/");

// 运行时文件
define("RUNTIME_PATH","../../Runtime/".PROJECT_NAME."/");

// 框架目录
define("THINK_PATH",realpath("../../thinkphp/thinkphp_clone/").'/');
// 加载
require "../../thinkphp/thinkphp_clone/ThinkPHP.php";
