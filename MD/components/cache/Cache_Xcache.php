<?php
	
class Cache_Xcache extends AbstractComponent implements ICache {
	
	public function init() {
		if (!function_exists('xcache_info')) {
			throw new CException('xcache没启动!');
		}
	}
	
	public function get($key) {
		return xcache_isset($key) ? xcache_get($key) : false;
	}
	
	public function set($key, $data, $expire) {
		if (xcache_set($key, $data, $expire)) {
			return true;
		}
		return false;
	}
	
	public function del($key) {
		xcache_unset($key);
	}

	public function garbage_collector()	{
	}
}
	
?>
