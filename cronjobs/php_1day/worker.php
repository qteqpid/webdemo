<?php
	
	require_once (dirname(__FILE__)."/../conf.php");
	MD::createWebApplication();
	

	if( !isset($db) ) { $db = MD::app()->db; }
	
	ini_set( 'error_reporting', E_ALL | E_STRICT );
	ini_set( 'display_errors', '1' );
	ini_set( 'max_execution_time',	10*60 );
	ini_set( 'memory_limit',	50*1024*1024 );

	$jobs_dir = dirname(__FILE__).'/jobs/';

	$fls = array();
	if ($argc > 1) {
		$fls = array_slice($argv,1);
	} else {
		$dir	= opendir($jobs_dir);
		while( $file = readdir($dir) ) {
			$fls[]	= $file;
		}
	}
	
	natcasesort($fls);
	foreach($fls as $file) {
		$current_file	= $jobs_dir.$file;
		if (is_file($current_file)) {
			$tmp	= pathinfo($current_file);
			if( 'php' != $tmp['extension'] ) {
				continue;
			}
			$message = include( $current_file );
			MD::log($message, CLogger::LEVEL_CRON, "php_1day");
		}
	}
	MD::app()->log->flush();
	
?>
