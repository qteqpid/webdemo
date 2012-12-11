<?php 

class CComponentFactory {
	
	private static $_self;
	
	public static function getInstance() {
		if (self::$_self == NULL) {
			self::$_self = new CComponentFactory();
		}
		return self::$_self;
	}
	
	/**
	 * 
	 * 组件工厂创建组件
	 * @param array $comParam
	 * @throws CException
	 */
	public function createComponent($comParam) {
		$clazz = $comParam['class'];
		
		if(!class_exists($clazz)){
			if(!array_key_exists('path', $comParam)){
				throw new CException("Create component $clazz fail");
			}
			$path = $comParam['path'];
			$path = MD::getRealPath($path);
			if (file_exists($path) == FALSE){ 
				throw new CException("Create component $clazz fail");
			}
			include_once ($path);
		}
		
		$param = array();
		if(array_key_exists('param', $comParam)){
			$param = $comParam['param'];
		}
		
		return new $clazz($param);
	}
}
