<?php

/**
 +---------------------------------------------
 * 一个收集sql语句各段的值的类，作为CDbCommand的输入
 +---------------------------------------------
 * @author qteqpid <glloveyp@163.com>
 *
 */
class Criteria extends AbstractComponent{

	/**
	 * 
	 * 构造函数调用父类构造函数
	 * @param array $sqlArray
	 */
	public function __construct($sqlArray = NULL){
		if ($sqlArray) parent::__construct($sqlArray);
	}
	/**
	 * 数据库的表名, 格式如下:
	 * 1. string:   比如 'users, posts'
	 * 2. array:    比如 array('users','posts') 或 array('users'=>'u', 'posts'), 后者表示 'users u, posts'
	 * @var mixed
	 */
	public $table;
	
	/**
	 * 是否要选择独一的数据
	 * @var boolean
	 */
	public $distinct;
	
	/**
	 * 要选择的字段，格式如下:
	 * 1. string:   比如 'name, age'
	 * 2. array:    比如 array('name','age') 或 array('name'=>'n', 'age'), 后者表示 'name as n, age'
	 * 3. 不赋值:    表示 *  
	 * @var mixed
	 */
	public $field;
	
	/**
	 * 要设置的字段，格式如下:
	 * 1. array:    比如 array('name'=>'xiaopang', 'age'=>25)
	 * 2. string
	 * @var mixed
	 */
	public $set;
	
	/**
	 * 要插入的数据，格式如下:
	 * 1. array:    比如 array('name'=>'xiaopang', 'age'=>25)
	 * @var array
	 */
	public $data;
		
	
	/**
	 * 要联合的表，格式如下:
	 * 1. string:   比如 ',age' 或 ' left join age'， 将和table进行拼接
	 * @var string
	 */
	public $join;
	
	/**
	 * 联合时的条件，格式如下：
	 * 1. string:  'users.id=question.id'
	 * @var string
	 */
	public $on;
	
	/**
	 * where条件,格式如下:
	 * 1. string:  'users.id=question.id'
	 * 2. array:
	 * @var mixed
	 */
	public $where;
	
	/**
	 * 要聚合的字段，比如 'name, age'
	 * @var string
	 */
	public $group;
	
	/**
	 * 聚合过滤条件，比如 'sum(age) > 100'
	 * @var string
	 */
	public $having;
	
	/**
	 * 排序，格式如下：
	 * 1. string  'age'  'name, age desc'
	 * @var string
	 */
	public $order;
	
	/**
	 * 限定，格式如下：
	 * 1. string:  '5'  '5,10'
	 * 2. array:  array(0,10)  array(10)
	 * @var mixed
	 */
	public $limit;
	
	/**
	 * 查询锁机制
	 * @var boolean
	 */
	public $lock;
	
	/**
	 * 支持后添加其他参数
	 */
	public function addParams($params) {
		$this->loadParam($params);
	}
}
