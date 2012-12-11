<?php 
require_once (dirname(__FILE__).DS.'mails'.DS.'AbstractMail.php');

class PHPMail extends AbstractComponent {
	
	protected $accounts;
	protected $cur_account;
	protected $pointer;
	
	public function init() {
		require_once ('libs/class.phpmailer.php');
		$this->pointer = 0;
		$this->cur_account = $this->accounts[$this->pointer];
	}
	
	/**
	 * 切换当前使用的邮箱账号
	 */
	public function switchAccount() {
		$this->pointer = ($this->pointer+1) % count($this->accounts);
		$this->cur_account = $this->accounts[$this->pointer];
		return $this;
	}
	
	/**
	 * 返回当前使用的邮箱账号信息
	 */
	public function current($key = null) {
		if ($key == null)
			return $this->cur_account;
		else
			return $this->cur_account[$key];
	}

	/**
	 * 发送邮件
	 */
	public function send($mail)
	{
		$phpmail = new PHPMailer(true);
		$phpmail->IsSMTP();
		$phpmail->SMTPDebug  = false;  // disable SMTP debug information (for log)
		$phpmail->Host       = $this->cur_account['host']; //主机
		$phpmail->Port       = $this->cur_account['port']; //端口一般为25
		$phpmail->Username   = $this->cur_account['user']; //SMTP认证的帐号
		$phpmail->Password   = $this->cur_account['pass']; //认证密码
		$phpmail->SMTPAuth   = true;
		$phpmail->CharSet   = "UTF-8";
		$phpmail->Encoding = "base64";
		$mail->setFrom($phpmail->Username);
		try {
			$phpmail->SetFrom($mail->getFrom(), MD::app()->siteName);
			$phpmail->AddAddress($mail->getTo());
			$phpmail->Subject = $mail->getSubject();
			//$phpmail->AltBody = "text/html";
			$phpmail->MsgHTML( $mail->getBody());
			//$phpmail->AddAttachment('images/phpmailer.gif');      // attachment
			$phpmail->Send();
			MD::log("[SUCCESS]Message Sent OK", CLogger::LEVEL_DEBUG, 'mail');
			return MAILINFO::SUCCESS;
		} catch (phpmailerException $e) {
			MD::log("[ERROR]".$e->errorMessage(), CLogger::LEVEL_ERROR, 'mail');
			return MAILINFO::SEND_ERROR;
		} catch (Exception $e) {
			MD::log("[ERROR]".$e->errorMessage(), CLogger::LEVEL_ERROR, 'mail');
			return MAILINFO::SEND_ERROR;
		}
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
	
}
