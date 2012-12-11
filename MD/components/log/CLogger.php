<?php

class CLogger extends AbstractComponent {
	
	const LEVEL_ERROR   = 'error';
	const LEVEL_WARNING = 'warning';
	const LEVEL_INFO    = 'info';
	const LEVEL_DEBUG   = 'debug'; // 调试输出
	const LEVEL_CRON   = 'cron';  // 定时任务的log
	const LEVEL_PROFILE   = 'profile'; // 性能相关的log
	const LEVEL_DATA   = 'data'; // ？？
	const LEVEL_APP   = 'app'; // 应用相关的log
	const LEVEL_SEARCH   = 'search'; // 搜索相关的log
	
	protected $poolSize = 1000; // log池的容量，超过将flush log到各个LogRouter里
	
	private $_logPool = array();
	
	private $_logCount = 0;  // 当前logPool里的log数
	
	private $_levels;
	
	private $_categories;
	
	public function log($msg, $level, $category) {
		$this->_logPool[] = array($msg,$level,$category,microtime(TRUE));
		$this->_logCount++;
		if ($this->_logCount >= $this->poolSize) {
			$this->flush();
		}
	}
	
	/**
	 * 从logPool里找出对应level和category的log返回
	 * @param const CLogger::LEVEL_* $levels
	 * @param string $categories
	 */
	public function getLogs($levels, $categories) {
		$this->_levels=preg_split('/[\s,]+/',strtolower($levels),-1,PREG_SPLIT_NO_EMPTY);
		$this->_categories=preg_split('/[\s,]+/',strtolower($categories),-1,PREG_SPLIT_NO_EMPTY);
		if(empty($levels) && empty($categories))
			return $this->_logPool;
		else if(empty($levels))
			return array_values(array_filter(array_filter($this->_logPool,array($this,'filterByCategory'))));
		else if(empty($categories))
			return array_values(array_filter(array_filter($this->_logPool,array($this,'filterByLevel'))));
		else
		{
			$ret=array_values(array_filter(array_filter($this->_logPool,array($this,'filterByLevel'))));
			return array_values(array_filter(array_filter($ret,array($this,'filterByCategory'))));
		}
	}
	
	private function filterByCategory($log)
	{
		foreach($this->_categories as $category) {
			$cat=strtolower($log[2]);
			if($cat===$category || (($c=rtrim($category,'.*'))!==$category && strpos($cat,$c)===0))
				return $log;
		}
		return FALSE;
	}
	
	private function filterByLevel($log)
	{
		return in_array(strtolower($log[1]),$this->_levels) ? $log : FALSE;
	}
	/**
	 * 清空log池
	 * @param array $params
	 */
	public function flush($params = array()) {
		$this->onFlush(new CEvent($this, $params));
		$this->_logPool = array();
		$this->_logCount = 0;
	}
	
	/**
	 * 触发事件处理器
	 * @param CEvent $event 事件对象
	 */
	public function onFlush($event) {
		$this->raiseEvent('onFlush', $event);
	}
}
