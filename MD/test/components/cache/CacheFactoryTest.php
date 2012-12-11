<?php
/**
 * cache组件的测试类
 * @author qteqpid
 *
 */
class CCacheFactoryTest extends AbstractTestCase {
	
	public $cache = 'data'; //缓存内容
	
	/**
	 * 测试对象的生成
	 */
	public function testInit() {
		$this->assertNotEquals(FALSE, MD::app()->cache, 'CCacheFactory初始化失败');
	}
	
	public function testSave() {
		$this->assertNotEquals(FALSE, MD::app()->cache->save('TESTCASE', $this->cache), 'CCacheFactory save缓存失败');
	}
	
	public function testSearch() {
		$this->assertEquals($this->cache, MD::app()->cache->search('TESTCASE'), 'CCacheFactory search缓存失败');
	}
	
	public function testDel() {
		$this->assertNotEquals(FALSE, MD::app()->cache->del('TESTCASE'), 'CCacheFactory del缓存失败');
		$this->assertEquals(FALSE, MD::app()->cache->search('TESTCASE'), 'CCacheFactory del缓存失败');
	}
	
}