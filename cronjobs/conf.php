<?php

	defined('SCRIPT_START') or define('SCRIPT_START', microtime(TRUE));
	defined('ONLINE') or define('ONLINE', FALSE);
	defined('MD_DEBUG') or define('MD_DEBUG', FALSE);
	defined('TRACE_LEVEL') or define('TRACE_LEVEL', 0);
	defined('DS') or define('DS', DIRECTORY_SEPARATOR);
	defined('ROOT') or define('ROOT', realpath(dirname(__FILE__)."/../").DS);
	defined('MD_ROOT') or define('MD_ROOT', ROOT.'MD'.DS); //框架代码根目录地址
	defined('SITE_ROOT') or define('SITE_ROOT', ROOT.'site'.DS);
	defined('TMP_ROOT') or define('TMP_ROOT', ROOT.'tmp'.DS);
	defined('LOG_ROOT') or define('LOG_ROOT', ROOT.'log'.DS);
	
	defined('DATA_ROOT') or define('DATA_ROOT', ROOT.'data'.DS);//数据文件目录地址
	defined('NO_SERVER') or define('NO_SERVER', TRUE);//定义没有经过服务器时候的环境常量
	
	defined('MD_CONFIG') or define('MD_CONFIG', SITE_ROOT.'config/app.php');
	defined('OPEN_CACHE') or define('OPEN_CACHE', TRUE);
	ini_set( 'error_reporting', E_ALL | E_STRICT   );
	require_once(MD_ROOT.'MD.php');
