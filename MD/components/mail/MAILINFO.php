<?php

class MAILINFO {
	/**
	 * 邮件类型
	 */
	const MAIL_NOTIFICATION = 'CMailNotification';
	const MAIL_INVITATION   = 'CMailInvitation' ;
	const MAIL_ACTIVATION   = 'CMailActivation';
	const MAIL_SIGNINFORG   = 'CMailSigninForget';
	const MAIL_FEEDBACK   	= 'CMailFeedback';
	const MAIL_ACTIVITY   	= 'CMailActivity';
	const MAIL_ALERT    	= 'CMailAlert';
	const MAIL_WEEKLY    	= 'CMailWeekly';

	/**
	 * 发送结果状态
	 */
	const SUCCESS    = 0;
	const AUTH_ERROR = 1;
	const SEND_ERROR = 2;
}