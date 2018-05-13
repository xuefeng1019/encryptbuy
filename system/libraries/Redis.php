<?php
/*
 * Created on 2012-4-17
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */
 
class CI_Redis {
	var $redis;
	public function __construct() {
		if (!$this->redis) {
			$this->redis = new Redis();
			$this->redis->connect('127.0.0.1');
		}
	}
	
	public function get($key) {
		if (!$key) {
			return false;
		}
		return $this->redis->get($key);
	}
	
	public function set($key, $value) {
		if (!$key || $value == null) {
			return false;
		}
		return $this->redis->set($key, $value);
	}
	
	public function getSet($key, $value) {
		if (!$key || $value == null) {
			return false;
		}
		return $this->redis->getSet($key, $value);
	}
	public function append($key, $value) {
		if (!$key || $value == null) {
			return false;
		}
		return $this->redis->append($key, $value);
	}
}
?>
