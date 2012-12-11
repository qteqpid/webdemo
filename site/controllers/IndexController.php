<?php

class IndexController extends Controller{
	
	public function actionIndex() {
		if (MD::app()->user->isLogged()) {
			$this->render("home.php");
		} else {
			$this->render("index.php");
		}
    }
}

