<?php
/**
 * 单元测试前要load的文件,通过phpunit.xml自动加载
 *
 * @author qteqpid
 */
defined('ONLINE') or define('ONLINE', FALSE);
defined('MD_DEBUG') or define('MD_DEBUG', TRUE);  // 是否是debug模式，默认是开启的
defined('MD_ROOT') or define('MD_ROOT', dirname(__FILE__).'/../'); 
defined('MD_CONFIG') or define('MD_CONFIG', MD_ROOT.'/config/test.php');

require_once (dirname(__FILE__).'/../MD.php');
require_once (MD_ROOT.'base/AbstractTestCase.php');

MD::createWebApplication(MD_CONFIG);

