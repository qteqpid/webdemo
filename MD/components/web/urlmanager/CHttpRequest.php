<?php

class CHttpRequest extends AbstractComponent {

	/*
	 * 以下是类外部可以使用的常量
	 */
	const REQUEST_POST = 0;
	const REQUEST_GET = 1;
	
	/*
	 * 以下是类内部使用的属性
	 */
	private $_uri;
	private $_method;

	/*
	 * 获取请求中的uri
	 */
	public function getRequestUri() {
		if (empty($this->_uri))
			$this->_uri = $_SERVER['REQUEST_URI'];
		return $this->_uri;
	}

	/*
	 * 获取客户端ip
	 */
	public function getRemoteAddr() {
		return isset($_SERVER['REMOTE_ADDR'])?$_SERVER['REMOTE_ADDR']:'127.0.0.1';
	}

	/*
	 * 获取请求类型. 目前只支持post和get
	 */
	public function getRequestMethod() {
		if (empty($this->_method))
			$this->_method = $_SERVER['REQUEST_METHOD'];
		switch($this->_method){
			case 'POST':
				return self::REQUEST_POST;
			case 'GET':
				return self::REQUEST_GET;
		}
	}
	
	public function getUrl() {
		return $this->getRequestUri();	
	}

	/**
	 * 重定向
	 */
	public function redirect($url) {
		if (strpos($url, '/') === 0) {
			$url = $this->getHost().$url;
		}
		header('Location: '.$url, TRUE, 302);
		MD::app()->end();
	}

	public function getHost() {
		return 'http://'.$_SERVER['HTTP_HOST'];	
	}

	public function isAjaxRequest() {
		return isset($_GET['_UPLOAD_PIC']) || isset($_GET['_PLUGIN_REQUEST']) || isset($_GET['_MD_AJAX']) || (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH']==='XMLHttpRequest');
	}

	public function getParam($name, $defaultValue=null) {
		return isset($_GET[$name]) ? $_GET[$name] : 
			(isset($_POST[$name]) ? $_POST[$name] : $defaultValue);
	}

	public function getQuery($name, $defaultValue=null) {
		return isset($_GET[$name]) ? $_GET[$name] : $defaultValue;
	}

	public function getPost($name, $defaultValue=null){
		return isset($_POST[$name]) ? $_GET[$name] : $defaultValue;
	}
	
	
	public static function sendHttpRequestUsingPOST($url, $post_data, $sync = FALSE) {
        $url2 = parse_url($url);
        isset($url2["port"]) or $url2["port"] = 80;
        isset($url2["query"]) or $url2["query"] = "";
        isset($url2["fragment"]) or $url2["fragment"] = "";
        isset($url2["path"]) or $url2["path"] = "/";
        $url2["path"] = ($url2["path"] == "" ? "/" : $url2["path"]);
        $url2["port"] = ($url2["port"] == "" ? 80 : $url2["port"]);
        $fsock_timeout=20;//秒
        if(!($fsock = fsockopen($url2["host"], $url2["port"], $errno, $errstr, $fsock_timeout))){
            return false;
        }
        $request = $url2["path"] . ($url2["query"] != "" ? "?" . $url2["query"] : "") . ($url2["fragment"] != "" ? "#" . $url2["fragment"] : "");

        $needChar = false;
        $post_data2 = "";

        foreach($post_data as $key => $val) {                                                                             
            $post_data2 .= ($needChar ? "&" : "") . urlencode($key) . "=" . urlencode($val);                              
            $needChar = true;
        }
        $in = "POST " . $request . " HTTP/1.1\r\n";
        $in .= "Accept: */*\r\n";      
        $in .= "Host: " . $url2["host"] . "\r\n";
	        $in .= "Content-type: application/x-www-form-urlencoded\r\n";
        $in .= "Content-Length: " . strlen($post_data2) . "\r\n";
        $in .= "Connection: Close\r\n\r\n";
        $in .= $post_data2 . "\r\n\r\n";

        unset($post_data2);
        if(!@fwrite($fsock, $in, strlen($in))){
            fclose($fsock);
            return false;
        }
        unset($in);
	fflush($fsock);
        if ($sync) {
        	while (trim(fgets($fsock,4096))){};
	        $data = "";
			while (!feof($fsock)) {
				$data .= fgets($fsock,4096);
			}
			fclose($fsock);
			return $data;
        }
	fgets( $fsock, 12 );
        fclose($fsock);
        return true;
    }   

    
	public static function send( $dst_url, $image_id, $remote_url, $port ) {
		$ch = curl_init ( $dst_url );
		curl_setopt ( $ch, CURLOPT_HTTPHEADER, array ( 'Connection: Close' ) );
		curl_setopt ( $ch, CURLOPT_HEADER, FALSE );
		curl_setopt ( $ch, CURLOPT_NOBODY, false );
		curl_setopt ( $ch, CURLOPT_CONNECTTIMEOUT, 0 );
		curl_setopt ( $ch, CURLOPT_TIMEOUT, 0 );
		curl_setopt ( $ch, CURLOPT_RETURNTRANSFER, true );
		curl_setopt ( $ch, CURLOPT_POST, true );
		
		$data = array ( 'remote_url' => $remote_url,
			'image_id' => $image_id,
			'port' => $port );
		
		curl_setopt ( $ch, CURLOPT_POSTFIELDS, $data );
		$response = curl_exec ( $ch );
		
		curl_close ( $ch );
		
		return json_decode( $response );
	}
}
