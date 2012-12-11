<?php

class ExampleController extends AbstractController {
	
	protected function actions() {
		return array(
			'CaptchaAction' => 'params'
			//...
		);
	}
	
	public function actionIndex() {
		
	}
	
	//...
	
	protected function filters() {
		return array(
			array(
				'class' => 'className',
				'rules' => array(
					array('allow',
						'actions' => array(),
					    'users' => array(),
						'roles' => array(),
						'ips' => array(),
						'requestMethods' => array(),
					),
				),
			),
		);
	}
}
