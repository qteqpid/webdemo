<?php

class SignupController extends Controller {

	/*
	 * 注册动作入口
	 */
	public function actionIndex($msg='', $userForm=null) {
		$model = new stdClass();
		$model->msg = $msg;//初始化提示信息
		$model->actionLink = $this->createSimpleUrl('signup', 'submit');
		$model->userForm = ($userForm === null) ? new UserForm() : $userForm;
		$this->render('signup.php',array('model'=>$model));
	}

	/*
	 * 处理注册信息提交
	 */
	public function actionSubmit() {
		if (isset($_POST['User'])) {
			$userForm = new UserForm($_POST['User']);
			if(!$userForm->validate()){
				$this->actionIndex($userForm->errorMessage, $userForm);
				return;
			}
			if(!UserModel::model()->addUser($userForm->getFormData())){
				$this->actionIndex(UserModel::model()->errorMessage, $userForm);
				return;
			}
			$this->redirect('/signin');
		} else {
			$this->actionIndex();
		}
	}
	
	public function filters() {
        return array(
            array(
                'class' => 'CAccessControllFilter',
                'rules' => array(
                    array('deny',
                    	'actions' => array('index','submit'),
                        'users' => array('@'),
                    ),
                ),
            ),
        );
    }
	
}
