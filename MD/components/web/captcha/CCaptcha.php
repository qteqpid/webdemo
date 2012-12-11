<?php
/*
 * 使用PHP生成验证码图像：
 * 1、创建图片并填充背景
 * 2、生成随机验证码
 * 3、生成干扰线和雪花
 * 4、生成验证码文字
 * 5、输出
 */

class CCaptcha {
	
	private $charset = 'abcdefghkmnprstuvwxyzABCDEFGHKMNPRSTUVWXYZ23456789';
	private $code;         //验证码
	private $codelen = 5;  //验证码长度
	private $width = 120;  //宽度
	private $height = 30;  //高度
	private $img;          //图像资源句柄
	private $font;         //字体
	private $fontcolor;    //字体颜色
	private $fontsize = 20;//字体大小

	public function __construct(){
		$this->font = ROOT.'site/themes/meiding/font/COUR.TTF';
	}

	// 生成验证码图片
	public function generate(){
		$this->createBg();
		$this->createCode();
		$this->createLine();
		$this->createFont();
	 }
	 
	//输出到浏览器
	public function outPutToBrowser(){
		$_SESSION['captcha'] = $this->getCode();
		header('Content-Type:image/png');
		imagepng($this->img);
		imagedestroy($this->img);
	}

	// 输出到文件
	public function outPutToFile() {
		/*
		imagepng($this->img,$this->filePath); //FIXME: 补充filePath
		imagedestroy($this->img);
		*/
	}

	//获取验证码
	public function getCode(){
		return strtolower($this->code);
	}
	
	//生成验证码
	private function createCode(){
		$_len = strlen($this->charset)-1;
		for ($i=0;$i<$this->codelen;$i++) {
			$this->code .= $this->charset[mt_rand(0,$_len)];
		}
	}

	//生成背景
	private function createBg(){
		$this->img = imagecreatetruecolor($this->width, $this->height);
		$color = imagecolorallocate($this->img, mt_rand(157,255), mt_rand(157,255), mt_rand(157,255));
		imagefilledrectangle($this->img,0,$this->height,$this->width,0,$color);
	}
	
	//生成线条和雪花
	private function createLine(){
		for ($i=0;$i<3;$i++) {
			$color = imagecolorallocate($this->img,mt_rand(0,156),mt_rand(0,156),mt_rand(0,156));
			imageline($this->img,mt_rand(0,$this->width),mt_rand(0,$this->height),mt_rand(0,$this->width),mt_rand(0,$this->height),$color);
		}
		/*
		for ($i=0;$i<80;$i++) {
			$color = imagecolorallocate($this->img,mt_rand(200,255),mt_rand(200,255),mt_rand(200,255));
			imagestring($this->img,mt_rand(1,5),mt_rand(0,$this->width),mt_rand(0,$this->height),'*',$color);
		}*/
	}
	
	//生成文字
	private function createFont(){
		$_x = $this->width / $this->codelen;
		for ($i=0;$i<$this->codelen;$i++) {
			$this->fontcolor = imagecolorallocate($this->img,mt_rand(0,156),mt_rand(0,156),mt_rand(0,156));
			imagettftext($this->img,$this->fontsize,mt_rand(-30,30),$_x*$i+mt_rand(1,5),$this->height / 1.4,$this->fontcolor,$this->font,$this->code[$i]);
		}
	}
}
