<?php
/**
 * 根据模块名称定位模块内部的存放的图片，样式，脚本
 * @author instreet
 */

if (isset($route->module)) MD::addModuleImport($route->module, array('models','widgets','components')); // 加载常用的目录

class Module {
	protected  static  $relativeThemeDir = null;
	protected  static  $realThemeDir = null;
	
	static public function getRelativeThemeDir(){
		if(empty(self::$relativeThemeDir)){
			$dir = '/'.MD::app()->params['siteDir'].'/'.MD::app()->params['moduleDir'].'/'.MD::app()->module.'/'; 
			$dir .= MD::app()->params['themeDir'].'/'.MD::app()->thememanager->getThemeName().'/';
			self::$relativeThemeDir = $dir;
		}
		return self::$relativeThemeDir;
	}
	
	static public function getRealThemeDir(){
		if(empty(self::$realThemeDir)){
			$dir = ROOT.MD::app()->params['siteDir'].DS.MD::app()->params['moduleDir'].DS.MD::app()->module.DS; 
			$dir .= MD::app()->params['themeDir'].DS.MD::app()->thememanager->getThemeName().DS;
			self::$realThemeDir = $dir;
		}
		return self::$realThemeDir;
	}
	
	static public function getModuleDir(){
		return ROOT.MD::app()->params['siteDir'].DS.MD::app()->params['moduleDir'].DS.MD::app()->module.DS;
	}
	
	static public function getCss($css) {
		$relativeThemeDir = self::getRelativeThemeDir();
		$realThemeDir = self::getRealThemeDir();
		$tail = MD::app()->params['cssDir']."/".$css;
		return $relativeThemeDir.$tail.'?v='.getSvnVersion($realThemeDir.$tail);
	}
	
	static public function getJs($js) {
		$relativeThemeDir = self::getRelativeThemeDir();
		$realThemeDir = self::getRealThemeDir();
	
		$tailPartOne = MD::app()->params['jsDir']."/" ;
		if(ONLINE && file_exists($realThemeDir.$tailPartOne."min-".$js)){
			$tail = $tailPartOne ."min-".$js;
		}else{
			$tail = $tailPartOne. $js;
		}
		return $relativeThemeDir.$tail.'?v='.getSvnVersion($realThemeDir.$tail);
	}
	
	static public function getImage($image) {
		$relativeThemeDir = self::getRelativeThemeDir();
		$realThemeDir = self::getRealThemeDir();
		$tail = MD::app()->params['imageDir']."/".$image;
		return $relativeThemeDir.$tail;
		return $relativeThemeDir.$tail.'?v='.getSvnVersion($realThemeDir.$tail);
	}
}
?>
