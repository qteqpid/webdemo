<?php
class CDispatcher extends AbstractComponent {

	/**
	 * 
	 * 根据Route选择合适的controller,action执行
	 * @param Route $route
	 */
	public function process($route) {
		
		if(!empty($route->module)){//如果是模块，则加载所需要的类文件
			include_once(MD::app()->baseDir.MD::app()->params['moduleDir'].DS.'Module.php');
		}
		
		$path = $this->getControllerPath($route->module);
		$clazz = $this->getControllerName($route->controller);
		$filePath = $path.$clazz.'.php';
		if(!class_exists($clazz)){
			include_once($filePath) ;
		}
		$controller = new $clazz($clazz);
		$controller->run($route->action);
	}

	/**
	 * 
	 * 处理请求错误或者异常发生时候的显示页面
	 */
	public function processError() {
		
		//ErrorController处于框架内部，一旦出错，则跳转到错误页面
		$errorController = new ErrorController();
		
		//通过actionId调用函数actionError
		$errorController->run("error");
		
	}
	
	/**
	 * 
	 * 获取controllerName
	 * @param string $controllerId
	 */
	public function getControllerName($controllerId) {
		return ucfirst($controllerId).'Controller';
	}

	/**
	 * 
	 * 获取controllerPath
	 * @param string $controllerId
	 * @param string $module
	 */
	public function getControllerPath($module = NULL) {
		$moduleDir = ($module == NULL) ? "" : MD::app()->params['moduleDir'] . DS .$module . DS;
		return MD::app()->baseDir.$moduleDir.MD::app()->params['controllerDir'].DS;
	}

	/**
	 * 
	 * 判断是否存在某个controller
	 * @param $controllerId
	 * @param $module
	 */
	public function existController($controllerId, $module = NULL) {
		$path = $this->getControllerPath($module);
		$clazz = $this->getControllerName($controllerId);
		if(class_exists($clazz))return true;
		$filePath = $path.$clazz.'.php';
		return file_exists($filePath);
	}
	
}
