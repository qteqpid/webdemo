<?php
	
class CMysqli extends AbstractComponent {
	
	protected $dbhost;
	protected $dbuser;
	protected $dbpass;
	protected $dbname;
	
	private $mysqli = FALSE;
	
	/**
	 * 连接数据库
	 * @return TRUE if success, otherwise FALSE
	 */
	public function connect()
	{
		if(FALSE !== $this->mysqli) {
			return TRUE;
		}
		$time	= microtime(TRUE);
		$this->mysqli = new mysqli($this->dbhost, $this->dbuser, $this->dbpass, $this->dbname);
		if($this->mysqli->connect_errno) {
			throw new CException("CONNECT $this->dbhost Failed: ".$this->mysqli->connect_error);
		}
		$this->mysqli->query('SET NAMES utf8');
		
		$time = microtime(TRUE) - $time;
		MD::log("CONNECT $this->dbhost [time:".number_format($time, 5, '.', '').']',CLogger::LEVEL_DEBUG, 'db');
		return TRUE;
	}
	
	/**
	 * 数据库增删改操作的函数，比如
	 * execute("update users set id=?, fullname=?", array(TypeValue $param)
	 * @param string $sql 完整的sql语句，若有参数，要用？代替
	 * @param string $types 参数类型字符串。可选的参数类型有:
	 * 	i: 整数
	 *  d: 浮点数
	 *  s: 字符串
	 *  b: 二进制数据
	 * @param array $params 要替换的参数值数组
	 * @return 成功时返回被影响行数，否则返回FALSE
	 */
	public function execute($sql, $types = '', $params = array()) {
		if (substr_count($sql, '?') != count($params)) {
			return FALSE;
		}
		if ($this->connect()) {
			$time = microtime(TRUE);
			$stmt = $this->mysqli->prepare($sql);
			if(!$stmt) return FALSE;
			if ($types !== '') {
				$this->bindParams($stmt, $types, $params);
			}
			$stmt->execute();
			$ar = $this->mysqli->affected_rows;
			
			$time = microtime(TRUE) - $time;
			MD::log("$sql [time:".number_format($time, 5, '.', '')."][affect_row:$ar]",CLogger::LEVEL_DEBUG, 'db');
			return $ar;
		} else {
			return FALSE;
		}
	}
	
	/**
	 * 数据库查询操作的函数，比如
	 * query("select * from users where id=?", array(TypeValue $param) : $param->type = 'i' , $param->value = 1
	 * @param string $sql 完整的sql语句，若有参数，要用？代替
	 * @param string $types 参数类型字符串。可选的参数类型有:
	 * 	i: 整数
	 *  d: 浮点数
	 *  s: 字符串
	 *  b: 二进制数据
	 * @param array $params 要替换的参数值数组
	 * @return 成功时返回结果对象数组或单个对象，否则返回FALSE
	 */
	public function query($sql, $types = '', $params = array()) {
		if (substr_count($sql, '?') != count($params)) {
			return FALSE;
		}
		if ($this->connect()) {
			$time = microtime(TRUE);
			$stmt = $this->mysqli->prepare($sql);
			if(!$stmt) return FALSE;
			if ($types !== '') {
				$this->bindParams($stmt, $types, $params);
			}
			$stmt->execute();
			$results = $this->fetch_object($stmt);
			
			$time = microtime(TRUE) - $time;
			$rn = count($results);
			MD::log("$sql [time:".number_format($time, 5, '.', '')."][result_num:$rn]",CLogger::LEVEL_DEBUG, 'db');
			return $results;
		} else {
			return FALSE;
		}		
	}
	
	/**
	 * 获取最后一次插入操作返回的自动id值
	 */
	public function insert_id() {
		return intval($this->mysqli->insert_id);
	}
		
	private function bindParams($stmt, $types, $params){

		$allParams = array();
		$allParams[] = & $types;

		$len = count($params);
		for( $i = 0; $i < $len; $i++) {
			$allParams[] = & $params[$i];
		}

		call_user_func_array(array($stmt, "bind_param") , $allParams);
	}
	
	private function fetch_object($stmt) {
		$results = array();                                                            
		$metadata = $stmt->result_metadata();
		while ($field = $metadata->fetch_field()) {
		    $result[$field->name] = "";    
		    $resultArray[$field->name] = &$result[$field->name];                                                                  
		}
		call_user_func_array(array($stmt,'bind_result'),$resultArray);
		while($stmt->fetch()) {
			$row = new stdClass();
		    foreach($resultArray as $k=>$v) { 
		        $row->$k = $v;
		    }
		    $results[] = $row;
		}
		return $results;
	}
	
	public function __destruct()
	{
		if( $this->mysqli ) {
			@$this->mysqli->close();
			$this->mysqli	= FALSE;
		}
	}
}
	
?>
