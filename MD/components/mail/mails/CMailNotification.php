<?php
/**
 * 普通通知的邮件
 * @author qteqpid
 */
class CMailNotification extends AbstractMail {
	
	/**
	 * 传递给这个类的参数中必须含有to, nickName,subject, message
	 */
	protected $nickName;
	
    protected $to;
    
    protected $subject;
    
    protected $message;
    
	private $_template = "嘿, {nickName}：<br/>
						{body}<br/>";
	
	public function init() {
		$this->checkValid(array('nickName', 'to', 'subject', 'message'));
	}
	
	public function getBody() {
		$content = str_replace(
						array('{siteName}','{nickName}','{siteUrl}','{body}'), 
						array($this->siteName, $this->nickName, $this->siteUrl, $this->message), 
						$this->_template);
		return str_replace('{content}',$content,$this->tpl);
	}
	
	public function getSubject() {
		return $this->subject;
	}
}
