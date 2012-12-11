<?php 
/**
 * CWebUser组件
 * @author qteqpid
 */
class WebUser extends CWebUser {
	//存储着WebUser对象的状态
	//包含数据库中User表的一条记录中所有字段的对象
	public $userInfo = null; //默认值为null
	
	/**
	 * 构造WebUser对象
	 * @param Obj $userInfo
	 */
	public function __construct($userInfo = NULL) {
		parent::__construct();//这个过程可能会自动登录，更新$this->userInfo
		if ($userInfo != NULL) $this->userInfo = $userInfo;//用传递的参数覆盖
	}
	
	/**
	 * 重写父类函数
	 * 更新WebUser对象的状态，即更新$this->userInfo对象
	 */
	public function updateStatus($status) {
		$this->userInfo = $status;     
	}
	
	/**
	 * 使用明文密码登录
	 * @param $username
	 * @param $password
	 * @param $rememberme
	 */
	public function login($username, $password, $rememberme = FALSE) {
		$identity = new Identity($username, $password);
		$duration = $rememberme ? 100000 : 0;
		return $this->signin($identity, $duration);
	}
	
	/**
	 * 为userInfo对象添加新的属性值
	 * @param  $key
	 * @param  $value
	 */
	public function setUserInfo($key, $value) {
		$this->userInfo->{$key} = $value;
	}
	
	/**
	 * 获取userInfo对象的属性值
	 * @param  $key 属性名称
	 * @param  $defaultValue 默认值
	 */
	public function getUserInfo($key = null, $defaultValue = '') {
		if(null === $key)return $this->userInfo;
		return isset($this->userInfo->{$key}) ? $this->userInfo->{$key} : $defaultValue;
	}
	
	/**
	 * 获取username
	 */
	public function getUsername() {
		return $this->getUserInfo('username');
	}

}

class Identity implements IIdentity {
	
	public function __construct($username = NULL, $password = NULL, $encrypt = FALSE) {
		$this->u = $username;
		$this->p = $encrypt ? $password : md5($password);
	}
	
	/**
	 * 实现接口函数
	 */
	public function validate(& $status) {
		$password = md_escape_string($this->p);
		$username = md_escape_string($this->u);
		//TODO: 检查用户是否存在，存在的话则保存数据库信息
		$result = UserModel::model()->query("select * from user where username='{$username}' and password='{$password}' limit 1");
		if(!$result)return FALSE;//验证失败直接返回
		$status = $result[0];
		return TRUE;
	}
}
