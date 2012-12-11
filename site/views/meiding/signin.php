<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<?php HeadWidget::selfLoad(array('title'=>'登录'))?>
</head>
<body>
<?php NavigateWidget::selfLoad()?>
<div class="content">

			<form method="post" action="<?php echo $model->loginUrl?>" id="signin">
				<table  style="margin:0 auto">
					<tr>
						<td><b>昵称：</b></td><td height="40px"><input class="input :required" name="username" id="username" type="text" /></td>
					</tr>
					<tr>
						<td><b>密码：</b></td><td height="40px"><input class="input :required" name="password" id="password" type="password"/></td>
					</tr>
					<tr>
						<td></td><td><input style="position:relative; top:2px;" type="checkbox" name="rememberme"/>下次自动登录</td>
					</tr>
					<?php if(isset($model->errorMsg)): ?>
					<tr>
						<td></td><td style="color:red"><?php echo $model->errorMsg ?></td>
					</tr>
					<?php endif;?>
					<tr>
						<td></td><td height="100px"><input style="margin:0 0 0 60px" class="l_s" type="submit" value="登录" /></td>
					</tr>
				</table>
			</form>
</div>
<script src="<?php echo MD::app()->getJs('vanadium.js') ?>" type="text/javascript" charset="utf-8"></script>
<script src="<?php echo MD::app()->getJs('validateRule.js') ?>" type="text/javascript" charset="utf-8"></script>
<?php FootWidget::selfLoad()?>
</body>
</html>
