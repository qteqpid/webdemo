<?php

/**
 +---------------------------------------------
 * 一个拼装sql语句的类，作为数据库的中间层
 * 
	$com = CDbCommand::getInstance();
	
	// delete
	$cri = new Criteria(array('table'=>'users', 
	                          'where'=>'id=1',               
	                          'limit'=>array(0,10)));        
	$com->delete($cri);
	
	// update
	$cri = new Criteria(array('table'=>'users', 
	                          'set'=>array('name'=>'1', 'age'=>2) ,
	                          'where'=> array('id'=>array('eq','1'),'_op'=>'OR','time'=>array('lt',22)), 
	                          'limit'=>array(0,10)));
	$com->update($cri);
	
	// insert
	$cri = new Criteria(array('table'=>'users',
	                          'data'=>array('name'=>'t','gender'=>'f','age'=>2)));
	$com->insert($cri);
 *
 +---------------------------------------------
 * @author qteqpid <glloveyp@163.com>
 *
 */
class CDbCommand {
	
	// 查询表达式
    protected $selectSql = 'SELECT %DISTINCT% %FIELDS% FROM %TABLE% %JOIN% %ON% %WHERE% %GROUP% %HAVING% %ORDER% %LIMIT%';
    
    protected $deleteSql = 'DELETE FROM %TABLE% %WHERE% %ORDER% %LIMIT%';
    
    protected $updateSql = 'UPDATE %TABLE% %SET% %WHERE% %ORDER% %LIMIT%';
    
    protected $insertSql = 'INSERT INTO %TABLE% (%FIELDS%) VALUES (%VALUES%)';
	 
    protected $comparison  = array('eq'=>'=','neq'=>'!=','gt'=>'>','egt'=>'>=','lt'=>'<','elt'=>'<=','notlike'=>'NOT LIKE','like'=>'LIKE');
    
    protected $oplist = array('AND','OR','XOR');
    
    protected $others = array('IN' => 'IN', 'BETWEEN'=>'BETWEEN', 'NOTIN'=>'NOT IN', 'NOTBETWEEN'=> 'NOT BETWEEN');
    
	private static $_command = NULL;
	
	private function __construct(){}
	
	public static function getInstance() {
		if (self::$_command == NULL) {
			self::$_command = new CDbCommand();
		}
		return self::$_command;
	}
	
	/**
     * 成功时返回对象数组，失败时返回FALSE
     * @param string $sql
     */
	public function select($criteria) {
        $sql   = str_replace(
            array('%TABLE%','%DISTINCT%','%FIELDS%','%JOIN%','%ON%','%WHERE%','%GROUP%','%HAVING%','%ORDER%','%LIMIT%'),
            array(
                $this->parseTable($criteria->table),
                $this->parseDistinct(isset($criteria->distinct)?$criteria->distinct:false),
                $this->parseField(isset($criteria->field)?$criteria->field:'*'),
                $this->parseJoin(isset($criteria->join)?$criteria->join:''),
                $this->parseOn(isset($criteria->on)?$criteria->on:''),
                $this->parseWhere(isset($criteria->where)?$criteria->where:''),
                $this->parseGroup(isset($criteria->group)?$criteria->group:''),
                $this->parseHaving(isset($criteria->having)?$criteria->having:''),
                $this->parseOrder(isset($criteria->order)?$criteria->order:''),
                $this->parseLimit(isset($criteria->limit)?$criteria->limit:'')
            ),$this->selectSql);
        $sql   .= $this->parseLock(isset($criteria->lock)?$criteria->lock:false);
        return $this->query($sql);
	}
	
	public function insert($criteria) {
        foreach ($criteria->data as $key=>$val){
            $value   =  $this->parseValue($val);
            if(is_scalar($value)) { // 过滤非标量数据
                $values[] = $value;
                $fields[] = $this->addSpecialChar($key);
            }
        }
		$sql = str_replace(
            array('%TABLE%','%FIELDS%','%VALUES%'),
            array(
                $this->parseTable($criteria->table),
                implode(',', $fields),
                implode(',', $values),
            ),$this->insertSql);
        $sql   .= $this->parseLock(isset($criteria->lock)?$criteria->lock:false);
        return $this->execute($sql);		
	}
	
	public function update($criteria) {
		$sql = str_replace(
            array('%TABLE%','%SET%','%WHERE%','%ORDER%','%LIMIT%'),
            array(
                $this->parseTable($criteria->table),
                $this->parseSet($criteria->set),
                $this->parseWhere(isset($criteria->where)?$criteria->where:''),
                $this->parseOrder(isset($criteria->order)?$criteria->order:''),
                $this->parseLimit(isset($criteria->limit)?$criteria->limit:'')
            ),$this->updateSql);
        $sql   .= $this->parseLock(isset($criteria->lock)?$criteria->lock:false);
        return $this->execute($sql);
	}
	
	public function delete($criteria) {
		$sql = str_replace(
            array('%TABLE%','%WHERE%','%ORDER%','%LIMIT%'),
            array(
                $this->parseTable($criteria->table),
                $this->parseWhere(isset($criteria->where)?$criteria->where:''),
                $this->parseOrder(isset($criteria->order)?$criteria->order:''),
                $this->parseLimit(isset($criteria->limit)?$criteria->limit:'')
            ),$this->deleteSql);
        $sql   .= $this->parseLock(isset($criteria->lock)?$criteria->lock:false);
        return $this->execute($sql);
	}

	
	/**
	 * 数据库的表名, 格式如下:
	 * 1. string:   比如 'users, posts'
	 * 2. array:    比如 array('users','posts') 或 array('users'=>'u', 'posts'), 后者表示 'users u, posts'
	 * @param mixed
	 */
    protected function parseTable($tables) {
        if(is_string($tables))
            $tables  =  explode(',',$tables);
        $array   =  array();
        foreach ($tables as $key=>$table){
            if(is_numeric($key)) {
                $array[] =  $this->addSpecialChar($table);
            }else{
                $array[] =  $this->addSpecialChar($key).' '.$this->addSpecialChar($table);
            }
        }
        return implode(',',$array);
    }
    
    /**
	 * 是否要选择独一的数据
	 * @param boolean
	 */
    protected function parseDistinct($distinct) {
        return !empty($distinct)?   'DISTINCT ' :'';
    }
    
	/**
	 * 要选择的字段，格式如下:
	 * 1. string:   比如 'name, age'
	 * 2. array:    比如 array('name','age') 或 array('name'=>'n', 'age'), 后者表示 'name as n, age'
	 * 3. 不赋值:    表示 *  
	 * @param mixed
	 */
    protected function parseField($fields) {
        if(is_array($fields)) {
            $array   =  array();
            foreach ($fields as $key=>$field){
                if(!is_numeric($key))
                    $array[] =  $this->addSpecialChar($key).' AS '.$this->addSpecialChar($field);
                else
                    $array[] =  $this->addSpecialChar($field);
            }
            $fieldsStr = implode(',', $array);
        }elseif(is_string($fields) && !empty($fields)) {
            $fieldsStr = $this->addSpecialChar($fields);
        }else{
            $fieldsStr = '*';
        }
        return $fieldsStr;
    }
    
    /**
	 * 要联合的表，格式如下:
	 * 1. string:   比如 ',age' 或 ' left join age'， 将和table进行拼接
	 * @param string
	 */
    protected function parseJoin($join) {
        return $join;
    }
    
    /**
	 * 联合时的条件，格式如下：
	 * 1. string:  'users.id=question.id'
	 * @param string
	 */
    protected function parseOn($on) {
        return empty($on)?'':'ON '.$on;
    }
    
    /**
	 * where条件,格式如下:
	 * 1. string:  'users.id=question.id'
	 * 2. array:  array{
	 * 	'name' => array('EQ','xiaopang'),
	 *  'name2' => 'xiaopang',
	 * 	'_op' = 'AND',
	 *  'id' => array('in',array(1,2,3)),
	 *  'key' => array('notin','a, b, c'),
	 * }
	 * @param mixed
	 */
    protected function parseWhere($where) {
        $whereStr = $this->parseCondition($where);
        return empty($whereStr)?'':'WHERE '.$whereStr;
    }
    
    protected function parseCondition($condition) {
        $conditionStr = '';
        if(is_string($condition)) {
            // 直接使用字符串条件
            $conditionStr = $condition;
        }else{ // 使用数组条件表达式
        	$operate = ' AND ';
            foreach ($condition as $key=>$val){
            	if ($key == '_op' && in_array(strtoupper(trim($val)), $this->oplist)) {
            		$operate= ' '.$val.' ';
            		continue;
            	} 
                $conditionStr .= $operate." ( ";
                if (is_numeric($key) && is_string($val)) {
                	$conditionStr .= $val;
                } else {
	                $key = $this->addSpecialChar($key);
	                if(is_array($val)) {
	                    if(is_string($val[0])) {
	                        if(preg_match('/^(EQ|NEQ|GT|EGT|LT|ELT|NOTLIKE|LIKE)$/i',$val[0])) { // 比较运算
	                            $conditionStr .= $key.' '.$this->comparison[strtolower($val[0])].' '.$this->parseValue($val[1]);
	                        }elseif(preg_match('/^(IN|NOTIN)$/i',$val[0])){ // IN 运算
	                            if(is_array($val[1])) {
	                                array_walk($val[1], array($this, 'parseValue'));
	                                $zone   =   implode(',',$val[1]);
	                            }else{
	                                $zone   =   $val[1];
	                            }
	                            $conditionStr .= $key.' '.$this->others[strtoupper($val[0])].' ('.$zone.')';
	                        }elseif(preg_match('/^(BETWEEN|NOTBETWEEN)$/i',$val[0])){ // BETWEEN运算
	                            $data = is_string($val[1])? explode(',',$val[1]):$val[1];
	                            $conditionStr .=  $key.' '.$this->others[strtoupper($val[0])].' '.$this->parseValue($data[0]).' AND '.$this->parseValue($data[1]);
	                        }else{
	                            throw new CException('parseWhere失败， where='.json_encode($condition));
	                        }
	                    }else {
	                    	throw new CException('parseWhere失败， where='.json_encode($condition));
	                    }
	                } else if (is_scalar($val)) {
	                	$conditionStr .= $key.' = '.$this->parseValue($val);
	                } else {
	                   throw new CException('parseWhere失败， where='.json_encode($condition));
	                }
                }
                $conditionStr .= ' )';
                $operate = ' AND ';
            }
            $conditionStr = substr($conditionStr,strlen(' AND '));
        }
        return $conditionStr;
    }
    /**
	 * 要聚合的字段，比如 'name, age'
	 * @param string
	 */
    protected function parseGroup($group)
    {
        return !empty($group)? 'GROUP BY '.$group:'';
    }
    
    /**
	 * 聚合过滤条件，比如 'sum(age) > 100'
	 * @param string
	 */
    protected function parseHaving($having)
    {
        return  !empty($having)?   'HAVING '.$having:'';
    }
    
    /**
	 * 排序，格式如下：
	 * 1. string  'age'  'name, age desc'
	 * @param string
	 */
    protected function parseOrder($order) {
        return !empty($order)?  'ORDER BY '.$order:'';
    }
    
	/**
	 * 限定，格式如下：
	 * 1. string:  '5'  '5,10'
	 * 2. array:  array(0,10)  array(10)
	 * @var mixed
	 */
    protected function parseLimit($limit) {
    	if(is_array($limit)) {
    		if (count($limit) == 1) {
    			$limit = $limit[0];
    		} else if (count($limit) >= 2) {
    			$limit = $limit[0].','.$limit[1];
    		} else {
    			$limit = '';
    		}
    	}
        return !empty($limit)?   'LIMIT '.$limit.' ':'';
    }
    
    /**
	 * 查询锁机制
	 * @param boolean
	 */
    protected function parseLock($lock=false) {
        if(!$lock) return '';
        return ' FOR UPDATE ';
    }
    
    /**
	 * 要设置的字段，格式如下:
	 * 1. array:    比如 array('name'=>'xiaopang', 'age'=>25)
	 * 2. string
	 * @param array
	 */
    protected function parseSet($set) {
    	if (is_array($set)) {
	        foreach ($set as $key=>$val){
	        	if (is_numeric($key)) {
	        		$sets[] = $val;
	        		continue;
	        	}
	            $value   =  $this->parseValue($val);
	            if(is_scalar($value)) // 过滤非标量数据
	                $sets[]    = $this->addSpecialChar($key).'='.$value;
	        }
	        $con = implode(',',$sets);
    	} else if (is_string($set)) {
    		$con = $set;
    	} else {
    		throw new CException('sql里的set错啦'.json_encode($set));
    	}
        return 'SET '.$con;
    }
    
    /**
     * 字段和表名添加`
     * 保证指令中使用关键字不出错 针对mysql
     * @param mixed $value
     */
    protected function addSpecialChar(&$value) {
        $value   =  trim($value);
        if( false !== strpos($value,' ') || false !== strpos($value,',') || false !== strpos($value,'*') ||  false !== strpos($value,'(') || false !== strpos($value,'.') || false !== strpos($value,'`')) {
            //如果包含* 或者 使用了sql方法 则不作处理
        }else{
            $value = '`'.$value.'`';
        }
        return $value;
    }
    
    protected function parseValue(&$value) {
        if(is_string($value)) {
            $value = '\''.$this->escape_string($value).'\'';
        }elseif(is_null($value)){
            $value   =  'null';
        }
        return $value;
    }
    
    protected function escape_string($value) {
    	//return md_escape_string($value);
    	return addslashes($value);
    }
	
    /**
     * 成功时返回对象数组，失败时返回FALSE
     * @param string $sql
     */
	public function query($sql) {
		return MD::app()->db->query($sql);	
	}
	
	/**
	 * 成功时返回影响行数，失败时返回FALSE
	 * @param string $sql
	 */
	public function execute($sql) {
		return MD::app()->db->execute($sql);	
	}
}
