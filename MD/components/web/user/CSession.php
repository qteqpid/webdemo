<?php

class CSession extends AbstractComponent {

	public function init() {	
		$cookie_domain = MD::app()->cookie->cookie_domain();
		ini_set( 'session.name',			$this->my_session_name($cookie_domain));
		ini_set( 'session.cache_expire',		300);
		ini_set( 'session.cookie_lifetime',		0);
		ini_set( 'session.cookie_path',		   '/');
		ini_set( 'session.cookie_domain',		$cookie_domain);
		ini_set( 'session.cookie_httponly',		1);
		ini_set( 'session.use_only_cookies',	1);
		ini_set( 'session.gc_maxlifetime',		10800);
		ini_set( 'session.gc_probability',		1);
		ini_set( 'session.gc_divisor',		1000);
		@session_start();
		register_shutdown_function(array($this, 'close'));
	}

	private function my_session_name($domain) {
		return str_replace(array('.','-'), '', $domain);
	}

	/**
	 * 彻底清除session
	 */
	public function destory() {
		if (session_id() !== '') {
			@session_unset(); //清除内存
			@session_destroy(); //清除文件
		}
	}

	/**
	 * 清空session
	 */
	public function clear() {
		foreach(array_keys($_SESSION) as $key) {
			unset($_SESSION[$key]);
		}
	}

	/**
	 * 关闭session
	 */
	public function close() {
		@session_write_close();
	}
}
