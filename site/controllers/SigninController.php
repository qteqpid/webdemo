<?php

class SigninController extends Controller {
	
	public function actionIndex($error = FALSE) {
		$model = new stdClass();
		$model->loginUrl = $this->createSimpleUrl('signin','submit');
		if ($error !== FALSE) $model->errorMsg = $error;
		$this->render('signin.php', array('model' => $model));
	}

	public function actionSubmit() {
		$username = isset($_POST['username']) ? $_POST['username'] : '';
		$password = isset($_POST['password']) ? $_POST['password'] : '';
		$rememberme = isset($_POST['rememberme']) ? $_POST['rememberme'] : FALSE;
		$isLogged = MD::app()->user->login($username, $password, $rememberme);
		if ($isLogged) {
			if (isset($_POST['redirectUrl'])) {
				$this->redirect($_POST['redirectUrl']);
			} else {
				MD::app()->user->redirectToHome();
			}
		} else {
			$this->actionIndex('昵称或密码错误!');
		}
	}
	
	public function filters() {
		return array(
			array(
				'class' => 'CAccessControllFilter',
				'rules' => array(
					array('deny', // 非登录用户才能访问
					    'users' => array('@'),
					),				
				),
			),
		);
	}
	
}
