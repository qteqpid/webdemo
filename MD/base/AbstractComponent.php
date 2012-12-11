<?php 
/**
 * 组件基类。
 * @author qteqpid
 *
 */
abstract class AbstractComponent {
	
	private $_eh; // 事件处理器列表容器
	
	/**
	 * 
	 * 组件基类构造函数
	 * @param array $params
	 */
	public function __construct($params = array()) {
		$this->_eh = array();
		$this->loadParam($params);
		$this->init();
	}
	
	/**
	 * 该方法在子类生成实例时自动调用
	 */
	protected function init() {}
	
	/**
	 * 
	 * 该方法被构造函数自动调用，将传递的参数数组，转换成组件类的成员对象
	 * @param $params
	 */
	protected function loadParam($params) {
		if (!is_array($params) || $params === array()) return;
		foreach ($params as $param => $value) {
			$this->$param = $value; 
		}
	}
	

	
	/**
	 * 安装事件调用处理器，当事件${name}发生时，调用${handler}进行处理
	 * @param string $name 事件名
	 * @param callback $handler 处理器
	 */
	public function attachEventHandler($name, $handler) {
		if (!isset($this->_eh[$name])) { 
			$this->_eh[$name] = array();
	    }
		$this->_eh[$name][] = $handler;
	}
	
	/**
	 * 卸载事件调用处理器
	 * @param string $name 事件名
	 * @param callback $handler 处理器
	 */
    public function detachEventHandler($name, $handler) {
        if (!isset($this->_eh[$name]))return;                                                                             

        $handlers = & $this->_eh[$name];    //通过返回数组引用，来改变原来数组
        $index = array_search($handler, $handlers, TRUE); // 寻找下标                                                     
        if ($index !== FALSE) { //FIX by qteqpid, when $index=0
            array_splice($handlers, $index, 1); // 删除元素                                                               
        }
    }
	
	/**
	 * 触发事件。
	 * @param string $name 事件名
	 * @param CEvent $event 事件本身。将作为handler的参数进行传递
	 */
	public function raiseEvent($name, $event) {
		if (isset($this->_eh[$name])) {
			foreach ($this->_eh[$name] as $handler) {
				call_user_func($handler, $event);
			}
		}
	}
	
	/**
	 * 获取相应事件的handlers列表
	 * @param string $name 事件名
	 * @return handlers 处理器列表
	 */
	public function getEventHandlers($name) {
		if($this->haveEvent($name)) {
			if (!isset($this->_eh[$name])) {
				$this->_eh[$name] = array();
			}
			return $this->_eh[$name];
		} else {
			throw CException("exception: event $name handler不存在。");
		}		
	}
	
	/**
	 * 判断该类是否含有该事件
	 * @param string $name 事件名
	 */
	public function haveEvent($name) {
		return !strncasecmp($name, 'on', 2) && method_exists($this, $name);
	}
	
	/**
	 * 判断该类是否含有该事件的处理器
	 * @param string $name 事件名
	 */
	public function haveEventHandler($name) {
		return !empty($this->_eh[$name]);
	}

}

/**
 * 
 * 事件类
 *
 */
class CEvent {

	public $sender;

	public $params;

	public function __construct($sender=null,$params=null)
	{
		$this->sender=$sender;
		$this->params=$params;
	}
}
