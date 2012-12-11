<?php

abstract class AbstractFilter extends AbstractComponent {
	
	protected $successor = NULL;
	
	public $action;
	
	public function __construct($params = array(), $action = NULL) {
		$this->setRules($params);
		$this->action = $action;
	}
	
	public function setSuccessor($filter) {
		$this->successor = $filter;
	}
	
	public function getSuccessor() {
		return $this->successor;
	}
	
	/**
	 * 运行filter检查
	 * @return 检查ok返回TRUE，否则返回FAlSE
	 */
	public function run() {
		//TODO
		$res = $this->checkValidate();
		if ($res == TRUE) {
			if ($this->getSuccessor() != NULL) {
				return $this->getSuccessor()->run();
			} else {
				return TRUE;
			}
		} else {
			return FALSE;
		}
	}
	
	/**
	 * @param array $params 规则列表
	 */
	abstract public function setRules($params);
	
	abstract public function checkValidate();
}
