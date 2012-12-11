<?php

class CWebUser extends AbstractComponent implements IUser {
	
	private $_keyPrefix;
	private $_isLogged = 'is_logged';//用于存取用户是否登录的状态的KEY,存放的数据是验证后的$status
	private $_loginUrl = NULL;

	public function init() {
		if (!$this->isLogged()) { //尝试用cookie登录
			$this->tryAutoLogin();
		}else{
			$identity = $this->getState($this->_isLogged,FALSE);//从SESSION中获取认证对象
			$this->signin($identity);//使用认证对象登录
		}
	}
	
	/**
	 * 判断用户是否已经登录
	 */
	public function isLogged() {
		return $this->getState($this->_isLogged, FALSE) ? TRUE : FALSE;
	}
	
	/**
	 * 尝试使用COOKIE中的数据自动登录
	 */
	public function tryAutoLogin() {
		if(MD::app()->cookie->getCookie($this->getStateKeyPrefix(), $cookieIdentity)){//成功获取COOKIE数据时候
			if(MD::app()->cookie->validateData($cookieIdentity)){//验证获取COOKIE数据的有效性，如果成功会给$cookieIdentity赋值
				$this->signin($cookieIdentity);//使用认证对象登录
			}
		}
	}
	
	/**
	 * 用认证对象登录 
	 * @param obj $identity
	 * @param int $duration
	 */
	public function signin($identity, $duration = 0) {
		if($identity->validate($status)){//如果认证成功
			$this->updateStatus($status);//更新WebUser状态数据
			$this->setState($this->_isLogged, $identity);//设置用户为登录状态
			if ($duration > 0) {
				//如果用户选择了‘记住我’, 将$identity保存到COOKIE，以下次自动登录
				//生存周期为$duration
				$this->saveToCookie($identity,$duration);
			}
		}
		return $this->isLogged() ? $this->afterSignin() : FALSE;
	}
	
	public function afterSignin() { return TRUE; }

	/**
	 * 将数据$data保存到cookie
	 * @param $data 
	 * @param int $duration
	 */
	protected function saveToCookie($data, $duration) {
		MD::app()->cookie->saveCookie($this->getStateKeyPrefix(), $data, $duration);//保存到cookie
	}
	
	
	/**
	 * 用户登出。 1. 清除cookie 2. 清除session
	 */
	public function signout($destorySession = TRUE) {
		MD::app()->cookie->removeCookie($this->getStateKeyPrefix());
		$destorySession ? MD::app()->session->destory() : $this->clearState();
	}
	

	/**
	 * 需要子类重新实现
	 * 更新webuser对象的状态
	 * @param  $status
	 */
	protected function updateStatus($status) {}
	

	
	//以下为与用户状态值相关的函数
	
	/**
	 * 获取已经登录的用户的认证对象
	 * @return obj or false
	 */
	public function getIdentity(){
		return $this->getState($this->_isLogged,FALSE);
	}
	
	/**
	 * 清除session中用户状态相关的信息
	 */
	protected function clearState() {
		$prefix=$this->getStateKeyPrefix();
		$n=strlen($prefix);   
		foreach(array_keys($_SESSION) as $key) {
			if(!strncmp($key,$prefix,$n))  
			unset($_SESSION[$key]);        
		}		
	}

	/**
	 * 设置用户状态信息
	 */
	protected function setState($key, $value) {
		$key = $this->getStateKeyPrefix().$key;
		$_SESSION[$key] =  serialize($value);
	}

	/**
	 * 获取用户状态信息
	 */
	protected function getState($key, $defaultValue = NULL) {
		$key = $this->getStateKeyPrefix().$key;
		return isset($_SESSION[$key]) ? unserialize($_SESSION[$key]) : $defaultValue;
	}

	private function getStateKeyPrefix() {
		if ($this->_keyPrefix == NULL) {
			$this->_keyPrefix = md5('MD.'.get_class($this).MD::app()->siteName);
		}
		return $this->_keyPrefix;
	}
	
	
	
	//以下为与url相关的函数
	
	
	/**
	 * 要求先登录
	 */
	public function loginRequired() {
		$request = MD::app()->request;
		if (!$request->isAjaxRequest())	{
			$this->setReturnUrl($request->getUrl());
		}
		$request->redirect($this->getLoginUrl());
	}
	
	protected function getLoginUrl() {
		if ( $this->_loginUrl == NULL)	
			$this->_loginUrl = MD::app()->createSimpleUrl('signin');
		return $this->_loginUrl;
	}

	public function setReturnUrl($url) {
		$this->setState('_returnUrl', $url);
	}

	public function getReturnUrl() {
		return $this->getState('_returnUrl', MD::app()->request->getHost());
	}

	/**
	 * 跳转到首页
	 */
	public function redirectToHome() {
		$host = MD::app()->request->getHost();
		MD::app()->request->redirect(rtrim($host,'/'));
	}

}
