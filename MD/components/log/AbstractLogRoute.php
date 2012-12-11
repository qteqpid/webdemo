<?php

abstract class AbstractLogRoute extends AbstractComponent {
	
	protected $levels; // 收集什么级别的log
	
	protected $categories; //收集什么分类的log
	
	private $_logs = array();
	
	/**
	 * 
	 * Enter description here ...
	 * @param CLogger $logger
	 * @param bool $dump
	 */
	public function collectLogs($logger, $dump) {
		$logs = $logger->getLogs($this->levels, $this->categories);
		$this->_logs = empty($this->_logs) ? $logs : array_merge($this->_logs, $logs);
		if ($dump && !empty($this->_logs)) {
			$this->processLogs($this->_logs);
			$this->_logs = array();
		}
		
	}
	
	abstract protected function processLogs($logs);
	
}
