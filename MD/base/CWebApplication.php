<?php

class CWebApplication extends AbstractComponent {
	
	//网站相关的基本信息
	public $siteName; //网站名
	public $siteUrl;  //网站链接
	public $domain;//域名，设置cookie等情况会用到
	
	//web应用的基本信息 
	public $module;//站点下当前应用的模块名称
	public $baseDir;//站点下当前应用的根路径
	public $theme;//当前主题名称
		
	//默认controller,action
	public $defaultController;
	public $defaultAction;

	// 以下是和配置文件对应的
	public $preload = array();//预先加载的组件列表
	public $components = array(); //组件配置列表
	public $import = array();//引入路径或者文件
	public $modules = array(); //模块配置列表
	public $params = array();//配置中该该站点携带的参数

	//组建缓存区,保存已生成的组建对象
	private $_components = array();
	
	//核心组件列表
	private $_coreComponents = array(
		'request'     => 'CHttpRequest',
		'dispatcher'  => 'CDispatcher',
		'thememanager'=> 'CThemeManager',
		'urlmanager'  => 'CUrlManager',
		'cookie'      => 'CCookieManager',
		'session'     => 'CSession',
	);
	
	/**
	 * 
	 * CWebApp构造函数
	 * @param array $config
	 */
	public function __construct($config) {
		MD::setApplication($this);
		parent::__construct($config);
	}

	
	
	/**
	 * 初始化。该方法在实例化MD时由AbstractComponent::__construct()调用
	 * @see Base::init()
	 */
	protected  function init() {
		$this->initUnassignParams();//初始化基础变量
		$this->preloadComponents();//预先加载基本组件
		$this->initSystemHandler();//设置错误和异常处理
		$this->loadHelper();//加载帮助
		$this->registerCoreComponents();//加载运行webApp必须的核心组件
	}
	
	/**
	 * 重载基类函数，以添加自定义参数
	 * @see lastMeiding/MD/base/AbstractComponent::loadParam()
	 */
	protected function loadParam($params){
		parent::loadParam($params);
		MD::loadCustomParam();
	}
	
	//初始化站点应用的根路径
	private function initUnassignParams() {
		$this->siteUrl = rtrim($this->siteUrl,'\\/')."/";
		if (empty($this->baseDir)) $this->baseDir = SITE_ROOT ;
	}
	
	
	//加载帮助
	private function loadHelper() {
		require_once(MD::getRealPath('md.base.CHelper'));
	}
	
	
	
	
	/**
	 * 设置系统错误和异常处理器
	 */
	private function initSystemHandler() {
		if(!defined('NO_SERVER')){//如果不是经过服务器来执行框架里的应用，则不进行错误拦截
			set_error_handler(array($this, 'handleError'), error_reporting());
			set_exception_handler(array($this, 'handleException'));
		}
	}
	
	//当webApp出现错误时候，系统自动调用该函数
	public function handleError($code, $message, $file, $line) {
		if ($code & error_reporting()) {
			restore_error_handler();
			restore_exception_handler();
			while(ob_get_length())@ob_clean();
			$message .= ' in file:'.$file.' line:'.$line;
			MD::log($message, CLogger::LEVEL_ERROR, 'ERROR');
			$this->displayErrorPage();
			$this->onError();
			$this->end(1);
		}
	}
	
	//当webApp出现异常时候，系统自动调用该函数
	public function handleException($exception) {
		restore_error_handler();
		restore_exception_handler();
		while(ob_get_length())@ob_clean();
		
		MD::log($exception->getMessage(), CLogger::LEVEL_ERROR, get_class($exception));
		$this->displayErrorPage();
		$this->onException();
		$this->end(1);
	}
	
	//显示错误页面
	private function displayErrorPage() {
		//当出现错误时候，更换基本路径，为显示错误页面做准备
		$this->baseDir = MD_ROOT;
		//显示错误页面
		$this->getComponents('dispatcher')->processError();	
	}
	
	//当webApp出现错误时候，自动调用该函数，触发事件
	private function onError() {
		//$this->raiseEvent('onError', new CEvent($this));
	}
	
	//当webApp出现异常时候，自动调用该函数，触发事件
	private function onException() {
		//$this->raiseEvent('onException', new CEvent($this));
	}
	
	//异常或者错误后调用该函数，触发http请求结束的事件
	public function end($status = 0, $exit = TRUE) {
		$this->onEndRequest();
		if($exit) exit($status);
	}
	
	
	
	/**
	 * 注册webApp运行必须的核心组件
	 */
	private function registerCoreComponents() {
		$this->_components = array();
		foreach ($this->_coreComponents as $name => $clazz) {
			if (!$this->haveComponent($name)) 
				$this->_components[$name] = new $clazz;
		}
	}
	
	/**
	 * 预load一些配置文件中的preload组件
	 */
	private function preloadComponents() {
		$preload = $this->preload;
		if ($preload == FALSE || !is_array($preload)) return;
		foreach ($preload as $name) {
			if(!$this->haveComponent($name) && ($com = $this->createComponent($name)) != FALSE) {
				$this->addComponents(array($name,$com));
			}
		}
	}
	
	/**
	 * 魔术方法，当找不到对应类变量时访问该方法.
	 * 几乎所有的MD::app()->{$name}都会经过此方法
	 */
	public function  __get($name) {
		if ($this->haveComponent($name)) {
			return $this->getComponents($name);
		} else if(($com = $this->createComponent($name)) != FALSE) {
			$this->addComponents(array($name => $com));
			return $com;
		} else if (method_exists($this, 'get'.$name)) {
			$method = 'get'.$name;
			return $this->$method();
		} else {
			throw new CException('MD::app()->'.$name.' 获取失败');
		}
	}

	//内部使用函数，获取webApp组件
	private function getComponents($name = NULL) {
		return $name == NULL ? $this->_components : ($this->haveComponent($name) ? $this->_components[$name] : FALSE);	
	}
	
	/**
	 * 
	 * 辅助函数
	 * @param $array
	 * @param $key
	 */
	protected  function getArrayValue($array, $key) {
		if ($array != NULL && is_array($array) && isset($array[$key])) {
			return $array[$key];
		} else {
			return FALSE;
		}
	}
	
	//判断当前webApp是否含有指定别名的组件
	private function haveComponent($name) {
		return $this->getArrayValue($this->_components, $name);
	}
	
	/**
	 * 动态创建组件
	 * @param string $alias 组件别名
	 * @return 组件对象 if success, otherwise FALSE
	 */
	private function createComponent($alias) {
		$comParam = $this->getArrayValue($this->components, $alias);
		if ($comParam != FALSE) {
			return CComponentFactory::getInstance()->createComponent($comParam);
		} else {
			return FALSE;
		}
	}
	
	//将生成的组件添加到组件缓冲区中，防止过多地创建组件
	private function addComponents($comps) {
		if(empty($this->_components)) {
			$this->_components = $comps;
		} else {
			foreach ($comps as $name => $c) {
				if ($c instanceof AbstractComponent) {
					$this->_components[$name] = $c;
				}
			}
		}
	}
	
	
	
	/**
	 * 运行用户请求
	 */
	public function run() {
		@ob_start();
		$this->onBeginRequest();
		$this->processRequest();
		$this->onEndRequest();
	}
	
	/**
	 * 触发开始处理http请求之前的准备事件
	 */
	private function onBeginRequest() {
		if ($this->haveEventHandler('onBeginRequest')) {
			$this->raiseEvent('onBeginRequest',new CEvent($this));
		}
	}
	
	/**
	 * 执行用户请求
	 */
	private  function processRequest() {
		$route = $this->parseUrl();
		$this->getComponents('thememanager')->updateTheme($this->theme);
		$this->getComponents('dispatcher')->process($route);
	}
	
	/**
	 * 解析用户请求url，并找出对应的应用、控制器、动作。若没有值则返回默认值
	 */
	private function parseUrl() {
		$route = $this->getComponents('urlmanager')->parseUrl();
		$this->module = $route->module;
		if (empty($route->controller)) $route->controller = $this->defaultController;
		if (empty($route->action)) $route->action = $this->defaultAction;
		return $route;
	}
	
	/**
	 * 触发http请求处理结束后的事件
	 */
	private function onEndRequest() {
		MD::log('ip='.MD::app()->request->getRemoteAddr().
				"\turl=".MD::app()->request->getUrl().
				"\tt=".number_format((microtime(TRUE)-SCRIPT_START), 5, '.', ''),
		CLogger::LEVEL_PROFILE,'total_time');
		
		if ($this->haveEventHandler('onEndRequest')) {
			$this->raiseEvent('onEndRequest',new CEvent($this));
		}
	}

    public function getImage($image) {
        return $this->getComponents('thememanager')->getImage($image);
    }

    public function getJs($js) {
        return $this->getComponents('thememanager')->getJs($js);
    }

    public function getCss($css) {
        return $this->getComponents('thememanager')->getCss($css);
    }
    
    /**
     * url生成,适合在controller里使用
     * @param Route $route  route信息
     * @param array $params  GET参数，以key,value形式
     */
    public function createUrl($route, $params = array()) {
        return $this->getComponents('urlmanager')->createUrl($route, $params);
    }  

    /**
     * 同上,区别是生成不含域名部分的短链接
     */
    public function createShortUrl($route, $params = array()) {
        return $this->getComponents('urlmanager')->createShortUrl($route, $params);
    }  

    /**
     * 适合创建简单的url（只包含那三个参数的url）
     */
    public function createSimpleUrl($controller = NULL, $action = NULL, $module = NULL) {
        return $this->getComponents('urlmanager')->createFormActionUrl($controller, $action, $module);
    }    
}
