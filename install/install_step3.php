<?php include_once("head.php") ?>

<?php 

	$quit = FALSE;
	$msg = '';
	
	if (!isset($db_host, $db_username, $db_password, $db_name, $site_url)) {
		$msg .= '<p>'.$i_message['mysql_invalid_configure'].'</p>';
		$quit = TRUE;
	}
	if (!$quit && !@mysqli_connect($db_host, $db_username, $db_password))
	{
		$msg .= '<p>'.mysqli_error().'</p>';
		$quit = TRUE;
	}

	if (!$quit)	{

		$db_charset	= 'utf8';
		$conn = mysqli_connect($db_host, $db_username, $db_password);
		mysqli_query($conn, "DROP DATABASE IF EXISTS `{$db_name}`");
		mysqli_query($conn, "CREATE DATABASE `{$db_name}` DEFAULT CHARACTER SET $db_charset ");
		if (mysqli_errno()) {
			$errormsg = mysqli_error();
			$msg .= '<p>'.($errormsg ? $errormsg : $i_message['database_errno']).'</p>';
			$quit = TRUE;
		} else {
			mysqli_query($conn, 'SET NAMES utf8');
//			mysqli_query($conn, "SET character_set_connection={$db_charset}, character_set_results={$db_charset}");
			mysqli_select_db($conn, $db_name);
		}
		
		if (!$quit) {
			// 插入数据库结构
			$sql = read_file("structure.sql");
			
			$tablenum = 0;
			foreach(explode(";", trim($sql)) as $query) {
				$query = trim($query);
				if($query) {
					if (substr($query, 0, 15) == 'CREATE DATABASE' || substr($query, 0, 5) == 'USE `') {
						continue;
					}
					if(substr($query, 0, 12) == 'CREATE TABLE') {
						$name = preg_replace("/CREATE TABLE ([A-Z ]*)`([a-z0-9_]+)` .*/is", "\\2", $query);
						echo '<p>'.$i_message['create_table'].' '.$name.' ... <span class="blue">OK</span></p>';
						@mysqli_query($conn, createtable($query, $db_charset));
						$tablenum++;
					} else {
						@mysqli_query($conn, $query);
					}
				}
			}
			echo '<p>'.$i_message['create_table'].' 共 '.$tablenum.'个 <span class="blue">OK</span></p>';
			
			// 插入数据
			$sqlFiles = readData(dirname(__FILE__).'/data');
			foreach ($sqlFiles as $f) {
				$sql = read_file($f);
				foreach(explode(";\n", trim($sql)) as $query) {
					$query = trim($query);
					if($query) {
						@mysqli_query($conn, $query);
					}
				}
				echo "<p> 插入数据 $f <span class=\"blue\">OK</span></p>";
			}
			echo '<p><span class="blue">'.$i_message['install_success'].'</span></p>';
			echo '
				<p class="center">
					<span class="red">注意在安装完成后删除install目录，以防止被不良分子盗取重要信息</span><br/>
					<span class="submit" onclick="window.location.href =\''.$site_url.'\';">完成</span>
				</p>';
			
			fopen('install.lock', 'w');

		}
	}
	
	if ($quit) {
		?>
		<div class="error"><?php echo $i_message['error'];?></div>
		<?php
		echo $msg;
	}
	
?>

<?php include_once("foot.php") ?>
