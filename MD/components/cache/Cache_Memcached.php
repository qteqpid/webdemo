<?php
	
class Cache_Memcached extends AbstractComponent implements ICache {
	
	protected $host;
	protected $port;
	protected $prefix;

	private $_mc;
	
	public function init() {
		$this->_mc = FALSE;
		$this->ext	= class_exists('Memcached',FALSE) ? 'memcached' : 'memcache';
	}
	
	private function connect() {
		if( FALSE == $this->_mc ) {
			$this->_mc	= $this->ext=='memcached' ? new Memcached() : new Memcache();
			$this->_mc->addServer( $this->host, intval($this->port) );
		}
		return $this->_mc;
	}
	
	public function get($key) {
		if( FALSE == $this->_mc ) {
			if( FALSE == $this->connect() ) {
				return FALSE;
			}
		}
		$key = $this->prefix.$key;
		return $this->_mc->get( $key );
	}
	
	public function set($key, $data, $expire) {
		if( FALSE == $this->_mc ) {
			if( FALSE == $this->connect() ) {
				return FALSE;
			}
		}
		$key = $this->prefix.$key;
		return $this->ext=='memcached' ? $this->_mc->set( $key, $data, $expire ) : $this->_mc->set( $key, $data, FALSE, $expire );
	}
	
	public function del($key) {
		if( FALSE == $this->_mc ) {
			if( FALSE == $this->connect() ) {
				return FALSE;
			}
		}
		$key	= $this->prefix.$key;
		return $this->_mc->delete( $key );
	}
	
	public function garbage_collector()	{
		return $this->_mc->flush();
	}
}
	
?>
