<?php
	
class Cache_Filesystem extends AbstractComponent implements ICache {

	protected  $prefix;
	protected $cachePath;

	private $_path;
	
	protected function init() {
		$this->_path = MD::getRealPath($this->cachePath,true);
	}
	
	private function find_filename($key) {
		return $this->_path.md5($this->prefix).'-'.md5($key);
	}
	
	public function get($key) {
		$file	= $this->find_filename($key);
		$res	= FALSE;
		if( file_exists($file) && is_readable($file) ) {
			$data	= file($file);
			if( $data && is_array($data) && count($data)==2 ) {
				if( intval($data[0]) >= time() ) {
					$res	= unserialize($data[1]);
				}
			}
			if( FALSE === $res ) {
				$this->del($key);
			}
		}
		return $res;
	}
	
	public function set($key, $data, $ttl) {
		$file	= $this->find_filename($key);
		$this->del($key);
		$data	= (time()+$ttl)."\n".serialize($data);
		$res	= file_put_contents($file, $data);
		chmod($file, 0777);
		return $res;
	}
	
	public function del($key) {
		$file	= $this->find_filename($key);
		$time	= microtime(TRUE);
		if( file_exists($file) && is_writable($file) ) {
			unlink($file);
		}
		$res = !file_exists($file);
		return $res;
	}
	
	public function garbage_collector() {   
		$prefix	= md5($this->prefix).'-';
		$prefixlen	= strlen($prefix);
		$dir	= opendir($this->_path);
		$i	= 0;
		while($filename = readdir($dir)) {
			if($filename=="." || $filename=="..") {
				continue;
			}
			if( substr($filename, 0, $prefixlen) != $prefix ) {
				continue;
			}
			$file	= $this->_path.$filename;
			$fp	= fopen($file, 'r');
			$tm	= fread($fp, 10);
			fclose($fp);
			if( intval($tm) <= time() && is_writable($file) ) {
				unlink($file);
				$i	++;
			}
		}
		closedir($dir);
		return $i;
	}
}
	
?>
