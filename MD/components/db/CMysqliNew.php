<?php
	
class CMysqliNew extends AbstractComponent {
	
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
	public function execute($sql) {
		if ($this->connect()) {
			$time = microtime(TRUE);
			$res = $this->mysqli->query($sql);
			if(!$res) return FALSE;
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
	 * @return 成功时返回结果对象数组，否则返回FALSE
	 */
	public function query($sql) {
		if ($this->connect()) {
			$time = microtime(TRUE);
			$res = $this->mysqli->query($sql);
			if(!$res) return FALSE;
			$results = $this->fetch_object($res);
			
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
		
	private function fetch_object($res) {
		$results = array();     
		while ($obj = $res->fetch_object()) {
			$results[] = $obj;
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
	
    public function startTrans() {
    	if ($this->connect()) {
    		$this->mysqli->autocommit(false);
    		return TRUE;
    	} else {
    		return FALSE;
    	}
    }
    
    public function commit() {
    	@$this->mysqli->commit();
    	@$this->mysqli->autocommit(true);
    }
    
    public function rollback() {
    	$this->mysqli->rollback();
    }
    
    public function changeDb($dbname) {
    	return $this->query("use $dbname");
    }
}
	
?>
