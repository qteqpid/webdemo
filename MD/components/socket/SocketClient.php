<?php

class SocketClient {
	private $_host;
	private $_port;
	private $_socket;
	
	public function __construct($remote_host = '127.0.0.1', $remote_port = 1000) {
		$this->_host = $remote_host;
		$this->_port = $remote_port;
		if (!function_exists('socket_create')) {
			throw new Exception('socket_create函数不存在，没找到sockets模块?');
		}
	}
	
	public function connect() {
		if (!($this->_socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP))) {
			throw new Exception("socket_create() failed:".socket_strerror ($this->_socket));
		}
		if (!($sc = socket_connect($this->_socket, $this->_host, $this->_port))) {
			throw new Exception("socket_connect() failed:".socket_strerror ($sc));
		}
		return true;
	}
	
	public function send($message) {
		if (!is_string($message)){
			throw new Exception("socket msg must be string type");
		}
		$formattedMessage = $this->formatMessage($message);
		socket_write ($this->_socket, $formattedMessage, strlen ($formattedMessage));
	}
	
	private function formatMessage($message) {
		$len = strlen($message);
		return "#BE{$len}#$message#END#";	
	}
	
	public function close() {
		if ($this->_socket) @socket_close($this->_socket);
	}
	
	public function __destruct() {
		@socket_close($this->_socket);
	}
}