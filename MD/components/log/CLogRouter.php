<?php

class CLogRouter extends AbstractComponent {
	
	public $logRoutes = array();
	private $_logRouters = array();
	
	public function init() {
		foreach ($this->logRoutes as $logRoute) {
			$clazz = $logRoute['clazz'];
			$params =$logRoute['params'];
			
			$router = new $clazz($params);
			$this->_logRouters[] = $router;
		}
		// 给CLogger装上onFlush事件的处理器，当CLogger触发onFlush事件时请求该类的collectLogs函数
		MD::getLogger()->attachEventHandler('onFlush', array($this, 'collectLogs'));
		// 当app执行onEnd时调用此处理器
		MD::app()->attachEventHandler('onEndRequest', array($this, 'processLogs'));
	}
	
	/**
	 * 收集从CLogger onFlush事件发过来的logs
	 * @param Event $event
	 */
	public function collectLogs($event) {
		$logger = MD::getLogger();
		foreach($this->_logRouters as $router) {
			$router->collectLogs($logger, TRUE);
		}		
	}
	
	/**
	 * 收集并保存从MD onEnd事件发过来的logs
	 * @param Event $event
	 */
	public function processLogs($event = NULL) {
		$logger = MD::getLogger();
		foreach($this->_logRouters as $router) {
			$router->collectLogs($logger, TRUE);
		}		
	}

	public function flush() {
		$this->processLogs();
	}
}
