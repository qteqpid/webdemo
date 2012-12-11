<?php

/**
 * 用memcache来实现session
 * @author: Qteqpid
 */
class CCacheSession extends CSession {
	
	protected function getSaveHandler() {
		return 'memcache';	
	}
	
	protected function getSavePath() {
		return 'tcp://192.168.72.73:12000/';
	}
}
