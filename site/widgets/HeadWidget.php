<?php
/**
 * 
 * HTML页面的头部组件：这些内容是不可显示的，可以放置一些CSS
 * 引入最基本的js(jquery.js)和css(imeiding.css)
 * @author instreet
 *
 */
class HeadWidget extends  AbstractWidget{

	public static $description = "";
	
	public static $title = "";
	
	public function run() {}
	/**
	 * 通过静态方法直接加载默认的CSS文件和js
	 */
	public static function selfLoad($params = array()){
		foreach ($params as $key => $val) {
			self::${$key} = $val;
		}
		self::render("app.widgets.view.head", array('title'=>self::$title,'desc'=>self::$description));
	}
}
