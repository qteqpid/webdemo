<?php
/**
 * 
 * 所有web的controller的直接基类
 * @author qteqpid
 *
 */
class Controller extends AbstractController{
	
	public $model;  // 用来渲染view的数据对象
	
	public function init() {}
	
    /**
     * url生成,适合在controller里使用
     * @param Route $route  route信息
     * @param array $params  GET参数，以key,value形式
     */
    public function createUrl($route, $params = array()) {
        return MD::app()->createUrl($route, $params);
    }  

    /**
     * 同上,区别是生成不含域名部分的短链接
     */
    public function createShortUrl($route, $params = array()) {
        return MD::app()->createShortUrl($route, $params);
    }  

    /**
     * 适合创建简单的url（只包含那三个参数的url）
     */
    public function createSimpleUrl($controller = NULL, $action = NULL, $module = NULL) {
        return MD::app()->createSimpleUrl($controller, $action, $module);
    }
    
	/**
	 * 获取组合后的图片链接和本地地址
	 * @param $productIds
	 * @param $prefix
	 * @param $dir //tmp,share
	 * @param $updateCycle //默认172800秒 = 两天
	 */
	public function getSharedPicData($name,$imageArr=null,$dir = 'share',$updateCycle=172800){
		$path = ROOT.$dir.DS.$name;
		
		$fullName = $name.".jpeg";
		$fullPath = ROOT.$dir.DS.$fullName;
		
		if(empty($imageArr)){
			if(!file_exists($fullPath)) return false;
			if($updateCycle > 0 && time() - filemtime($fullPath) >= $updateCycle) return false;
		}else{
			$layout = new ImageLayout();
			$ret = $layout->runTemplate($imageArr);
			if ($ret == ImageLayout::SUCCESS) {
				$img = $layout->getFinalImage();
				$imgTool = new ImageTool();
				$imgTool->saveImage($img, $path);
			}else{
				return false;
			}
		}
		$url = rtrim(MD::app()->siteUrl,DS).DS.$dir.DS.$fullName;
		return array('url' => $url,'path' => $fullPath);
	}
	
	/**
	 * 获取指定属性的数据
	 * @param array|obj $dataSource
	 * @param array $fileds
	 */
	protected function getFiledsData($dataSource ,$fileds){
		foreach($dataSource as $key => $data){
			if(in_array($key, $fileds)){
				if(is_array($dataSource)){
					if(!isset($resultData))$resultData = array();
					$resultData[$key] = $data;
				}else{
					if(!isset($resultData))$resultData = new stdClass();
					$resultData->$key = $data;
				}
			}
		}
		return $resultData;	
	}
	
		
	/**
	 * 过滤掉指定属性的数据
	 * @param array|obj $dataSource
	 * @param array $fileds
	 */
	protected function filterFiledsData($dataSource ,$fileds){
		foreach($dataSource as $key => $data){
			if(!in_array($key, $fileds)){
				if(is_array($dataSource)){
					if(!isset($resultData))$resultData = array();
					$resultData[$key] = $data;
				}else{
					if(!isset($resultData))$resultData = new stdClass();
					$resultData->$key = $data;
				}
			}
		}
		return $resultData;	
	}
	
}
