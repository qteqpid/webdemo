<?php

interface IApi {
	const ERROR_NO_FUNC   = 100;
	const ERROR_NO_PARAM  = 101;  // 参数错误
	const ERROR_AUTH_FAIL = 401; // 授权权限错误
	const ERROR_DUPLICATE = 301; // 重复提交错误（）
	const ERROR_MYSQL = 201; // 数据库相关错
	const ERROR_UNKNOWEN = 11911;//未预料的错误
	
	/*
	 * action执行完的结果返回模式，可以是return 也可以是echo
	 */
	const RETURN_MODE = 0;
	const ECHO_MODE = 1;
	
	public function verifyUser();
}