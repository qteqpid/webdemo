<?php

include_once('Mbstring.php');
        
function str_cut($str, $start, $mx) {  
	return mb_strlen($str)>$mx ? mb_substr($str, $start, $mx -1).'..' : $str;
}

function pure_str_cut($str, $start, $mx) {
	return mb_strlen($str)>$mx ? mb_substr($str, $start, $mx) : $str;
}

/**
 * 将列表中的对象的某个属性取出来凑成数组返回
 */
function arraized($objectArr, $key) {
	if (!$objectArr) return array();
	$arr = array();
	foreach ($objectArr as $obj) {
		$arr[] = $obj->$key;
	}
	return $arr;
}

/**
 * 根据某字段对对象数组排序，默认是从小到大排序, 若字段类型是整数，则用int_sort_objs
 * @param array $list 要排序的对象数组
 * @param string $key  依据的字段
 * @param string $order 排序方式，'asc'表示从小到大，'desc'表示从大到小
 */
function sort_objs($list,$key,$order='asc') {
    if(empty($list) || !isset($list[0]->{$key}))
       return $list;

    $alist = array();
    for($i = 0; $i < count($list); $i++) {
        $alist[$list[$i]->{$key}.'_'.$i] = $list[$i];       
    }  
    $order=="asc" ? ksort($alist) : krsort($alist);
    return array_values($alist);   
}

/**
 * 判断字段是整数类型时请用这个，字符串用sort_objs
 */
function int_sort_objs($list, $key, $order='asc') {
    if(empty($list) || !isset($list[0]->{$key}))
       return $list;
    $len = count($list);
    for($i = 1; $i < $len; $i++) {
    	for($j = 0; $j < $len-$i; $j++) {
    		if ($list[$j]->{$key} > $list[$j+1]->{$key}) {
    			list($list[$j+1],$list[$j]) = array($list[$j],$list[$j+1]);
    		}
    	}
    }
    if ($order!="asc") $list = array_reverse($list);
    return $list;
}


function formatDate($timestamp, $return_words='auto', $return_dt_format='%Y年%m月%d日   %H时') {
	if( $return_words == FALSE ) {
		return strftime($return_dt_format, $timestamp);
	}
	$time	= time() - $timestamp;
	$h	= floor($time / 3600);
	$time	-= $h * 3600;
	$m	= floor($time / 60);
	$time	-= $m * 60;
	$s	= $time;
	if( $return_words === 'auto' && $h >= 12 ) {
		return strftime($return_dt_format, $timestamp);
	}
	$txt	= '##BEFORE## ';
	if( $h > 0 ) {
		$txt	.= $h;
		$txt	.= $h==1 ? '##HOUR##' : '##HOURS##';
	}
	if( $h >= 3 ) {
		$txt	.= '##AGO##';
		return _parse_date_replace_strings($txt);
	}
	if( $m > 0 ) {
		if( $h > 0 ) {
			$txt	.= '##AND##';
		}
		$txt	.= $m;
		$txt	.= $m==1 ? '##MIN##' : '##MINS##';
		if( $h > 0 ) {
			$txt	.= '##AGO##';
			return _parse_date_replace_strings($txt);
		}
	}
	if( $h==0 && $m==0 ) {
		if( $s == 0 ) {
			return _parse_date_replace_strings('##NOW##');
		}
		$txt	.= $s;
		$txt	.= $s==1 ? '##SEC##' : '##SECS##';
	}
	$txt	.= '##AGO##';
	return _parse_date_replace_strings($txt);
}

function _parse_date_replace_strings($txt='') {
	global $page;
	$tmp	= array (
		'##BEFORE##'	=> '', 
		'##HOUR##'		=> '时',
		'##HOURS##'		=> '时',
		'##MIN##'		=> '分',
		'##MINS##'		=> '分',
		'##SEC##'		=> '秒',
		'##SECS##'		=> '秒',
		'##AND##'		=> '',
		'##AGO##'		=> '之前',
		'##NOW##'		=> '不久之前',
	);
	$txt	= str_replace(array_keys($tmp), array_values($tmp), $txt);
	$txt	= trim($txt);
	$txt	= str_replace(' ', '&nbsp;', $txt);
	return $txt;
}

if( ! function_exists('json_encode') ) {
	function json_encode($a=false)
	{
		// Thanks to Mike Griffiths, http://www.mike-griffiths.co.uk/
	
		// Some basic debugging to ensure we have something returned
		if (is_null($a)) return 'null';
		if ($a === false) return 'false';
		if ($a === true) return 'true';
		if (is_scalar($a))
		{
			if (is_float($a))
			{
				// Always use "." for floats.
				return floatval(str_replace(",", ".", strval($a)));
			}

			if (is_string($a))
			{
				static $jsonReplaces = array(array("\\", "/", "\n", "\t", "\r", "\b", "\f", '"'), array('\\\\', '\\/', '\\n', '\\t', '\\r', '\\b', '\\f', '\"'));
				return '"' . str_replace($jsonReplaces[0], $jsonReplaces[1], $a) . '"';
			}
			else
				return $a;
		}
		$isList = true;
		for ($i = 0, reset($a); $i<count($a); $i++) {
			if (key($a) !== $i)
			{
				$isList = false;
				break;
			}
		}
		$result = array();
		if ($isList)
		{
			foreach ($a as $v) $result[] = json_encode($v);
			return '[' . join(',', $result) . ']';
		}
		else
		{
			foreach ($a as $k => $v) $result[] = json_encode($k).':'.json_encode($v);
			return '{' . join(',', $result) . '}';
		}
	}
		
}

	/**
	 * 
	 * 格式化questionMessage,answerMessage
	 * @param string $message
	 * @param int $maxLength
	 */
    function formattedMessage($message, $maxLength) {
	    if (mb_strlen($message) > $maxLength) {
			$message = str_cut($message, 0, $maxLength);
		}
		return $message;
    }
    
    function readFileToArray($filePath, $preserve = FALSE, $prefix = '', $num = 0) {
    	$content = array();
	    $i = 0;
    	if (file_exists($filePath) && is_readable($filePath)) {
	    	$fp = fopen($filePath, 'r');
	    	while (!feof($fp)) {
	    		$line = trim(fgets($fp));
				if ($line !== "") {
					$i++;
					if ($preserve) { // 内容也当键值
						$content[$prefix.$line] = $line;
					} else {
			    		$content[] = $line;
					}
					if ($num != 0 && $i >= $num) break;
				}
	    	}
	        fclose($fp);
    	}
        return $content;
    }
    
    function readFileToString($filePath) {
    	if (file_exists($filePath) && is_readable($filePath)) {
	    	return file_get_contents($filePath);
    	} else {
    		return '';
    	}
    }
    
    function appendToFile($file, $data) {
    	$fh = fopen($file,'a');
    	if (is_array($data)) {
    		foreach ($data as $d) {
    			fwrite($fh, $d."\n");
    		}
    	} else {
    		fwrite($fh, $data."\n");
    	}
    	fclose($fh);
    }
    
    function writeFile($file, $data, $flush = FALSE) {
	$i = 0;
	$limit = 10000; // 一万行一次flush
    	$fh = fopen($file,'w');
    	if (is_array($data)) {
    		foreach ($data as $d) {
			$i++;
    			fwrite($fh, $d."\n");
			if ($flush && $i % $limit == 0) fflush($fh);
    		}
    	} else {
    		fwrite($fh, $data."\n");
    	}
    	fclose($fh);
    }
    
    /**
     * 将一个含有url的字符串进行分割成为两个数组
     * 第一个数组为不含url的字符串
     * 第二个数组为含有url的字符串
     */
	function splitContentByUrl($content){
		//$urlPattern = '/https?:\/\/\w+\.\w+[\w\/\.\-?&=~!@#$%^*()+|:;,]*/';
    	$urlPattern = '/https?:\/\/[^\s\x80-\xff]+/';
    	
    	$normalStrs = array();//不含url链接的字符串
    	$urlStrs = array();//匹配出来的url链接字符串
    	
    	$matches = array();//存放匹配结果的数组
    	$startPos = 0;//循环搜索时候的，搜索的起始位置
    
    	while(preg_match($urlPattern, $content, $matches, PREG_OFFSET_CAPTURE, $startPos)){
    		$happenPos = $matches[0][1];
    		$normalStrs[] = substr($content, $startPos, $happenPos -$startPos);
    		$startPos = $happenPos + strlen($matches[0][0]);
    		$urlStrs[] = $matches[0][0];
    	}
    	$normalStrs[] = substr($content, $startPos);
    	
    	return array($normalStrs,$urlStrs);
	}
	
	/**
	 * 将含有链接的文本内容转化为短链接
	 * 返回包含短链接的文本
	 * @param string $content
	 * @return string
	 */
    function shortenContentUrl($content){
    	//获取分割好的字符串数组
    	list($normalStrs,$urlStrs) = splitContentByUrl($content);
    	
    	//获取压缩后的字符串
    	if(!empty($urlStrs))$resultStrs = MD::app()->oauthService->weibo->getShortenUrl($urlStrs);
    	else $resultStrs = array();
		
    	//组装返回结果
    	$i =0;$resultContent = '';
    	foreach($resultStrs as $resultUrl){
    		$resultContent .= $normalStrs[$i] . $resultUrl;
    		$i ++;
    	}
    	$resultContent .= $normalStrs[$i];
    	
    	return $resultContent;
    }
    
    /**
	 * 将含有短链接的文本内容转化为原始链接
	 * 返回包含原始链接的文本
	 * @param string $content
	 * @return string
	 */
    function originContentUrl($content){
    	//获取分割好的字符串数组
    	list($normalStrs,$urlStrs) = splitContentByUrl($content);
    	
    	//获取压缩后的字符串
    	/*
    	if(!empty($urlStrs))$resultStrs = MD::app()->oauthService->weibo->getOriginUrl($urlStrs);
		else $resultStrs = array();
		*/
		
    	//组装返回结果
    	$i =0;$resultContent = '';
    	foreach($urlStrs as $url){
    		$resultContent .= $normalStrs[$i] . "<a target='_blank' href='$url'>". $url ."</a>";
    		$i ++;
    	}
    	$resultContent .= $normalStrs[$i];
    	
    	return $resultContent;
    }

    function addEmotion($text) {
		return preg_replace('/\[([a-z]*)\]/','<img style="border:none;float:none;height:12px;margin:0;padding:0;width:12px;display:inline" src="/site/themes/meiding/images/faces/$1.gif" alt="[$1]" title="$1"/>',$text);	
    }
    
    /**
     * 获取指定文件的svn版本号
     * @param fullpath $file
     */
    function getSvnVersion($file){
    	return 0; //FIXME
	}
	
	/**
	 * 替换对引号，反斜线
	 * @param  $str
	 */
	function md_escape_string($str){
		return str_replace(array("'",'"','\\'),array('‘','“','﹨'),$str);
	}
	
	/**
	 * 读取文件夹下的所有文件名
	 * @param string $dir 文件夹绝对路径
	 * @param string $prefix 要获取的文件名前缀,默认是null,表示不过滤
	 * @param boolean $exclude true表示剔除，false表示包含，默认是true
	 */
	function md_readdir($dir, $prefix = null, $exclude = true) {
		$files = array();
		if (is_dir($dir)) {
	        if ($dh = opendir($dir)) {
	                while( ($file = readdir($dh)) !== false) {
	                	if ($prefix != null) {
	                		if ($exclude) {
		                        if (strpos($file,$prefix) !== 0)
		                               $files[] = $file;
	                		} else {
      							if (strpos($file,$prefix) >= 0)
		                               $files[] = $file;
	                		}
	                	} else {
	                		$files[] = $file;
	                	}
	                }
	                closedir($dh);
	        }
		}
		return $files;
	}
	
	function getLastHourBeginTimeStamp($time) {
		return strtotime(date("Y-m-d H:00:00", $time))-3600;
	}
	
	function getLastDayBeginTimeStamp($time) {
		return strtotime(date("Y-m-d 00:00:00", $time))-3600*24;
	}
	
	/**
	 * 在explode的基础上，对每个元素进行了trim
	 */
	function md_explode($sep, $str) {
		if (!is_string($str) || $str === "") return array();
		$arr = explode($sep, $str);
		foreach($arr as &$i) {
			$i = trim($i);
		}
		return $arr;
	}
	
	/**
	 * 根据key把对象数组转变成map的形式
	 * @param array $objects
	 * @param string $key
	 * @param boolean $append 是否保留重复key的值。true表示保留，则最后map的值是数组形式
	 */
	function md_expand2array($objects, $key, $append = false) {
		$arr = array();
		foreach ($objects as $o) {
			if ($append) {
				if (isset($arr[$o->$key])) {
					$arr[$o->$key][] = $o;
				} else {
					$arr[$o->$key] = array();
					$arr[$o->$key][] = $o;
				}
			} else {
				$arr[$o->$key] = $o;
			}
		}
		return $arr;
	} 

?>
