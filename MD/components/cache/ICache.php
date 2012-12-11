<?php

interface ICache {
	/**
	 * 请求缓存内容
	 */
	function get($key);
	/**
	 * 保存缓存
	 * @param unknown_type $key  缓存的键
	 * @param unknown_type $data 缓存的值
	 * @param unknown_type $expire 缓存有效期，以秒为单位
	 */
	function set($key, $data, $expire);
	/**
	 * 删除缓存
	 */
	function del($key);
	/**
	 * 缓存垃圾回收
	 */ 
	function garbage_collector();
}
