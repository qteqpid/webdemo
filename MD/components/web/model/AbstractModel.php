<?php

abstract class AbstractModel extends AbstractComponent{
	/**
	 * model对象缓存池
	 * @var arrayObj
	 */
	private static $_models = array();
	
	protected $tableName;//表名称
	protected $pk = 'id';//表主键
	protected $dbCommand;//DbCommand对象，解析sql语句
	protected $attributes = array();//插入数据库记录的字段的名值对
	protected $validateFailure = false;//插入数据校验失败时候的默认返回值

	public $errorMessage;//错误信息
	
	public function init() {
		$this->dbCommand = CDbCommand::getInstance();
	}
	
	/**
	 * 子类重写该方法，用于生成model对象
	 * @param $className
	 */
	public static function model($className = __CLASS__) {
		if (isset(self::$_models[$className])) {
			return self::$_models[$className];
		} else {
			return self::$_models[$className] = new $className();
		}
	}
	
	/**
	 * 验证插入的数据库记录是否有效
	 */
	protected function validate(){return false;}
	
	/**
	 * 获取指定名称的属性值
	 * @param string $name
	 */
	protected function getAttribute($name){
		if(array_key_exists($name, $this->attributes))
			return $this->attributes[$name];
		return false;
	}
	
	/**
	 * 设置指定名称的属性值
	 * @param string $name
	 * @param $value
	 */
	protected function setAttribute($name,$value){ $this->attributes[$name]=$value; }

	/**
	 * 当新增一条数据库记录时候，有一些字段是不需要外部提供
	 * 通过这个函数来生成一些字段的默认值
	 */
	protected  function preSave() {}
	
	/**
	 * 当新增一条数据库记录成功的时候，自动调用该函数
	 */
	protected function postSave() { return $this->insertId(); }
	
	/**
	 * 插入一条数据时候，先检验，如果检验失败，调用该函数，默认返回false
	 */
	public function validateFailure() { return $this->validateFailure;} 
	
	/**
	 * 新增一条数据库记录
	 */
	protected  function save() {
		$this->preSave();
		if ($this->validate()) {
			$rowNum = $this->add(new Criteria(array('table'=>$this->tableName, 'data'=>$this->attributes)));
			if ($rowNum == 0) {
				$this->errorMessage = '操作失败，请检查填写信息'; 
				return false;
			}
			return $this->postSave();
		} else {
			return false;
		}
	}

	
	//增删改查的函数接口
	
	
	/**
	 * 增加一条数据库记录
	 */
	public function add($criteria){
		if (empty($criteria->table)) $criteria->table = $this->tableName;
		return $this->dbCommand->insert($criteria);
	}

	/**
	 * 根据条件获取数据库表字段的值
	 * 成功时返回对象数组，失败时返回FALSE
	 */
	public function find($criteria, $all = TRUE){
		if (empty($criteria->table)) $criteria->table = $this->tableName;
		if (!$all && empty($criteria->limit)) {
			$criteria->limit = '1';
		}
		return $this->dbCommand->select($criteria);
	}
		
	/**
	 * 根据主键值获取记录
	 * 成功时返回对象数组，失败时返回FALSE
	 * @param mixed $val 主键值
	 * @param array $fields 要取的字段
	 * @return arrayObj 
	 */
	public function findByPK($val, $fields = NULL){
		$sqlArray = array(
			'field'=>$fields, 
			'table'=>$this->tableName, 
			'where'=>array($this->pk=>array('eq',$val))
		);
		
		$criteria = new Criteria($sqlArray);
		return $this->dbCommand->select($criteria);
	}
		
	/**
	 * 根据条件设置数据库表中某字段的值
	 */
	public function update($criteria, $all = TRUE){
		if (empty($criteria->table)) $criteria->table = $this->tableName;
		if (!$all && empty($criteria->limit)) {
			$criteria->limit = '1';
		}
		return $this->dbCommand->update($criteria);
	}

	/**
	 * 根据条件删除数据表一条记录
	 */
	public function delete($criteria){
		if (empty($criteria->table)) $criteria->table = $this->tableName;
		return $this->dbCommand->delete($criteria);
	}
	
	public function query($sql) {
		return $this->dbCommand->query($sql);
	}
		
	public function execute($sql) {
		return $this->dbCommand->execute($sql);
	}

	/**
	 * 获取最近插入的id
	 */
	public function insertId() {
		return MD::app()->db->insert_id();
	}
}

?>
