<?php
	
/**
 * (多)读(一)写分离版本
 * @author qteqpid
 *
 */
class CMysqliRW extends AbstractComponent {
	
	protected $rw;
	protected $ro;
	
	private $mysqli = FALSE; // 当前连接
	private $mysqli_rw = FALSE; // 写连接
	private $mysqli_ro = FALSE; // 只读连接
	
	/**
	 * 连接数据库
	 * @return TRUE if success, otherwise FALSE
	 */
	public function connect($link, $server_type)
	{
		if(FALSE !== $link) { // mysqli切换
			$this->mysqli = $link;
			return TRUE;
		}
		$time	= microtime(TRUE);
		$host = $this->rw;
		switch ($server_type) {
			case 'ro':
				$host = $this->ro[array_rand($this->ro)];
				$this->mysqli_ro = @(new mysqli($host['dbhost'], $host['dbuser'], $host['dbpass'], $host['dbname']));
				$this->mysqli = $this->mysqli_ro;
				if(!$this->mysqli->connect_errno)break;
			case 'rw':
			default:
				$this->mysqli_rw = new mysqli($host['dbhost'], $host['dbuser'], $host['dbpass'], $host['dbname']);
				$this->mysqli = $this->mysqli_rw;
		}
		if($this->mysqli->connect_errno) {
			throw new CException("CONNECT {$host['dbhost']} Failed: ".$this->mysqli->connect_error);
		}
		$this->mysqli->query('SET NAMES utf8');
		
		$time = microtime(TRUE) - $time;
		MD::log("CONNECT {$host['dbhost']} [time:".number_format($time, 5, '.', '').']',CLogger::LEVEL_DEBUG, 'db');
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
		if ($this->connect($this->mysqli_rw, 'rw')) {
			$time = microtime(TRUE);
			$res = $this->mysqli->query($sql);
			if(!$res) return FALSE;
			$ar = $this->mysqli->affected_rows;
			
			$time = microtime(TRUE) - $time;
			MD::log("$sql [time:".number_format($time, 5, '.', '')."][affect_row:$ar] ".$this->mysqli->host_info,CLogger::LEVEL_DEBUG, 'db');
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
		if ($this->connect($this->mysqli_ro, 'ro')) {
			$time = microtime(TRUE);
			$res = $this->mysqli->query($sql);
			if(!$res) return FALSE;
			$results = $this->fetch_object($res);
			
			$time = microtime(TRUE) - $time;
			$rn = count($results);
			MD::log("$sql [time:".number_format($time, 5, '.', '')."][result_num:$rn] ".$this->mysqli->host_info,CLogger::LEVEL_DEBUG, 'db');
			return $results;
		} else {
			return FALSE;
		}		
	}
	
	/**
	 * 获取最后一次插入操作返回的自动id值
	 */
	public function insert_id() {
		return intval($this->mysqli_rw->insert_id);
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
/*
		if( $this->mysqli_ro ) {
			@$this->mysqli_ro->close();
			$this->mysqli_ro = FALSE;
		}*/
		if( $this->mysqli_rw ) {
			@$this->mysqli_rw->close();
			$this->mysqli_rw = FALSE;
		}
	}
	
	/**
	 * 该方法已废弃
	 */
    public function startTrans() {
    	if ($this->connect()) {
    		$this->mysqli->autocommit(false);
    		return TRUE;
    	} else {
    		return FALSE;
    	}
    }
    
    /**
	 * 该方法已废弃
	 */
    public function commit() {
    	@$this->mysqli->commit();
    	@$this->mysqli->autocommit(true);
    }
    
    /**
	 * 该方法已废弃
	 */
    public function rollback() {
    	$this->mysqli->rollback();
    }
    
    public function changeDb($dbname) {
	if ($this->connect($this->mysqli_ro, 'ro')) {
		$time = microtime(TRUE);
		$res = $this->mysqli->query("use $dbname");
		if(!$res) return FALSE;
		return true;
	} 
	return false;
    }
}
	
?>
