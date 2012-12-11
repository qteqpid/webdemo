<?php

class CInlineAction extends AbstractAction {
	
	private $_controller;
	
	public function __construct($controller, $actionId) {
		$this->setActionName($actionId);
		$this->_controller = $controller;
	}
	
	public function run() {
		$method = 'action'.ucfirst($this->getActionName());
		$this->_controller->$method();
	}
}
