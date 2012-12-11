<?php 
/**
 * MD框架入口类，使用该类创建MD--WebApp
 * @author qteqpid
 */

defined('SITE_DIR') and define('SITE_ROOT', ROOT.SITE_DIR.DS);//根据ROOT和SITE_DIR生成SITE_ROOT
defined('TEMP_DIR') and define('TEMP_ROOT', ROOT.TEMP_DIR.DS);//根据ROOT和TEMP_DIR生成TEMP_ROOT
defined('LOG_DIR') and define('LOG_ROOT', ROOT.LOG_DIR.DS);//根据ROOT和LOG_DIR生成LOG_ROOT

//防止用户未设置下列常量，以下提供默认值
defined('SITE_ROOT') or define('SITE_ROOT', ROOT.'site'.DS);
defined('TEMP_ROOT') or define('TEMP_ROOT', ROOT.'tmp'.DS);
defined('LOG_ROOT') or define('LOG_ROOT', ROOT.'log'.DS);

class MD {
	
	private static $_app; //MD::app()的值
	private static $_logger; //log组件
	private static $_includePaths = array();//引入的路径
	private static $_classes = array();
	/**
	 * webApp单例模式
	 */
	public static function app() {
		return self::$_app;
	}
	/**
	 * 
	 * 根据配置创建CWebApp对象
	 */
	public static function createWebApplication() {
		$config = require_once (MD_CONFIG);
		return new CWebApplication($config);
	}
	/**
	 * 
	 * 设置CWebApp对象，但是此时CWebApp尚未构造完成
	 * @param Object $app
	 * @throws CException
	 */
	public static function setApplication($app) {
		if (self::$_app == NULL) {
			self::$_app = $app;
		} else {
			throw new CException('MD不能实例化两次！');
		}
	}
	/**
	 * 
	 * 被CWebApplication构造基础框架后调用，用于加载自定义参数
	 */
	public static function loadCustomParam(){
		/**
		 * 以下将用户定义常量添加到MD::app()->params参数数组中，保证框架内部的一致性
		 */
		//设置用户配置文件夹
		defined('SITE_DIR') and MD::app()->params['siteDir'] = SITE_DIR;
		defined('TEMP_DIR') and MD::app()->params['tempDir'] = TEMP_DIR;
		defined('LOG_DIR') and MD::app()->params['logDir'] = LOG_DIR;
		
		//为用户配置的文件夹生成绝对路径的别名
		MD::app()->params['sitePath'] = 'root.'. MD::app()->params['siteDir'];
		MD::app()->params['tempPath'] = 'root.'. MD::app()->params['tempDir'];
		MD::app()->params['logPath']  = 'root.'. MD::app()->params['logDir'];
		
		//至此框架内部内容已经完全加载
		MD::initImport();//引入外部配置的路径
	}

	
	
	//通过预先加载的log组件，输出日志
	public static function log($msg, $level = CLogger::LEVEL_INFO, $category = 'application') {
		self::getLogger();
		if (MD_DEBUG && $level != CLogger::LEVEL_PROFILE && TRACE_LEVEL > 0) {
			$traces = debug_backtrace();
			$count = 0;
			foreach($traces as $trace) {
				if (isset($trace['file'], $trace['line'])) {
					$msg.="\nin ".$trace['file'].' ('.$trace['line'].')';
					if(++$count >= TRACE_LEVEL)
						break;
				}
			}
		}
		self::$_logger->log($msg, $level, $category);
	}
	
	/**
	 * 函数获取logger对象
	 */
	public static function getLogger() {
		if (self::$_logger === NULL) {
			self::$_logger = new CLogger();
		}
		return self::$_logger;
	}
	

	
	
	/**
	 * 
	 * 加载引入的文件
	 * @throws CException
	 */
	private static function initImport() {
		foreach (MD::app()->import as $i) {
			if (preg_match('/\.\*$/', $i)) { // 以.*结尾，是目录
				$i = rtrim($i,".*");
				self::$_includePaths[] = MD::getRealPath($i, TRUE);
			} else {
				$slices = explode('.',$i);
				$num = count($slices);
				if ($num > 0)
					self::$_classes[$slices[$num-1]] = MD::getRealPath($i);	
			}
		}
	}
	
	/**
	 * 加载模块的目录
	 * @param $module 模块名
	 * @param $dirs 目录名数组 一般是 ('models','widgets','components','forms')
	 */
	public static function addModuleImport($module, $dirs) {
		if (!is_array($dirs))
			throw new CException("加载模块$module失败,第二个参数不是数组");
		$tmp = array();
		foreach ($dirs as $d) {
			$tmp[] = MD::getRealPath("app.modules.$module.$d", TRUE);
		}
		self::$_includePaths = array_merge($tmp, self::$_includePaths); // 模块如果同名则优先加载
	}
	
	/**
	 * 获取路径别名的真实路径。举例:
	 * getRealPath(md.components.mail.CMail) => {MD_ROOT}components/mail/CMail.php
	 * getRealPath(md.components.mail.CMail, TRUE) => {MD_ROOT}components/mail/CMail/
	 * getRealPath(app.components.mail.CMail) => {SITE_ROOT}components/mail/CMail.php
	 * getRealPath(components.mail.CMail) => components/mail/CMail.php
	 * @param string $alias 路径别名
	 * @param boolean $isDir 是否是目录
	 */
	public static function getRealPath($alias, $isDir = FALSE) {
		if (isset(self::$_aliasDir[$alias])) {
			return $isDir ? self::$_aliasDir[$alias].DS : self::$_aliasDir[$alias].'.php';
		} else {
			$dirs = explode('.', $alias);
			$path = null;
			if (isset(self::$_aliasDir[$dirs[0]])) {
				$path = rtrim(self::$_aliasDir[$dirs[0]],DS);
				unset($dirs[0]);
				$path = $path.DS;
			}
			foreach ($dirs as $dir) {
				$path .= $dir.DS;
			}
			$path = rtrim($path, DS);
			self::$_aliasDir[$alias] = $path;
			$path = $isDir ? $path.DS : $path.'.php';
			return $path;
		}
	}
	
	public static $_aliasDir = array(
		'root'	=> 	ROOT,
		'md'	=> 	MD_ROOT,
		'app'	=> 	SITE_ROOT,
		'log'	=> 	LOG_ROOT,
		'tmp'	=> 	TEMP_ROOT,
	);
	
	
	
	/**
	 * 
	 * 类的自动加载机制
	 * @param $classname
	 */
	public static function autoload($classname) {
		if (isset(self::$_coreClasses[$classname])) {
			$path = MD::getRealPath(self::$_coreClasses[$classname]);
			if ($path != FALSE) {
				include_once $path;
				return TRUE;
			}
		} else { // import path;
			foreach (self::$_includePaths as $path) {
				if (is_file($path.$classname.'.php')) {
					include_once $path.$classname.'.php';
					return TRUE;
				}
			}
			foreach (self::$_classes as $name => $path) {
				if ($classname == $name) {
					include_once $path;
					return TRUE;
				}
			}	
		}

		return FALSE;
	}
	
	//以下是MD框架需要的类文件
	private static $_coreClasses = array(
		'AbstractComponent' => 'md.base.AbstractComponent',
		'CException'        => 'md.base.CException',
		'CWebApplication' => 'md.base.CWebApplication',
		'AbstractTestCase'  => 'md.base.AbstractTestCase',
		
		'CComponentFactory'  => 'md.components.CComponentFactory',
	
		'Cache_Filesystem' => 'md.components.cache.Cache_Filesystem',
		'Cache_Memcached' => 'md.components.cache.Cache_Memcached',
		'Cache_Xcache' => 'md.components.cache.Cache_Xcache',
		'CacheFactory' => 'md.components.cache.CCacheFactory',
		'ICache'      	=> 'md.components.cache.ICache',
	
		'CDbCommand' => 'md.components.db.CDbCommand',
		'CMysqli' => 'md.components.db.CMysqli',
		'CMysqlNew' => 'md.components.db.CMysqliNew',
	    'CMysqlRW' => 'md.components.db.CMysqliRW',
		'Criteria'   => 'md.components.db.Criteria',	
			
		'AbstractLogRoute' => 'md.components.log.AbstractLogRoute',
		'CFileLogRoute' => 'md.components.log.CFileLogRoute',
		'CWebLogRoute' => 'md.components.log.CWebLogRoute',
		'CRemoteLogRoute' => 'md.components.log.CRemoteLogRoute',
		'CLogRouter' => 'md.components.log.CLogRouter',
		'CLogger'  => 'md.components.log.CLogger',
	
		'CMail' => 'md.components.mail.CMail',
		'MAILINFO' => 'md.components.mail.MAILINFO',
		'XMail' => 'md.components.mail.XMail',
	
		'RedisService' => 'md.components.redis.RedisService',
	
		'Solr' => 'md.components.solr.Solr',
		'SolrFactory' => 'md.components.solr.SolrFactory',
	
		'AbstractAction' => 'md.components.web.action.AbstractAction',
		'CInlineAction' => 'md.components.web.action.CInlineAction',
	
		'CCaptcha' => 'md.components.web.captcha.CCaptcha',
		
		'AbstractController' => 'md.components.web.controller.AbstractController',
		'ErrorController' => 'md.components.web.controller.ErrorController',	
	
		'CDispatcher'   => 'md.components.web.dispatcher.CDispatcher',
	
		'CFilterChain'          => 'md.components.web.filter.CFilterChain',
		'AbstractFilter'        => 'md.components.web.filter.AbstractFilter',
		'CAccessControllFilter' => 'md.components.web.filter.CAccessControllFilter',
	
		'AbstractModel'         => 'md.components.web.model.AbstractModel',
	
		'CThemeManager' => 'md.components.web.theme.CThemeManager',
		
		'CUrlManager'   => 'md.components.web.urlmanager.CUrlManager',
		'CHttpRequest'  => 'md.components.web.urlmanager.CHttpRequest',
		
		'CWebUser'      => 'md.components.web.user.CWebUser',
		'IUser'         => 'md.components.web.user.IUser',
		'IIdentity'             => 'md.components.web.user.IIdentity',
		'CCookieManager'        => 'md.components.web.user.CCookieManager',
		'CSession'              => 'md.components.web.user.CSession',
		'CCacheSession'              => 'md.components.web.user.CCacheSession',
	
		'AbstractViewRender' => 'md.components.web.view.AbstractViewRender',
		'CViewRender'   => 'md.components.web.view.CViewRender',
	
		'AbstractWidget' => 'md.components.web.widget.AbstractWidget',
	
		'IApi' => 'md.components.web.api.IApi',
	    'MDclient' => 'md.components.web.api.MDclient',
		'CStack' => 'md.base.CStack',
		'Config' => 'md.base.Config',
	
		'SocketClient' => 'md.components.socket.SocketClient',
		'SocketServer' => 'md.components.socket.SocketServer',
	);
	
}

spl_autoload_register(array('MD','autoload'));
