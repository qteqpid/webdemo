<?php
/**
 * 
 * 测试Smarty模板在独立action中的使用效果
 * @author instreet
 *
 */
class TestAction extends AbstractAction{
	
	/**
	 * 
	 * viewRender对象缓冲
	 * @var AbstractViewRender $viewRender
	 */
	protected $viewRender;

	/**
	 * action动作执行的方法
	 * (non-PHPdoc)
	 * @see lastMeiding/MD/components/web/action/AbstractAction::run()
	 */
	public function run(){
		$model = new stdClass();
		$this->viewRender = MD::app()->viewRender;
		
		$model = new stdClass();
		$model->regUFO = new OauthUserForm();
		$model->loginUFO = new OauthUserForm();
		$model->which = 'login';
		
		$this->viewRender->renderPage("modal/window_bindAccount.php" , array('model' => $model));
	}
}
?>
