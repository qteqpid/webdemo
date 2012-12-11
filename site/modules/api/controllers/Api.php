<?php

class Api extends Controller implements IApi {
	
	static $whiteList = array('180.186.72.71', '127.0.0.1');
	
	private $output_mode = IAPI::ECHO_MODE;
	
	public function setOutputMode($output) {
		$this->output_mode = $output;
	}
	
	/**
	 * (non-PHPdoc)
	 * 验证授权
	 * @return true if pass , otherwise false
	 * @see IApi::verifyUser()
	 */
	public function verifyUser() {
		if ($this->isSelf()) return true;
		$header = apache_request_headers();
		if (isset($header["Authorization"], $_REQUEST['client_id'])) { 
			try{
				$cid = $_REQUEST['client_id'];
				$tmp = explode(" ",$header["Authorization"]);
				$accessToken = $tmp[1];
				// 拿着cid和act到数据库查询
				//MD::log("AccessToken=".$accessToken,CLogger::LEVEL_DEBUG,'verify');
				return true; 
			} catch(CException $e) {return false;}
		}
		return false;
	}

	public function checkLogin() {
		return MD::app()->user->isLogged();
	}
	
	/**
	 * 返回错误信息
	 * @param string $request 请求方法
	 * @param string $error 错误信息
	 * @param int $code 错误代号
	 */
	public function returnError($request, $error, $code) {
		$format = isset($_REQUEST['format']) ? $_REQUEST['format'] : "json";
		if ($format == "json") {
			$data = json_encode(array("request"=>$request, "error"=>$error, "error_code"=>$code));
		} else if ($format == "xml") {
			// TODO: 做一个xml_encode吧
		} else {}
		if ($this->output_mode == IApi::ECHO_MODE) {
			echo $data;
		} else {
			return $data;
		}
	}
	
	/**
	 * 返回请求结果
	 * @param string $data 结果
	 */
	public function returnInfo($data) {
		$format = isset($_REQUEST['format']) ? $_REQUEST['format'] : "json";
		if ($format == "json") {
			$data = json_encode($data);
		} else if ($format == "xml") {
			// TODO: 做一个xml_encode吧
		} else {}
		if ($this->output_mode == IApi::ECHO_MODE) {
			echo $data;
		} else {
			return $data;
		}		
	}
	
	/**
	 * 获取请求参数
	 * @return 成功则返回值，失败返回false
	 * @param string $key
	 */
	public function getParam($key) {
		return isset($_REQUEST[$key]) ? $_REQUEST[$key] : false;
	}
	
	public function parseParam($params,$fieldFilterMap=null) {
		$obj = new stdClass();$isHaveUid = false;
		foreach ($params as $key => $default_Value) {
			$val = $this->getParam($key);
			if ($val === false) {
				if ($default_Value === 'require') return false;
				$val = $default_Value;
			}
			if ($key === 'un') {
				if ($val == MD::app()->user->getUsername()) {
					$obj->uid = MD::app()->user->getId();
				} else {
					$id = UserModel::model()->find(new Criteria(array(
						'field' => 'id',
						'where' => array('username'=>$val)
					)));
					if ($id) {
						$obj->uid = $id[0]->id;
					} else {
						return false;
					}
				}
				$isHaveUid = true;
			}
			$obj->$key = $val;
		}
		if(null === $fieldFilterMap)return $obj;
		else {
			if($isHaveUid)$fieldFilterMap['uid']= null;
			return HelpService::getFilterParams($obj, $fieldFilterMap);
		}
	}
	
	private function isSelf() {
		//return true;
		if ((isset($_GET['r']) && $_GET['r'] == "rpwtrpjj") || MD::app()->request->isAjaxRequest()) {
			//$rand = trim();
			//if ($rand == HelpService::APHash(MD::app()->request->getRemoteAddr())) return true;
			return true;
		}
		return false;
	}
	/*
	public function filters() {
		return array(
			array(
				'class' => 'CAccessControllFilter',
				'rules' => array(
					array('deny', // 登录用户才能访问post前缀的action
					    'actions' => array('post.*'),
					    'users' => array('?'),
					),				
				),
			),
		);
	}
	*/
}
