<?php

/**
 * 只有线上能用redis，测试不能用
 * @author peter, qteqpid
 */
class RedisService extends AbstractComponent{

    private $redis;
    protected $host;
    protected $port;
    protected $prefix; 

    public function init() {
		if(ONLINE) {
	        $this->redis = new Redis();
	        $this->redis->connect($this->host, $this->port);
	        if (!$this->redis->ping()) {
	            echo 'Redis service not responding.';
		    	throw new CException('Redis ping failed');
	        }
		}
    }

    public function push($key,$content) {
		if(ONLINE) {
    		return $this->redis->lPush($this->prefix.$key,base64_encode(serialize($content)));
		}
    }
    
    public function pop($key) {
    	if(ONLINE) {
    		return unserialize(base64_decode($this->redis->lPop($this->prefix.$key)));
    	}
    }
    
    public function get($key) {
    	if(ONLINE) {
    		return $this->redis->get($this->prefix.$key);
    	}
    }
    
    public function set($key, $val) {
    	if(ONLINE) {
	    	return $this->redis->set($this->prefix.$key, $val);
    	}
    }
    
    public function incr($key) {
    	if(ONLINE) {
    		return $this->redis->incr($this->prefix.$key);
    	}
    }
    
    public function decr($key) {
    	if(ONLINE) {
	    	return $this->redis->decr($this->prefix.$key);
    	}
    }
    
    public function del($key) {
    	if(ONLINE) {
	    	return $this->redis->del($this->prefix.$key);
    	}
    }
}

?>
