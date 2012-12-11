<?php 
require_once (dirname(__FILE__).DS.'mails'.DS.'AbstractMail.php');

class XMail extends AbstractComponent {
	
	/**
	 * 生成邮件对象
	 * @param string $type 邮件类型。具体可以看MAILINFO::MAIL_{xxx}
	 * @param array $params 邮件参数
	 */
	public function createMail($type, $params = array()) {
		$clazz = $type;
		include_once (dirname(__FILE__).DS.'mails'.DS.$clazz.'.php');
		return new $clazz($params);
	}
	
	public function send($mail)
    {
        $crlf   = "\n";
        $boundary   = '=_Part_'.md5(time().rand(0,9999999999));
        preg_match('/^(.*)(\<(.*)\>)?$/iuU', $mail->getFrom(), $m);//$C->SITE_TITLE.' <'.$C->SYSTEM_EMAIL.'>';
        $from_mail  = trim($m[3]);
        $from_name  = trim($m[1]);
        $tmp    = empty($from_name) ? $from_mail : ( '=?UTF-8?B?'.base64_encode($from_name).'?= <'.$from_mail.'>' );
        $headers    = '';
        $headers    .= 'From: '.$tmp.$crlf;
        $headers    .= 'Reply-To: '.$tmp.$crlf;
        $headers    .= 'Return-Path: '.$tmp.$crlf;
        $headers    .= 'Message-ID: <'.time().rand(1000,9999).'@'.MD::app()->domain.'>'.$crlf;
        $headers    .= 'X-Mailer: PHP/'.PHP_VERSION.$crlf;
        $headers    .= 'MIME-Version: 1.0'.$crlf;
        $headers    .= 'Content-Type: multipart/alternative; boundary="'.$boundary.'"'.$crlf;
        $headers    .= '--'.$boundary.$crlf;
        $headers    .= 'Content-Type: text/html; charset=UTF-8'.$crlf;
        $headers    .= 'Content-Transfer-Encoding: base64'.$crlf;
        $headers    .= 'Content-Disposition: inline'.$crlf;
        $headers    .= $crlf;
        $headers    .= chunk_split(base64_encode($mail->getBody()), 76, $crlf);
        $subject    = '=?UTF-8?B?'.base64_encode($mail->getSubject()).'?=';
        $result = @mail( $mail->getTo(), $subject, '', $headers );
        if( ! $result ) {
            // if mail is not accepted for delivery by the MTA, try something else:
            $headers    = '';
            $headers    .= 'From: '.$tmp.$crlf;
            $headers    .= 'Reply-To: '.$tmp.$crlf;
            $headers    .= 'Return-Path: '.$tmp.$crlf;
            $headers    .= 'Message-ID: <'.time().rand(1000,9999).'@'.MD::app()->domain.'>'.$crlf;
            $headers    .= 'X-Mailer: PHP/'.PHP_VERSION.$crlf;
            $headers    .= 'MIME-Version: 1.0'.$crlf;
            $headers    .= 'Content-Type: multipart/alternative; boundary="'.$boundary.'"'.$crlf;
            $headers    .= '--'.$boundary.$crlf;
            $headers    .= 'Content-Type: text/html; charset=UTF-8'.$crlf;
            $headers    .= 'Content-Transfer-Encoding: base64'.$crlf;
            $headers    .= 'Content-Disposition: inline'.$crlf;
            $headers    .= chunk_split(base64_encode($mail->getBody()), 76, $crlf);
            $result = @mail( $mail->getTo(), $subject, '', $headers );
        }
        return ($result == TRUE) ? MAILINFO::SUCCESS : MAILINFO::SEND_ERROR;
    }

}
