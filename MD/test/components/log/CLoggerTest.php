<?php

class CLoggerTest extends AbstractTestCase {
	public function testInit() {
		$logger = MD::getLogger();
		$this->assertTrue(($logger instanceof CLogger), 'getLogger 失败');
	}
}