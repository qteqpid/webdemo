<?php

class CThemeManager extends AbstractComponent {
		
	private $_siteTheme; // 主题名
	
	private $_themeDir;  //主题根目录
	
	private $_relativePath; //相对应用根目录的路径
	
	private $_realPath;		//文件系统中的路径

	public function init() {
		$this->_siteTheme = MD::app()->theme;
	}
	
	public function updateTheme($theme) {
		$this->_siteTheme = $theme;
		$this->_themeDir = null;
		$this->_relativePath = null;
	}
	
	public function getThemeDir() {
		if (!empty($this->_themeDir)) return $this->_themeDir;
		return $this->_themeDir = MD::app()->baseDir.MD::app()->params['themeDir'].DS.$this->_siteTheme.DS;
	}
	
	public function getRelativePath() {
		if (!empty($this->_relativePath)) return $this->_relativePath;
		$relativePath = "/".MD::app()->params['siteDir']."/".MD::app()->params['themeDir']."/".$this->_siteTheme."/";
		return $this->_relativePath = $relativePath;
	}
	
	public function getRealPath(){
		if (!empty($this->_realPath)) return $this->_realPath;
		$realPath = ROOT.MD::app()->params['siteDir']."/".MD::app()->params['themeDir']."/".$this->_siteTheme."/";
		return $this->_realPath = $realPath;
	}
	
	public function getThemeName() {
		return $this->_siteTheme;
	}
	
	public function getCss($css) {
		$relativePath = $this->getRelativePath();
		$realPath = $this->getRealPath();
		$tail = MD::app()->params['cssDir']. "/" .$css;
		return $relativePath.$tail.'?v=0';//.getSvnVersion($realPath.$tail);
	}
	
	public function getJs($js) {
		$relativePath = $this->getRelativePath();
		$realPath = $this->getRealPath();
		$tailPartOne = MD::app()->params['jsDir']. "/" ;
		if(ONLINE && file_exists($realPath.$tailPartOne."min-".$js)){
			$tail = $tailPartOne ."min-".$js;
		}else{
			$tail = $tailPartOne. $js;
		}
		return $relativePath.$tail.'?v=0';//.getSvnVersion($realPath.$tail);
	}
	
	public function getImage($image) {
		$relativePath = $this->getRelativePath();
		$realPath = $this->getRealPath();
		$tail = MD::app()->params['imageDir']. "/" .$image;
		return $relativePath.$tail;
		return $relativePath.$tail;
	}
}
