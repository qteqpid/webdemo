<?php
/**
 * 注册后账户激活的邮件
 * @author qteqpid
 */
class CMailActivation extends AbstractMail {
	
	/**
	 * 传递给这个类的参数中必须含有to, activeLink
	 */
	protected $activeLink;
	
    protected $to;
    
    protected $tpl = 'mail.activation.tpl';
    
	private $_template = "感谢你注册{siteName}!<br/>
						你的登录名为:{email}<br/>
						请马上点击以下注册确认链接，激活你的{siteName}帐号！<br/>
						<a href=\"{active_link}\">{active_link}</a><br/>
						如果以上链接无法访问，请将该网址复制并粘贴至新的浏览器窗口中。<br/><br/>";
	
	public function init() {
		$this->checkValid(array('activeLink', 'to'));
	}
	
	public function getBody() {
		$content = str_replace(
						array('{siteName}','{active_link}','{siteUrl}','{email}'), 
						array($this->siteName, $this->activeLink, $this->siteUrl, $this->to), 
						$this->_template);
		return str_replace('{content}',$content,$this->tpl);						
	}
	
	public function getSubject() {
		return $this->siteName.'开通确认！';
	}
}
