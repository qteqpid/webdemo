<?php
/**
 * 忘记密码的邮件
 * @author qteqpid
 */
class CMailSigninForget extends AbstractMail {
	
	/**
	 * 传递给这个类的参数中必须含有必须有 to, resetLink
	 */
	protected $resetLink;
	
    protected $to;
    
	private $_template = "你好：<br/>
						请点击下面的链接重置您在{siteName}的账户密码。<br/>
						<a href=\"{reset_link}\">{reset_link}</a><br/>
						如果您本人从未在本站发起过密码重置请求，请直接忽略本邮件。<br/>";
	
	public function init() {
		$this->checkValid(array('resetLink', 'to'));
	}
	
	public function getBody() {
		$content = str_replace(
						array('{siteName}','{reset_link}','{siteUrl}'), 
						array($this->siteName, $this->resetLink, $this->siteUrl), 
						$this->_template);
		return str_replace('{content}',$content,$this->tpl);
	}
	
	public function getSubject() {
		return '来自'.$this->siteName.'的密码重置邮件';
	}
}
