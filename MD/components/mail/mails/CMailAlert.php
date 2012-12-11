<?php
/**
 * 报警邮件
 * @author qteqpid
 */
class CMailAlert extends AbstractMail {
	
	/**
	 * 传递给这个类的参数中必须含有to, content, title
	 */
	protected $content;
	
	protected $title;
	
    protected $to;
    
	public function init() {
		$this->checkValid(array('content', 'title', 'to'));
	}
	
	public function getBody() {
		$content = $this->content;
		return $content;
	}
	
	public function getSubject() {
		return $this->siteName.$this->title;
	}
}
