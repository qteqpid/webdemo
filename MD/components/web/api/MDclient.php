<?php

class MDclient {
	
	private $host;
	private $format; // 目前只支持json
	private $decode_json; // 是否需要把json格式数据decode成json对象
	private $useragent = 'MD OAuth2 v0.1';
	// 授权数据，先留着
	private $client_id;
	private $access_token;
	
	public function __construct() {
		$this->host = MD::app()->siteUrl."/api/";
		$this->format = 'json';
		$this->client_id = 1;
		$this->decode_json = true;
		$this->access_token = 1;
	}
	
	/**
	 * 获取用户关注的问题
	 * @param int $uid
	 */
	public function question_follow($uid) {
		$params = array();
		$params['uid'] = $uid;
		return $this->get('question/follow', $params);
	}
	
	/**
	 * get请求
	 * @param string $url 要请求的服务url，前面不带斜杠
	 * @param array $params 请求参数
	 */
	public function get($url, $params = array()) {
		$response = $this->oAuthRequest($url, 'GET', $params);
		if ($this->format === 'json' && $this->decode_json) {
			return json_decode($response, true);
		}
		return $response;
	}
	
	public function post($url, $params = array(), $multi = false) {
		$response = $this->oAuthRequest($url, 'POST', $params, $multi );
		if ($this->format === 'json' && $this->decode_json) {
			return json_decode($response, true);
		}
		return $response;
	}
	
	private function oAuthRequest($url, $method, $params, $multi = false) {
		$params['format'] = $this->format;
		$params['client_id'] = $this->client_id;
		if (strrpos($url, 'https://') !== 0 && strrpos($url, 'https://') !== 0) {
			$url = "{$this->host}{$url}";
		}
		switch ($method) {
			case 'GET':
				$url = $url . '?' . http_build_query($params);
				return $this->http($url, 'GET');
			default:
				$headers = array();
				if (!$multi && (is_array($params) || is_object($params))) {
					$body = http_build_query($params);
				} else {
					throw new CException("暂不支持上传文件");
				}
				return $this->http($url, $method, $body, $headers);
		}
	}
	
	private function http($url, $method, $postfields = NULL, $headers = array()) {
		$ci = curl_init();
		/* Curl settings */
		curl_setopt($ci, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_0);
		curl_setopt($ci, CURLOPT_USERAGENT, $this->useragent);
		curl_setopt($ci, CURLOPT_CONNECTTIMEOUT, 10);
		curl_setopt($ci, CURLOPT_TIMEOUT, 10);
		curl_setopt($ci, CURLOPT_RETURNTRANSFER, TRUE); //return the transfer as a string of the return value of curl_exec() instead of outputting it out directly
		curl_setopt($ci, CURLOPT_ENCODING, ""); // a header containing all supported encoding types is sent
		curl_setopt($ci, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ci, CURLOPT_HEADERFUNCTION, array($this, 'getHeader'));
		curl_setopt($ci, CURLOPT_HEADER, FALSE);

		switch ($method) {
			case 'POST':
				curl_setopt($ci, CURLOPT_POST, TRUE);
				if (!empty($postfields)) {
					curl_setopt($ci, CURLOPT_POSTFIELDS, $postfields);
					$this->postdata = $postfields;
				}
				break;
			case 'DELETE':
				curl_setopt($ci, CURLOPT_CUSTOMREQUEST, 'DELETE');
				if (!empty($postfields)) {
					$url = "{$url}?{$postfields}";
				}
		}

		if ( isset($this->access_token) && $this->access_token )
			$headers[] = "Authorization: OAuth2 ".$this->access_token;

		curl_setopt($ci, CURLOPT_URL, $url );
		curl_setopt($ci, CURLOPT_HTTPHEADER, $headers );
		curl_setopt($ci, CURLINFO_HEADER_OUT, TRUE );

		$response = curl_exec($ci);
		curl_close ($ci);
		return $response;
	}

	private function getHeader($ch, $header) {
		$i = strpos($header, ':');
		if (!empty($i)) {
			$key = str_replace('-', '_', strtolower(substr($header, 0, $i)));
			$value = trim(substr($header, $i + 2));
			$this->http_header[$key] = $value;
		}
		return strlen($header);
	}
}
