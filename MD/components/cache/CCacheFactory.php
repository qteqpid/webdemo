<?php
class CCacheFactory extends AbstractComponent {
	
	protected $mechanism;
	protected $host;
	protected $port;
	protected $prefix;
	
	const HALF_MIN  = 30;
	const ONE_MIN  = 60;
	const TWO_MIN  = 120;
	const TEN_MIN  = 600;	
	const ONE_HOUR = 3600;
	const ONE_DAY  = 86400;
	const ONE_MONTH = 2592000;
	
	private $_cache_info = array(
		'TESTCASE' => array('key'=>'testcase', 'expireTime' => self::ONE_HOUR,  'isAlive' => TRUE), // 单元测试用，这里做个例子
	);
	
	private $_cache;
	
	public function init() {
		switch($this->mechanism) {
			case 'memcached':
				require_once (dirname(__FILE__).DS.'Cache_Memcached.php');
				$this->_cache = new Cache_Memcached(array(
														'host'=>$this->host,
														'port'=>$this->port,
														'prefix'=>$this->prefix));
				break;
				
			case 'xcache':
				require_once (dirname(__FILE__).DS.'Cache_Xcache.php');
				$this->_cache = new Cache_Xcache();
				break;
				
			case 'filesystem':
			default:
				require_once (dirname(__FILE__).DS.'Cache_Filesystem.php');
				$this->_cache = new Cache_Filesystem(array('prefix'=>$this->prefix,
															'cachePath' => $this->cachePath));
		}
	}

	public function save($cacheKey, $data, $keySuffix = '') {
		if( !OPEN_CACHE ) return FALSE;
		if (($cache = $this->getCache($cacheKey)) != FALSE) {
			return @$this->_cache->set($cache['key'].$keySuffix, $data, $cache['expireTime']);	
		}
	}
	
	public function search($cacheKey, $keySuffix = '') {
		if( !OPEN_CACHE ) return FALSE;
		if (($cache = $this->getCache($cacheKey)) != FALSE) {
			return @$this->_cache->get($cache['key'].$keySuffix);
		} else {
			return FALSE;
		}
	}
	
	public function del($cacheKey, $keySuffix = '') {
			if (($cache = $this->getCache($cacheKey)) != FALSE) {
			return @$this->_cache->del($cache['key'].$keySuffix);
		}
	}
	
	public function get_debug_info() {
		return $this->_cache->get_debug_info();
	}
	
	public function garbage_collector() {
		return $this->_cache->garbage_collector();
	}
	
	private function getCache($index) {
		if (isset($this->_cache_info[$index]) && $this->_cache_info[$index]['isAlive'] == TRUE) {
			return $this->_cache_info[$index];
		} else {
			return FALSE;
		}
	}
}    	
