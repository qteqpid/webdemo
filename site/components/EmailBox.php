<?php

/**
 * 邮箱相关的一些功能
 * @author qteqpid
 */

class EmailBox {
	
	/**
	 * 根据email地址形态获得对应的邮箱url链接
	 * @param string $email
	 */
	public static function getEmailUrl($email) {
		$email_info = explode("@", $email);
		switch ($email_info[1]) {      
		    case "qq.com"    : $email_url = "mail.qq.com";break;
		    case "163.com"   : $email_url = "mail.163.com";break;
		    case "126.com"   : $email_url = "mail.126.com";break;
		    case "gmail.com" : $email_url = "mail.google.com";break;
		    case "hotmail.com" : $email_url = "mail.live.com";break;
		    default          : $email_url = "mail.".$email_info[1];
		}
		return 'http://'.$email_url;
	}
	
}
