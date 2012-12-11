<?php
/**
 * 
 * CViewRender作为一个系统级别的组件可以用来，在不是用模板的情况下渲染视图文件
 * 视图文件必须存放在配置好的视图文件夹下
 * @author instreet
 *
 */
class CViewRender extends  AbstractViewRender{
	
	/**
	 * 
	 * 重写父类函数，具体渲染页面
	 */
	public function render(){
		if(($viewFile = $this->getViewFile($this->view)) != FALSE ) {
			if (!empty($this->data) && is_array($this->data)) {
				foreach($this->data as $k=>$v) {
					${$k} = $v;
				}
			}
			require_once ($viewFile);
		} else {
			throw new CException('render failed. view='.$this->view);
		}
	}
	
	/**
	 * 获取对应视图文件的完整文件路径
	 * @param string $view 视图文件名
	 * @return string 找到的话返回文件路径，否则返回FALSE
	 */
	public function getViewFile($view) {
		$viewFile = $this->getViewDir().$view;
		if (is_file($viewFile)) {
			return $viewFile;
		} else {
			return FALSE;
		}
	}
	
	/**
	 * 
	 * 获取存放视图文件的文件夹
	 */
	public  function getViewDir() {
		$dir = MD::app()->baseDir;
		if (!empty(MD::app()->module)) {
			$dir .= MD::app()->params['moduleDir'] . DS . MD::app()->module . DS; 
		}
		$dir .= MD::app()->params['viewDir'] . DS . MD::app()->thememanager->getThemeName() . DS;
		return $dir;
	}
}

?>