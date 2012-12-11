<?php

abstract class AbstractAction extends AbstractComponent {
	
	private $_id;
	
	abstract public function run(); 
	
	public function getActionName() {
		return $this->_id;
	}
	
	public function setActionName($actionId) {
		$this->_id = $actionId;
	}
}
