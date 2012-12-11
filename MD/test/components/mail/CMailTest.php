<?php
/**
 * mail组件的测试类
 * @author qteqpid
 *
 */
class CMailTest extends AbstractTestCase {
	/**
	 * 邀请邮件测试
	 */
	public function testSendInvite() { 
		$mail = MD::app()->mail;
		$this->assertNotEquals(FALSE, $mail, '邮件初始化失败');
		
		$params = array(
			'inviteLink' => 'testLink',
			'to' => 'noreply@imeiding.com',
			'friendName' => 'phpunit',
		);
		$obj = $mail->createMail(MAILINFO::MAIL_INVITATION, $params);
		$this->assertTrue(($obj instanceof AbstractMail), 'createMail对象不是AbstractMail实例');
		
		$retCode = $mail->send($obj);
		$this->assertEquals($retCode, MAILINFO::SUCCESS, 'invite邮件发送失败');
	}

	/**
	 * 通知邮件测试
	 */
	public function testSendNoti() { 
		$mail = MD::app()->mail;
		$this->assertNotEquals(FALSE, $mail, '邮件初始化失败');
		
		$params = array(
			'subject' => 'this is subject',
			'message' => 'this is a message',
			'to' => 'noreply@imeiding.com',
			'nickName' => 'phpunit',
		);
		$obj = $mail->createMail(MAILINFO::MAIL_NOTIFICATION, $params);
		$this->assertTrue(($obj instanceof AbstractMail), 'createMail对象不是AbstractMail实例');
		
		$retCode = $mail->send($obj);
		$this->assertEquals($retCode, MAILINFO::SUCCESS, 'noti邮件发送失败');
	}
	
	/**
	 * 激活邮件测试
	 */
	public function testSendActi() { 
		$mail = MD::app()->mail;
		$this->assertNotEquals(FALSE, $mail, '邮件初始化失败');
		
		$params = array(
			'activeLink' => 'testLink',
			'to' => 'noreply@imeiding.com',
		);
		$obj = $mail->createMail(MAILINFO::MAIL_ACTIVATION, $params);
		$this->assertTrue(($obj instanceof AbstractMail), 'createMail对象不是AbstractMail实例');
		
		$retCode = $mail->send($obj);
		$this->assertEquals($retCode, MAILINFO::SUCCESS, 'active邮件发送失败');
	}
	
	/**
	 * 忘记密码邮件测试
	 */
	public function testSendSigninfo() { 
		$mail = MD::app()->mail;
		$this->assertNotEquals(FALSE, $mail, '邮件初始化失败');
		
		$params = array(
			'resetLink' => 'testLink',
			'to' => 'noreply@imeiding.com',
		);
		$obj = $mail->createMail(MAILINFO::MAIL_SIGNINFORG, $params);
		$this->assertTrue(($obj instanceof AbstractMail), 'createMail对象不是AbstractMail实例');
		
		$retCode = $mail->send($obj);
		$this->assertEquals($retCode, MAILINFO::SUCCESS, 'signinfo邮件发送失败');
	}
}