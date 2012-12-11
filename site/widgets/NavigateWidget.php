<?php
/**
 * 
 * 页面导航小组件
 * @author instreet
 *
 */
class NavigateWidget extends AbstractWidget {

	public function run() {
		self::render('app.widgets.view.navigate');	
	}
	
	/**
	 * 通过该静态函数，直接加载NavigateWidget组件
	 */
	public static function selfLoad($tab = ''){
		self::render('app.widgets.view.navigate',array('tab'=>$tab));	
	}
}
