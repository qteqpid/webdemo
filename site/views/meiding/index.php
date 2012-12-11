<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head>
<?php HeadWidget::selfLoad()?>
<title>首页</title>
</head>
<body class="homepage">
	<?php NavigateWidget::selfLoad()?>
	<div class="content" style="font-size:50px">
		Welcome to <?php echo MD::app()->siteName;?>
	</div>
	<?php FootWidget::selfLoad()?>
</body>
</html>

