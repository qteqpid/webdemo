<?php
/*
 * tThinkSNS 安装文件,修改自pbdigg.
 */

error_reporting(0);
session_start();
define('INSTALL', TRUE);
define('ROOT', str_replace('\\', '/', substr(dirname(__FILE__), 0, -7)));

$_SITE = '爱每叮网';
$_VERSION = '2.0';

include 'install_function.php';
include 'install_lang.php';

$timestamp				=	time();
$structurefile = 'structure.sql';

//判断是否安装过
header('Content-Type: text/html; charset=utf-8');
if (file_exists('install.lock'))
{
	exit($i_message['install_lock']);
}
if (!is_readable($structurefile))
{
	exit($i_message['install_dbFile_error']);
}

set_magic_quotes_runtime(0);
if (!get_magic_quotes_gpc())
{
	addS($_POST);
	addS($_GET);
}
@extract($_POST);
@extract($_GET);
?>
<?php if(!$v) $v = 1; ?>
<?php require_once("install_step$v.php"); ?>
