<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>访问出错</title>
<link type="text/css" rel="stylesheet" href="<?php echo MD::app()->getCss('reset.css')?>" />
<link type="text/css" rel="stylesheet" href="<?php echo MD::app()->getCss('normal.css')?>"></link>
<link type="text/css" rel="stylesheet" href="<?php echo MD::app()->getCss('help.css')?>"></link>
</head>

<body id="error_body">
<?php NavigateWidget::selfLoad()?>
	<div id="error_wrap">
	   <!--<div id="error_chain_left"></div>
	   <div id="error_chain_right"></div>-->
	   <div id="error_content">
			<h2>很抱歉, 你要打开的链接发生错误....</h2>
			<h2>我们建议你尝试以下页面</h2>
			<div>
			<a href="/" class="home">- 主页</a>
			<a href="/square" class="com">- 社区</a>
			</div>
		</div>
	</div>

<?php BottomWidget::selfLoad()?>
</body>
</html>
