<?php

class CCookieManager extends AbstractComponent {

	/**
	 * 获取COOKIE中的数据
	 * @param string $cookieKey
	 * @param 输入参数 $data 当数据获取成功时候，$data的值是返回的结果
	 * 当数据获取失败时候，$data不会被修改
	 * @return bool  成功返回true ,失败返回false
	 */
	public function getCookie($cookieKey,& $data) {
		if (isset($_COOKIE[$cookieKey])) {
			$data = $_COOKIE[$cookieKey];
			return true;
		}
		return false;
	}
	 
	public function saveCookie($name, $data, $duration) {
		$value = $this->hashData(serialize($data));
		setcookie($name, $value, time()+$duration, '/', $this->cookie_domain());
	}
	 
	public function removeCookie($name) {
		setcookie($name, NULL, time()+60*24*60*60, '/', $this->cookie_domain());
	}
	 
	/**
	 * 验证cookie的合法性
	 * @param & string $data cookie的value，该值是经过hashData后得到的。
	 * @return bool 表示验证成功与否，当成功时传递来的引用参数 $data 被赋值为真实值
	 */
	public function validateData(& $data) {
		$len = strlen($this->computeHashCode('test'));//这是个计算出来是固定的长度
		if(strlen($data) >= $len) {
			$hashCode = substr($data, 0, $len);
			$realData = substr($data, $len);
			if($hashCode === $this->computeHashCode($realData)){
				$data = unserialize($realData);
				return true;
			}
		}
		return FALSE;
	}

	/**
	 * 给cookie的value加密
	 * @param array $data 包含有状态信息的cookie值（已序列化）
	 * @return string 加密后的cookie值
	 */
	public function hashData($data) {
		return $this->computeHashCode($data).$data;
	}

	public function computeHashCode($data) {
		return md5($data);
	}

	public function cookie_domain() {
		return '.'.MD::app()->domain;
	}
}
