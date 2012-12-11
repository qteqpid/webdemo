<?php

class CAccessControllFilter extends AbstractFilter {
	
	protected $deny;
	
	protected $allow;
	
	private $_rules = array(); // 过滤规则
	
	public function setRules($rules) {
		foreach ($rules as $rule) {
			if(is_array($rule) && isset($rule[0])) {
				$this->_rules[] = new AccessRule($rule);
			}
		}
	}
	
	public function checkValidate() {
		$user = MD::app()->user;
		$request = MD::app()->request;
		
		foreach ($this->_rules as $rule) {
			$result = $rule->check($this->action, $user, $request);
		   	if ($result > 0) { //allow pass
				return TRUE;
			} else if ($result < 0) { // deny
				$this->accessDenied($user);
				return FALSE;	
			}
		}
		return TRUE;
	}

	protected function accessDenied($user) {
		if (!$user->isLogged()) {
			$user->loginRequired();
		} else {
			$user->redirectToHome();
		}
	}
}

class AccessRule extends AbstractComponent
{
	/**
	 * TRUE is allow, FALSE is deny
	 * @var boolean
	 */
	public $allow;
	public $actions;
	public $users;
	public $roles;
	public $ips;
	public $requestMethods;
	
	public function __construct($rule) {
		$this->allow = $rule[0]==='allow';
		foreach(array_slice($rule,1) as $name=>$value)
		{
			$this->$name=array_map('strtolower',$value);
		}
	}
	
	/**
	 * 检查规则啦
	 * @param AbstractAction $action
	 * @param CWebUser $user
	 * @param CHttpRequest $request
	 * @return boolean
	 */
	public function check($action, $user, $request) {
		$res = TRUE;
		$res and $res = $this->checkActions($action);
		$res and $res = $this->checkUsers($user);
		$res and $res = $this->checkRoles($user);
		$res and $res = $this->checkIps($request);
		$res and $res = $this->checkRequestMethods($request);

		if ($res) { //符合条件，判断结果
			return $this->allow ? 1 : -1;
		} else {
			return 0;
		}
	}
	
	/**
	 * 检查用户action是否在actions列表中
	 * @param AbstractAction $actionId
	 */
	public function checkActions($action) {
		if (empty($this->actions)) return true;
		foreach ($this->actions as $a) {
			if (preg_match("/".$a."/i",$action->getActionName())) return true;
		}
		return false;
	}
	
	/**
	 * 检查用户是否符合用户身份规则（登录非登录）
	 * @param CWebUser $user
	 */
	public function checkUsers($user) {
		if(empty($this->users))	return TRUE;
		
		foreach($this->users as $u)	{
			if($u === '*') // 任何人
				return TRUE;
			else if($u === '?' && !$user->isLogged()) // 旅客
				return TRUE;
			else if($u === '@' && $user->isLogged()) // 社区登录用户
				return TRUE;
		}
		return FALSE;
	}
	
	/**
	 * 用户角色检查，（管理员和普通用户）
	 * @param CWebUser $user
	 */
	public function checkRoles($user) {
		if(empty($this->roles)) return TRUE;
		
		foreach($this->roles as $role){
			if($role == 'isadmin' && $user->isAdmin()){
				return TRUE;
			}else if($role == 'noadmin' && !$user->isAdmin()){
				return TRUE;
			}
		}
		return FALSE;
	}
	
	/**
	 * 请求ip检查
	 * @paramCHttpRequest $request
	 */
	public function checkIps($request) {
		if(empty($this->ips))	return TRUE;
		$ip = $request->getRemoteAddr();
		foreach($this->ips as $rule) {
			if($rule === '*' || $rule === $ip || (($pos = strpos($rule,'*')) !== false && !strncmp($ip,$rule,$pos)))
				return TRUE;
		}
		return FALSE;
	}
	
	/**
	 * 请求类型（get、put...）检查
	 * @param CHttpRequest $request
	 */
	public function checkRequestMethods($request) {
		return empty($this->requestMethods) || in_array(strtolower($request->getRequestMethod()), $this->requestMethods);
	}
}
