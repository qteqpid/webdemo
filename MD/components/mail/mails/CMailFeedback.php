<?php
/**
 * 用户反馈的邮件
 * @author qteqpid
 */
class CMailFeedback extends AbstractMail {
	
	/**
	 * 传递给这个类的参数中必须含有$reporter、$comments、$reporter_email
	 */
	protected $reporter;
    protected $reporter_email;
	protected $comments;
    
	private $_template = "<table>
	                       	<tr><td>反馈人:</td><td>{reporter}</td></tr>
    	                   	<tr><td>邮箱:</td><td>{reporter_email}</td></tr>
        	                <tr><td>内容:</td><td>{comments}</td></tr>
               		      </table>";
	
	public function init() {
		$this->to = 'feedback@imeiding.com';
		$this->checkValid(array('reporter', 'reporter_email', 'comments'));
	}
	
	public function getBody() {
		$content = str_replace(
						array('{reporter}','{reporter_email}','{comments}'), 
						array($this->reporter, $this->reporter_email, $this->comments), 
						$this->_template);
		return str_replace('{content}',$content,$this->tpl);						
	}
	
	public function getSubject() {
		return '社区质量反馈';
	}
}
