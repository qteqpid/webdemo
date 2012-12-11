<?php
/**
 * 页面底部可显示的小组件：
 * @author instreet
 *
 */
class FootWidget extends AbstractWidget{
	
	public function run(){}
	
	public static function selfLoad(){
		self::render("app.widgets.view.foot");	
	}
}
