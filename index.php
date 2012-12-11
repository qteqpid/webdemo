<?php

	/**
	 * MD开发规范：
	 *  1. 所有的路径都用全局路径
	 *  2. private类成员以下划线开头
	 *  3. 所有模块化的代码都需要写相应的unittest代码
	 *  4. 提交代码前请确保单元测试全部通过
	 */

	defined('SCRIPT_START') or define('SCRIPT_START', microtime(TRUE));
	date_default_timezone_set('asia/chongqing');
	/**
	 *  初始化一些重要变量
	 */

	/*
	 * 是否是上线状态，默认是关闭的。开发人员请勿打开此开关。
	 */
	defined('ONLINE') or define('ONLINE', FALSE);
	defined('ONLINE') or define('ONLINE', TRUE);
	
	/*
	 * 是否是debug模式，默认是开启的
	 */
	defined('MD_DEBUG') or define('MD_DEBUG', TRUE);
	defined('MD_DEBUG') or define('MD_DEBUG', FALSE);
	
	/*
	 * 是否缓存生效
	 */ 
	defined('OPEN_CACHE') or define('OPEN_CACHE', TRUE);
	defined('OPEN_CACHE') or define('OPEN_CACHE', FALSE);

	//$debug_ips = array('123.120.40.130'); //线上若要看log，请使用该变量，将你的ip添加到调试ip数组中

	/**
	 * log位置跟踪信息显示多少层。默认为3，表示显示3级
	 */
	defined('TRACE_LEVEL') or define('TRACE_LEVEL', 3);
	
	defined('DS') or define('DS', DIRECTORY_SEPARATOR);
	defined('ROOT') or define('ROOT', dirname(__FILE__).DS);
	defined('MD_ROOT') or define('MD_ROOT', ROOT.'MD'.DS); //框架代码根目录地址
	
	//定义根目录下一些文件夹名称
	defined('SITE_DIR') or define('SITE_DIR', 'site');//web应用的目录名称
	defined('TEMP_DIR') or define('TEMP_DIR', 'tmp');//临时文件的目录名称
	defined('LOG_DIR') or define('LOG_DIR', 'log');//日志文件的目录名称
	defined('DATA_DIR') or define('DATA_DIR', 'data');//数据文件的目录名称

	MD_DEBUG and ini_set( 'error_reporting', E_ALL | E_STRICT	);

	defined('MD_CONFIG') or define('MD_CONFIG', ROOT.SITE_DIR.DS.'config/app.php');
	require_once('./MD/MD.php');

	MD::createWebApplication()->run();
?>
