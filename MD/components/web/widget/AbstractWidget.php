<?php
/**
 * 独立小组件
 * @author qteqpid
 */
abstract class AbstractWidget extends AbstractComponent {

	abstract function run();
	
	public static function render($viewFile, $data = array()) {
		$file = MD::getRealPath($viewFile);
	    if (!empty($data) && is_array($data)) {
			foreach($data as $k=>$v) {         
				${$k} = $v;
			}
		}
	    require ($file);
	}
}
