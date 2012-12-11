<?php
if (!defined('INSTALL'))
{
	exit ('Access Denied');
}


function addS(&$array)
{
	if (is_array($array))
	{
		foreach ($array as $key => $value)
		{
			 addS($array[$key]);
		}
	}
	elseif (is_string($array))
	{
		$array = addslashes($array);
	}
}

function result($result = 1, $output = 1)
{
	if($result)
	{
		$text = ' ... <span class="blue">OK</span>';
		if(!$output)
		{
			return $text;
		}
		echo $text;
	}
	else
	{
		$text = ' ... <span class="red">Failed</span>';
		if(!$output)
		{
			return $text;
		}
		echo $text;
	}
}

function createtable($sql, $db_charset)
{
	$db_charset = (strpos($db_charset, '-') === FALSE) ? $db_charset : str_replace('-', '', $db_charset);
	$type = strtoupper(preg_replace("/^\s*CREATE TABLE\s+.+\s+\(.+?\).*(ENGINE|TYPE)\s*=\s*([a-z]+?).*$/isU", "\\2", $sql));
	$type = in_array($type, array("MYISAM", "HEAP")) ? $type : "MYISAM";
	return preg_replace("/^\s*(CREATE TABLE\s+.+\s+\(.+?\)).*$/isU", "\\1", $sql).
		(mysql_get_server_info() > "4.1" ? " ENGINE=$type DEFAULT CHARSET=$db_charset" : " TYPE=$type");
}

function writable($var)
{
	$writeable = FALSE;
	$var = ROOT.$var;
	if(!is_dir($var))
	{
		@mkdir($var, 0777);
	}
	if (is_dir($var))
	{
		$var .= '/temp.txt';
		if (($fp = @fopen($var, 'w')) && (fwrite($fp, 'thinksns')))
		{
			fclose($fp);
			@unlink($var);
			$writeable = TRUE;
		}
	}
	return $writeable;
}


function check_extension($extension_name) {
	global $i_message;
	if (extension_loaded($extension_name))	{
		echo $extension_name.':'.$i_message['support'];
		result(1, 1);
		return TRUE;
	} else {
		$key = "php_extention_unload_".$extension_name;
		echo '<span class="red">'.$i_message[$key].'</span>';
		result(0, 1);
		$quit = FALSE;
	}
}

function read_file($file) {
	$fp = fopen($file, 'r');
	$sql = '';
	while (!feof($fp)) {
	    	$line = trim(fgets($fp));
		if ($line !== "" && substr($line,0,2) != '--') {
			$sql .= $line;
		}
	}
	fclose($fp);
	return $sql;
}

function readData($dir) {
	$dir = rtrim($dir,'/').'/';
	$dir_handler = opendir($dir);
	$fls	= array();
	while( $file = readdir($dir_handler) ) {
		$fls[]	= $file;
	}
	$result = array();
	foreach($fls as $file) {
		$current_file	= $dir.$file;
		if (is_file($current_file)) {
			$tmp	= pathinfo($current_file);
			if( 'sql' == $tmp['extension'] ) {
				$result[] = $current_file;
			}
		}
	}
	return $result;
}
?>
