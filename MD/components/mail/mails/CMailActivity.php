<?php
/**
 * 抽奖邮件
 * @author qteqpid
 */
class CMailActivity extends AbstractMail {
	
	/**
	 * 传递给这个类的参数中必须含有to, number
	 */
	protected $number; // 邀请链接
	
    protected $to;     
    
	private $_template = "您好：<br/>
						感谢您参加{siteName}的80后活动，您的抽奖号是{number}。<br/>
						抽奖将于1月29日进行，届时我们将在<a href=\"{siteUrl}\">{siteName}</a>(www.imeiding.com)						
						以及<a href=\"http://weibo.com/u/2546960673\">官方微博</a>公布获奖名单，敬请关注！<br/><br/>";
	
	public function init() {
		$this->checkValid(array('number', 'to'));
	}
	
	public function getBody() {
		$content = str_replace(
						array('{siteName}','{number}','{siteUrl}'), 
						array($this->siteName, $this->number, $this->siteUrl), 
						$this->_template);
		return str_replace('{content}',$content,$this->tpl);						
	}
	
	public function getSubject() {
		return $this->siteName.' -- 80后活动';
	}
}
