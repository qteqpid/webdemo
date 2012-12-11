<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<?php HeadWidget::selfLoad(array("title"=>'注册')) ?>
</head>
<body>
<?php NavigateWidget::selfLoad()?>

<div class="content">
	<form action="<?php echo $model->actionLink?>" method="post" name="form1" id="form1" autocomplete="off">
		<table style="margin:0 auto">
			<tr>
				<td><b>昵称：</b></td>
				<td height="40px"><input class="input :min_length;1" type="text" name="User[name]" id="name"
					maxlength="16" value="<?php echo htmlspecialchars($model->userForm->name)?>" /></td>
			</tr>
			<tr>
				<td><b>密码：</b></td>
				<td height="40px"><input class="input :min_length;6 :only_on_blur" type="password" name="User[passwd]"
					id="passwd" maxlength="32"
					value="<?php echo htmlspecialchars($model->userForm->passwd)?>" /></td>
			</tr>
			<tr>
				<td></td>
				<td height="70px"><input style="margin:2px 0 0 50px" class="r_s" type="submit" value="注册"/></td>
			</tr>
		</table>
	</form>
</div>

<script src="<?php echo MD::app()->getJs('vanadium.js') ?>" type="text/javascript" charset="utf-8"></script>
<script src="<?php echo MD::app()->getJs('validateRule.js') ?>" type="text/javascript" charset="utf-8"></script>
<?php FootWidget::selfLoad()?>
</body>
</html>
