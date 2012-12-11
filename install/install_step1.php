<?php include_once("head.php") ?>

<?php 
	$quit = FALSE;
?>
<!-- 

检查服务器配置和目录文件权限

 -->
<!-- 
	服务器配置检测 
-->
<div class="shade">
<div class="settingHead"><?php echo $i_message['install_env'];?></div>

<!-- 操作系统 -->
<h5><?php echo $i_message['php_os'];?></h5>
<p><?php echo PHP_OS;result(1, 1);?></p>

<!-- PHP版本 -->
<h5><?php echo $i_message['php_version'];?></h5>
<p>
<?php
echo PHP_VERSION;
if (PHP_VERSION < '5')
{
	result(0, 1);
	$quit = TRUE;
}
else
{
	result(1, 1);
}
?></p>

<!-- 文件上传 -->
<h5><?php echo $i_message['file_upload'];?></h5>
<p>
<?php
if (@ini_get('file_uploads'))
{
	echo $i_message['support'],'/',@ini_get('upload_max_filesize');
}
else
{
	echo '<span class="red">'.$i_message['unsupport'].'</span>';
}
result(1, 1);
?></p>

<!-- PHP扩展 -->
<h5><?php echo $i_message['php_extention'];?></h5>
<p>
<?php
if (check_extension('mysqli') == FALSE) $quit = TRUE;
?></p>
<p>
<?php
if (check_extension('gd') == FALSE) $quit = TRUE;
?></p>
<p>
<?php
if (check_extension('curl') == FALSE) $quit = TRUE;
?></p>
<p>
<?php
if (check_extension('memcache') == FALSE) $quit = TRUE;
?></p>
<p>
<?php
if (check_extension('redis') == FALSE) $quit = TRUE;
?></p>

<!-- Mysql数据库 -->
<h5><?php echo $i_message['mysql'];?></h5>
<p>
<?php
if (function_exists('mysql_connect'))
{
	echo $i_message['support'];
	result(1, 1);
}
else
{
	echo '<span class="red">'.$i_message['mysql_unsupport'].'</span>';
	result(0, 1);
	$quit = TRUE;
}
?></p>
</div>

<!-- 
	目录和文件的写权限
-->
<div class="shade">
<div class="settingHead"><?php echo $i_message['dirmod'];?></div>
<?php
$dirarray = array (
	'log',
	'avatar',
	'avatar/thumbs',
	'tmp',
	'install',
);
foreach ($dirarray as $key => $dir)
{
	if (writable($dir))
	{
		echo '<p>'. $dir.result(1, 0). '</p>';
	}
	else
	{
		echo '<p>'. $dir.result(0, 0). '</p>';
		$quit = TRUE;
	}
}

?>
<!-- <span class='red'><?php echo $i_message['install_dirmod'];?></span> -->
</div>
<p class="center">
	<form method="post" action='index.php?v=2'>
	<input style="width:200px;" type="submit" class="submit" name="next" value="仍要<?php echo $i_message['install_next'];?>（谨慎）" <?php //if($quit) echo "disabled=\"disabled\"";?>>
	</form>
</p>

<?php include_once("foot.php") ?>
