<?php

abstract class AbstractMail extends AbstractComponent {

	const MAIL_SEPERATOR = "\r\n";
	  
	protected $from;  // 邮件发送者
	
	protected $to;   // 邮件接收人
	
	protected $subject; // 邮件标题
	
	protected $body;  // 邮件正文
	
	protected $charset = 'UTF-8'; // 邮件字符编码
	
	protected $type = 'text/html';  // 邮件类型
	
	protected $siteName; // 网站名
    
    protected $siteUrl; // 网站链接
    
    protected $tpl = 'mail.notify.tpl';
    
    public function __construct($params) {
    	parent::__construct($params);
    	$this->siteName = MD::app()->siteName;
    	$this->from = MD::app()->mail->current('user'); //"service@imeiding.com"; 
    	$this->siteUrl  = MD::app()->siteUrl;
    	$this->tpl = readFileToString(dirname(__FILE__).'/'.$this->tpl);
    }
	
	public function getFrom() {
		return $this->from;
	}
	
	public function setFrom($from) {
		$this->from = $from;
	}
	
	public function getTo() {
		return $this->to;
	}
	
	public function setTo($to) {
		$this->to = $to;
	}
	
	public function getContent() {
		$header = $this->getHeader();
		return $header.self::MAIL_SEPERATOR.self::MAIL_SEPERATOR.$this->getBody();
	}
	
	/**
	 * 获取邮件正文，该函数必须在子类中实现
	 */
	abstract public function getBody();
	
	/**
	 * 获取邮件标题，该函数必须在子类中实现
	 */
	abstract public function getSubject();
	
	/**
	 * 获取邮件头
	 */
	public function getHeader() {
		$subject = '=?UTF-8?B?'.base64_encode($this->getSubject()).'?=';
		$header = 'From:'.$this->from.self::MAIL_SEPERATOR.
				  'To:'.$this->to.self::MAIL_SEPERATOR.
				  'Subject:'.$subject.self::MAIL_SEPERATOR.
				  'Content-Type: '.$this->type."; charset=".$this->charset.self::MAIL_SEPERATOR;
		return $header;
	}

	/**
	 * 检查所需的变量是否都有了
	 */
	public function checkValid($checkParams) {
		foreach ($checkParams as $param) {
			if ($this->$param === NULL) {
				throw new CException('Param "'.$param.'" is NOT SET!');
			}
		}
	}
	
}
