<?php

class SignoutController extends AbstractController {
	
	public function actionIndex() {
		MD::app()->user->signout();
		$this->redirect('/');
	}
	
	public function filters() {
		return array(
			array(
				'class' => 'CAccessControllFilter',
				'rules' => array(
					array('deny', // 登录用户才能访问
					    'users' => array('?'),
					),				
				),
			),
		);
	}
}
