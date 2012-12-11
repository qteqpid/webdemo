<?php

/**
 * 
 * ErrorController当webApp出现错误时候，自动会调用渲染框架内定义的错误页面
 * @author instreet
 *
 */
class ErrorController extends AbstractController {
	
	//重写父类函数，以使用新的ViewRender
	protected function getViewRender(){
		return new ErrorViewRender();
	}
	
	public function actionError() {
		//渲染出错情况下的视图
		ob_get_clean();//清除缓存中已经有的页面部分，并导向错误页面
		$this->render('error.php');
	}
}

/**
 * 
 * ErrorViewRender用来渲染，webApp错误的情况下，应当显示的视图
 * @author instreet
 *
 */
class ErrorViewRender extends AbstractViewRender{
	
	/**
	 * 
	 * 重写父类render函数，渲染页面
	 */
	protected function render(){
		$MDViewPath = MD::getRealPath(MD::app()->params['MDViewPath'],true);
		require_once ($MDViewPath. $this->view);
	}
}

?>


