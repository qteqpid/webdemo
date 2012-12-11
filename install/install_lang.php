<?php
if (!defined('INSTALL'))
{
	exit ('Access Denied');
}

$i_message['install_lock'] = '您已安装过 '. $_SITE . $_VERSION . '，如果需要重新安装，请先删除install目录下的install.lock文件';
$i_message['install_title'] = '每叮网 ' . $_VERSION . ' 安装向导';
$i_message['install_wizard'] = '安装向导';
$i_message['install_dbFile_error'] = '数据库文件无法读取，请检查/install/structure.sql和data.sql是否存在。';
$i_message['support'] = '支持';
$i_message['unsupport'] = '不支持';
$i_message['php_extention'] = 'PHP扩展';
$i_message['php_extention_unload_gd'] = '您的服务器没有安装这个PHP扩展：gd';
$i_message['php_extention_unload_mbstring'] = '您的服务器没有安装这个PHP扩展：mbstring';
$i_message['php_extention_unload_mysqli'] = '您的服务器没有安装这个PHP扩展：mysqli';
$i_message['php_extention_unload_curl'] = '您的服务器没有安装这个PHP扩展：curl';
$i_message['php_extention_unload_memcache'] = '您的服务器没有安装这个PHP扩展：memcache';
$i_message['php_extention_unload_redis'] = '您的服务器没有安装这个PHP扩展：redis';
$i_message['install_env'] = '服务器配置';
$i_message['php_os'] = '操作系统';
$i_message['php_version'] = 'PHP版本';
$i_message['file_upload'] = '附件上传';
$i_message['mysql'] = 'MySQL数据库';
$i_message['mysql_unsupport'] = '您的服务器不支持MYSQL数据库，无法安装每叮网。';
$i_message['install_prev'] = '上一步';
$i_message['install_next'] = '下一步';
$i_message['dirmod'] = '目录和文件的写权限';
$i_message['install_dirmod'] = '目录和文件是否可写，如果发生错误，请更改文件/目录属性 777';
$i_message['install_setting'] = '数据库资料与管理员账号设置';
$i_message['install_mysql'] = '数据库配置';
$i_message['install_mysql_host'] = '数据库服务器';
$i_message['install_mysql_host_intro'] = '格式：地址(:端口)，一般为 localhost';
$i_message['install_mysql_username'] = '数据库用户名';
$i_message['install_mysql_password'] = '数据库密码';
$i_message['install_mysql_name'] = '数据库名';
$i_message['site_url'] = ' 站点地址';
$i_message['install_mysql_host_empty'] = '数据库服务器不能为空';
$i_message['install_mysql_username_empty'] = '数据库用户名不能为空';
$i_message['install_mysql_name_empty'] = '数据库名不能为空';
$i_message['install_mysql_siteurl_empty'] = '站点地址不能为空';
$i_message['error'] = '错误';
$i_message['database_errno'] = '程序在执行数据库操作时发生了一个错误，安装过程无法继续进行。';
$i_message['create_table'] = '创建表';
$i_message['install_success'] = '安装成功';
$i_message['mysql_invalid_configure'] = '数据库配置信息不完整';
