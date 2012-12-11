<?php

/**
 * 
 * AbstractViewRender基类，统一使用该类对视图进行渲染
 * @author instreet
 *
 */
abstract class AbstractViewRender extends AbstractComponent {
	
	protected  $data;
	protected  $view;
	protected  $cacheLifeTime = 0;
	/**
	 * 
	 * 具体如何渲染页面，可以通过继承类重写
	 */
	abstract protected  function render();
	
	/**
	 * 渲染页面
	 */
	public function renderPage($view, $data, $cacheLifeTime = 0) {
		$this->view = $view;
		$this->data = $data;
		$this->cacheLifeTime = $cacheLifeTime;
		$this->render();
	}
	
}

?>
