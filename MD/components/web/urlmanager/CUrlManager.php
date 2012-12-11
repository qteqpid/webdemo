<?php


class CUrlManager extends AbstractComponent {

	
	/**
	 * 
	 * 判断webApp是否存在某组件
	 * @param string $module
	 */
	private function searchModule($module) {
		return in_array($module,  MD::app()->modules);	
	}
	
	
	
	/**
	 * 解析http请求
	 * controller/action/value  -> controller/action
	 * controller/value -> controller
	 * controller/action -> controller/action
	 * module/controller/action/value -> controller/action
	 */
	public function parseUrl() {
		$uri = MD::app()->request->getRequestUri();
		$uri = urldecode($uri);
		if (($pos = strpos($uri, '?')) !== FALSE) {
			$uri = substr($uri, 0, $pos);
		}
		if (($pos = strpos($uri, '#')) !== FALSE) {
			$uri = substr($uri, 0, $pos);
		}
		$uri = trim($uri, '/');

		$nodeList = explode('/', $uri);
		if (empty($nodeList) || $nodeList[0] === '') {
			return new Route();
		}

		$route = new Route();
		// module
		if ($this->searchModule($nodeList[0])) {
			$route->module = trim($nodeList[0]);
			array_shift($nodeList);
		}

		if (count($nodeList) == 0) {
			return $route;
		}

		// controller
		$controllerId = $nodeList[0];
		if (MD::app()->dispatcher->existController($controllerId, $route->module)) {
			$route->controller = $controllerId;
			array_shift($nodeList);
		} else {
			$route->controller = 'index';
			//throw new CException(" $controllerId urlManager's parseUrl fail: can NOT find controller in $uri");
		}
		// action	
		if (count($nodeList) % 2 != 0) {
			$route->action = $nodeList[0];
			if (strpos($route->action,'.') !== false) {
				$tmp = explode('.', $route->action, 2);
				$route->action = $tmp[1].ucwords($tmp[0]);
			}
			array_shift($nodeList);
		}
		// params
		for ($i = 0; $i < count($nodeList); $i += 2) {
			$_GET[$nodeList[$i]] = $nodeList[$i+1];
		}
		
		return $route;
	}


	
	/**
	 * url生成
	 * @param string $module 模块名
	 * @param string $controller controller名
	 * @param string $action  action名
	 * @param array $params  GET参数，以key,value形式
	 * @param string $separator 参数分隔符
	 */
	public function createUrl($route, $params) {
		$suffix = $this->createShortUrl($route, $params);
		return MD::app()->siteUrl.ltrim($suffix,"/");
	}

	/**
	 * 不包含域名部分的短链接,比如http://www.imeiding.com/xxx => /xxx
	 */
	public function createShortUrl($route, $params = array()) {
		$url = $this->createFormActionUrl($route->controller, $route->action, $route->module);
		$anchor = "";
		foreach ($params as $key => $val) {
			if ($key == "#") {
				$anchor = "#".$val;
				continue;
			}
			$url .= "/$key/".urlencode($val);
		}
		return $url.$anchor;
	}

	/**
	 * 只包含module、controller、action信息的短链接，比如 /xxx/yyy. 一般用在form里的action
	 */
	public function createFormActionUrl($controller = NULL, $action = NULL, $module = NULL) {
		$url  = empty($module) ? "/" : "/$module/";
		if (empty($controller)) {
			return $url;
		} else {
			$url .= $controller;
			if (empty($action)) {
				return $url;
			} else {
				return $url .= "/".$action;
			}
		}
	}
	
	/**
	 * 
	 * 获取图片服务器的图片地址
	 * @param string $imageAvatar
	 * @return string $imgSrc
	 */
	public function getImageAvatarLink($imageAvatar){
		if (FALSE != strstr($imageAvatar, 'http')) {
            return $imageAvatar; 
        }
        return 'http://static.instreet.cn/'.$imageAvatar;	
	}

	public function getUserAvatarLink($avatar, $dir = '') {
		$avatarDir = '/avatar/';
		if ($dir !== '') $avatarDir .= $dir . "/";
		return $avatarDir.$avatar;
	}
	
	public function getUserLink($username) {
		return MD::app()->createUrl(new Route('user'), array('un'=>$username));
	}
	
	public function getShopLink($shopId) {
		return MD::app()->createUrl(new Route('shop'), array('id'=>$shopId));
	}
	
	public function getUsernameByLink($userLink){
		$lastPos = strrpos($userLink, "/");
		return substr($userLink, $lastPos+1);
	}
}

/**
 * 
 * Route类，用于路由用户的http请求
 *
 */
class Route {

	public $module;

	public $controller;

	public $action;

	public function __construct($controller = NULL, $action = NULL, $module = NULL) {
		$this->controller = $controller;
		$this->action = $action;
		$this->module = $module;
	}
}
