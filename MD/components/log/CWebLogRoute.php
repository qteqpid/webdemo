<?php

/**
 *  将logs显示在页面底部
 * @author qteqpid
 */
class CWebLogRoute extends AbstractLogRoute {
	
	/**
	 * 处理CLogger发送过来的日志
	 * (non-PHPdoc)
	 * @see lastMeiding/MD/components/log/AbstractLogRoute::processLogs()
	 */
	public function processLogs($logs) {
		//if (!ONLINE && MD_DEBUG ){
		global $debug_ips;
		if (!ONLINE && MD_DEBUG && !MD::app()->request->isAjaxRequest() || (isset($debug_ips) && in_array(MD::app()->request->getRemoteAddr(), $debug_ips))){
			$logViewRender = new LogViewRender();
			$logViewRender->renderPage("log.php",$logs);
		}
	}
}

/**
 * 
 * LogViewRender用来渲染，webApp错误的情况下，应当显示的视图
 * @author instreet
 *
 */
class LogViewRender extends AbstractViewRender{
	
	/**
	 * 
	 * 重写父类render函数，渲染页面
	 */
	protected function render(){
		$data = $this->data;
		
		$MDViewPath = MD::getRealPath(MD::app()->params['MDViewPath'],true);
		require_once ($MDViewPath. $this->view);
	}
}
