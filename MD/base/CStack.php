<?php

/**
 * 自定义的栈结构
 * @author qteqpid
 *
 */
class CStack {
	private $stack;
	private $size;
	
	public function __construct() {
		$this->stack = array();
		$this->size = 0;
	}
	
	public function push($node) {
		array_push($this->stack, $node);
		$this->size ++;
	}
	
	public function pop() {
		if ($this->isEmpty()) return false;
		$node = array_pop($this->stack);
		$this->size --;
		return $node;
	}
	
	public function isEmpty() {
		return $this->size == 0;
	}
	
	public function toArray() {
		return $this->stack;
	}
	
	public function size() {
		return $this->size;
	}
	
}