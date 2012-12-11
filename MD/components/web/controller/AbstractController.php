<?php

/**
 * controller基类:
 * 子类如果想使用自己的viewRender,	需要重写getViewRender();
 * 
 * @author qteqpid
 */
abstract class AbstractController extends AbstractComponent {
	
	public $cacheLifeTime = 0; // 当值大于0时，打开页面缓存
	
	/**
	 * 
	 *缓冲viewRender对象
	 * @var object
	 */
	protected $viewRender = null;

	/**
	 * 
	 * 如果子类需要使用自己的模板，则重写该函数
	 * 获取viewRender;
	 * @param string $view
	 * @param array|null $data
	 */
	protected function getViewRender() {
		if (!$this->viewRender){
			//默认使用系统CViewRender组件来渲染视图
			$this->viewRender = MD::app()->viewRender;
		}
		return $this->viewRender;
	}
	
	/**
	 * 运行用户action
	 * @param string $actionId action名
	 */
	public function run($actionId) {
		if (($action = $this->createAction($actionId)) != NULL) {
			$this->runActionWithFilters($action, $this->filters());
		} else {
			$this->missingAction($actionId);
		}
	}
	/**
	 * 渲染视图页面
	 * @param string $view 视图文件名
	 * @param array $data  显示数据
	 */
	public function render($view, $data = NULL) {
		$this->getViewRender()->renderPage($view, $data, $this->cacheLifeTime);
	}
	
	/**
	 * 重定向
	 */
	public function redirect($url) {
		MD::app()->request->redirect($url);
	}
	
	/**
	 * 返回actionClass和params的对应表，比如
	 * return array(
	 * 		'actionClass' => 'param',
	 * );
	 * 这样的好处是某些独立性强的action可以单独提出来作为一个类，
	 * 并可以被多个controller所享用
	 */
	protected function actions() {
		return array();
	}

	/**
	 * 执行用户提交的action前做的filter检查
	 */
	protected function filters() {
		return array();
	}

	/**
	 * 返回对应的action对象
	 * @param string $actionId action名
	 * @return AbstractAction action对象如果找到的话，否则返回null
	 */
	protected function createAction($actionId) {
		if (method_exists($this, 'action'.$actionId) && $actionId !== 's') {
			return new CInlineAction($this, $actionId);
		} else {
			$action = $this->createActionFromMap($this->actions(), $actionId);
			if ($action != NULL && !method_exists($action, 'run')) {
				throw new CException('Action 必须有run方法。action='.$actionId);
			}
			return $action;
		}
	}
	
	/**
	 * 从actions()里寻找对应的action
	 * @param array $actionMap
	 * @param string $actionId
	 * @throws CException
	 * @return AbstractAction 若找到则返回对应action对象，否则返回NULL
	 */
	protected function createActionFromMap($actionMap, $actionId) {
		
		$clazz = ucfirst($actionId).'Action';
		
		if (isset($actionMap[$clazz])) {
			if (!class_exists($clazz)) {//检查类是否存在
				
				$filePath = MD::app()->baseDir.MD::app()->params['actionDir'].DS.$clazz.'.php';
				
				if(!file_exists($filePath)){//在指定系统配置的文件夹下搜索
					throw new CException('Class '.$clazz.' is NOT FOUND!');
				}
				require_once($filePath);
			}
			
			$params = $actionMap[$clazz];
			return new $clazz($params);	
		}
		return NULL;
	}
	
	/**
	 * 找不到对应action时的处理函数
	 * @param string $actionId
	 */
	protected function missingAction($actionId) {
		throw new CException($actionId.' in '. get_class($this).' is NOT FOUND!');
	}
	
	/**
	 * 在运行action前先运行过滤器
	 * @param AbstractAction $action
	 * @param array $filters
	 */
	protected function runActionWithFilters($action, $filters) {
		if (empty($filters)) {
			$this->runAction($action);
		} else {
			if (($chain = CFilterChain::createChain($action, $filters)) != NULL) {
				if ($chain->run()) {
					$this->runAction($action);
				}
			}
		}
	}
	
	/**
	 * 运行action
	 * @param AbstractAction $action
	 */
	protected function runAction($action) {
		$action->run();
	}

}
