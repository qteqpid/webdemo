<?php
/**
 * 邀请好友的邮件
 * @author qteqpid
 */
class CMailInvitation extends AbstractMail {
	
	/**
	 * 传递给这个类的参数中必须含有to, inviteLink,friendName
	 */
	protected $inviteLink; // 邀请链接
	
    protected $to;     
    
    protected $friendName;  // 邀请你的好友昵称
    
	private $_template = "你好：<br/>
						你的好友邀请你加入{siteName}，请点击下面的链接开始你的注册过程(链接有效期为1周)。<br/>
						<a href=\"{invite_link}\">{invite_link}</a><br/>";
	
	public function init() {
		$this->checkValid(array('inviteLink', 'to', 'friendName'));
	}
	
	public function getBody() {
		$content = str_replace(
						array('{siteName}','{invite_link}','{siteUrl}'), 
						array($this->siteName, $this->inviteLink, $this->siteUrl), 
						$this->_template);
		return str_replace('{content}',$content,$this->tpl);						
	}
	
	public function getSubject() {
		return $this->friendName.'邀请你加入'.$this->siteName;
	}
}
