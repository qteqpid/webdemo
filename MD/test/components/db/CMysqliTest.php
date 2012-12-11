<?php

class CMysqliTest extends AbstractTestCase {
	
	
	public function testConnect() {
		$this->assertEquals(TRUE, MD::app()->db->connect(), '数据库连接失败！');
	}
	
	public function testExecute() {
		$this->assertEquals(1, MD::app()->db->execute("insert into tags set tagname='testsns'"), '数据库插入失败！');
		$this->assertEquals(1, MD::app()->db->execute("insert into tags set tagname=:tn", array(':tn'=>"testsns")), '数据库插入失败！');
		$this->assertEquals(1, MD::app()->db->execute("insert into tags set tagname=:tn", array(':tn'=>"testsns'xx")), '数据库插入(注入)失败！');
	}
	
	public function testQuery() {
		$obj = MD::app()->db->query("select tagname from tags where tagname='testsns'");
		$this->assertEquals('testsns', $obj->tagname, '数据库查询失败!');
		
		$obj = MD::app()->db->query("select tagname from tags where tagname=:t", array(':t'=>"testsns'xx"));
		$this->assertEquals("testsns'xx", $obj->tagname, '数据库查询(注入)失败!');
	}
	
	public function testQueryAll() {
		$obj = MD::app()->db->queryAll("select tagname from tags where tagname='testsns'");
		$this->assertEquals(2, count($obj), '数据库查询all失败!');
	}
	
	public static function tearDownAfterClass() {
		MD::app()->db->execute('delete from tags where tagname like "testsns%"');
		MD::app()->db = NULL;
	}
}