<?php
/**
 * 用来读取site/config/下的配置文件的通用类
 * @author qteqpid
 */

class Config {
	
	private static $configs = array();
	
	/**
	 * 获取配置文件里的值
	 * @param string $config 点地址,比如"app.config.tbp"
	 * @param string $key
	 * @param mixed $defaultValue
	 */
	public static function get($config, $key = null, $defaultValue = false) {
		if (!isset(self::$configs[$config])) {
			$real_path = MD::getRealPath($config);
			self::$configs[$config] = include($real_path);
		}
		if ($key === null) {
			return self::$configs[$config];
		} 
		return isset(self::$configs[$config][$key]) ? self::$configs[$config][$key] : $defaultValue;
	}
}