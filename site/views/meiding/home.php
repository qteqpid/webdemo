<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head>
<?php HeadWidget::selfLoad()?>
<title>进来啦</title>
</head>
<body class="homepage">
	<?php NavigateWidget::selfLoad()?>
	<div class="content" style="font-size:50px">
		欢迎进来! <?php echo MD::app()->user->getUsername() ?>
	</div>
	<?php FootWidget::selfLoad()?>
</body>
</html>

