<?php
//继承AbstractModel基类
class UserModel extends AbstractModel {
		
	
	//必须的字段名称
	private $_necessary = array('password','username');
	
	public static function model($className = __CLASS__) {
		return parent::model($className);
	}

	//初始化继承成员变量
	public function init() {
		parent::init();
		$this->tableName = 'user';
	}

	/**
	 * 判断必须的字段是否都存在
	 */
	private function allExist(){
		foreach($this->_necessary as $name){
			if(!key_exists($name, $this->attributes))
				return false;
		}
		return true;
	}
	
	//重写父类函数
	protected function validate() {
		if(!$this->allExist()){
			$this->errorMessage ="提交的信息不完整，无法注册用户";
			return false;
		}
		
		$username = $this->getAttribute('username');
		if ( ($user = $this->findUser(new Criteria(array('where'=>array('username'=>$username))))) != NULL) {
			$this->errorMessage = '昵称已经被注册过了';
			$this->validateFailure = $user->id;
			return false;
		}
		return true;
	}
	
	/**
	 * 增加一个user
	 * @param arrayMap $params
	 */
	public function addUser($params){
		$this->attributes = $params;
		return $this->save();
	}
	
	/**
	 * @param Criteria $criteria
	 * @return object or false
	 */
	public function findUser($criteria) {
		$user = $this->find($criteria,FALSE);
		if(empty($user)) return false;
		return $user[0];
	}
	
}

?>
