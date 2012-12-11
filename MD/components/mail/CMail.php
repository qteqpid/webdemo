<?php 
require_once (dirname(__FILE__).DS.'mails'.DS.'AbstractMail.php');

class CMail extends AbstractComponent {
	
	public $result_str;
	
	protected $host;          //主机
	protected $port;          //端口一般为25
	protected $user;          //SMTP认证的帐号
	protected $pass;          //认证密码
	
	private $_conn;
	private $_socket;
	
	public function init() {
		$this->user = base64_encode($this->user);
		$this->pass = base64_encode($this->pass);
		$this->result_str = '';
	}

	/**
	 * 发送邮件
	 * @param AbstractMail $mail     邮件对象
	 */
	public function send($mail)
	{
		if (FALSE == $this->ping()) {
			$this->connect();
		}
		//以下是和服务器会话
		$this->docommand('EHLO', "EHLO HELO\r\n");
		$this->docommand('AUTH', "AUTH LOGIN\r\n");		
		$this->docommand('USER', $this->user."\r\n");
		$this->docommand('PASS', $this->pass."\r\n");
		
		if(FALSE == strpos($this->result_str,"235")){
		   $this->result_str .= "smtp auth error!";
		   return MAILINFO::AUTH_ERROR;
		}

		$this->docommand('FROM', "MAIL FROM:<".$mail->getFrom().">\r\n");
		$this->docommand('RCPT', "RCPT TO:<".$mail->getTo().">\r\n");
		$this->docommand('DATA', "DATA\r\n");
		$this->docommand('BODY', $mail->getContent()."\r\n.\r\n");
		
		if(FALSE == strpos($this->result_str,"250")){
		   $this->result_str .= "send email failed!";
		   return MAILINFO::SEND_ERROR;
		}

		//结束，关闭连接
		$this->docommand('QUIT', "QUIT\r\n");
		return MAILINFO::SUCCESS;
	}
	
	/**
	 * 生成邮件对象
	 * @param string $type 邮件类型。具体可以看MAILINFO::MAIL_{xxx}
	 * @param array $params 邮件参数
	 */
	public function createMail($type, $params = array()) {
		$clazz = $type;
		include_once (dirname(__FILE__).DS.'mails'.DS.$clazz.'.php');
		return new $clazz($params);
	}
	
	public function getError() {
		return $this->result_str;
	}
	/**
	 * 检查socket是否处于连接状态
	 */
	private function ping() {
		if ($this->_socket == null) return FALSE;
		try {
			socket_write ($this->_socket, "EHLO HELO\r\n", strlen ("EHLO HELO\r\n"));
			return socket_read ($this->_socket, 1024);
		} catch (CException $e) {
			return FALSE;
		}
	}
	
	/**
	 * socket连接smtp服务器
	 */
	private function connect() {
		$this->_socket = socket_create (AF_INET, SOCK_STREAM, SOL_TCP);
		if(!$this->_socket){
			return FALSE;
		}
		$this->_conn = socket_connect($this->_socket,$this->host,$this->port);
		if(!$this->_conn) {
			return FALSE;
		}
		$this->result_str .= "SERVER RESPONSE：<font color=#cc0000>".socket_read ($this->_socket, 1024)."</font><br/>";
		return TRUE;
	}
	
	/**
	 * 发送通信命令
	 * @param  $key 用于识别命令的key
	 * @param  $value 命令内容
	 */
	private function docommand($key, $value)
	{
		socket_write ($this->_socket, $value, strlen ($value));
		$this->result_str .= "[$key]SERVER RESPONSE：<font color=#cc0000>".socket_read ($this->_socket, 1024)."</font><br/>";
	}

}
