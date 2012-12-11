<?php
/**
 * socket接收端。
 * log格式是：
 * #BE{len of body}#{body}#END#
 * @author qteqpid
 *
 */
class SocketServer {
	private $_host;
	private $_port;
	private $_socket;
	private $_eventHandler;
	
	/**
	 * @param string $remote_host 要监听的服务器ip地址，比如192.168.1.7
	 * @param mixed $remote_port 要监听的服务器端口
	 * @throws Exception 无sockets模块支持
	 */
	public function __construct($remote_host = '127.0.0.1', $remote_port = 1000) {
		DEBUG && debug(__CLASS__,__LINE__,"初始化SocketServer");
		$this->_host = $remote_host;
		$this->_port = $remote_port;
		if (!function_exists('socket_create')) {
			throw new Exception('socket_create函数不存在，没找到sockets模块?');
		}
	}
	
	public function setEventHandler($eh) {
		$this->_eventHandler = $eh;
	}
	
	public function connect() {
		DEBUG && debug(__CLASS__,__LINE__,"SocketServer连接$this->_host:$this->_port...",false);
		if (!($this->_socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP))) {
			throw new Exception("socket_create() failed:".socket_strerror ($this->_socket));
		}
		if (!($bind = socket_bind($this->_socket, $this->_host, $this->_port))) {
			throw new Exception("socket_bind() failed:".socket_strerror ($bind));
		}
		if (!($sl = socket_listen($this->_socket))) {
			throw new Exception("socket_listen() failed:".socket_strerror ($sl));
		}
		DEBUG && debug(__CLASS__,__LINE__,"OK");
		return true;
	}
	
	public function receive() {
		while(true) {
			if (!($socket_resource = socket_accept($this->_socket))) {
				throw new Exception("socket_accept() failed:".socket_strerror ($socket_resource));
			}
			DEBUG && debug(__CLASS__,__LINE__,"SocketServer收到一个新socket连接\n");
			while(true) {
				/**
				 * 一条log的完整格式：
				 * #BE5#xxxxx#END#          
				 * 其中#BE{num}#是log头，#END#是log尾,  num是log体的长度
				 * @var unknown_type
				 */
				$message = trim(socket_read($socket_resource, 1024));
				if (!$message) 
					break;
				if (preg_match_all("/#BE([0-9]+)#(.*?)#END#/", $message, $matches, PREG_SET_ORDER)) {
					//若log是中间断了，则会丢失1个log
					foreach ($matches as $ms) {
						if ($ms[1] == strlen($ms[2])) {
							$this->dealWith($ms[2]);
						}
					}
				}
			}
		}
	}
	
	public function dealWith($message) {
		if ($this->_eventHandler)
			$this->_eventHandler->dispatch($message);
		else
			echo $message;
	}
	
	public function __destruct() {
		socket_close($this->_socket);
	}
}