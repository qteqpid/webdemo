<?php include_once("head.php") ?>
<?php 
	$quit = FALSE;
?>

<!-- <h2><?php echo $i_message['install_setting'];?></h2> -->
<form method="post" action="index.php?v=3" id="install" onSubmit="return check(this);">
<div class="shade">
<div class="settingHead"><?php echo $i_message['install_mysql'];?></div>

<h5><?php echo $i_message['install_mysql_host'];?></h5>
<p><?php echo $i_message['install_mysql_host_intro'];?></p>
<p><input type="text" name="db_host" value="localhost" size="40" class='input' /></p>

<h5><?php echo $i_message['install_mysql_username'];?></h5>
<p><input type="text" name="db_username" value="root" size="40" class='input' /></p>

<h5><?php echo $i_message['install_mysql_password'];?></h5>
<p><input type="password" name="db_password" value="" size="40" class='input' /></p>

<h5><?php echo $i_message['install_mysql_name'];?></h5>
<p><input type="text" name="db_name" value="sns_social" size="40" class='input' /></p>

<h5><?php echo $i_message['site_url'];?></h5>
<p><input type="text" name="site_url" value="http://localhost/" size="40" class='input' /></p>

</div>

<div class="center">
	<input type="button" class="submit" name="prev" value="<?php echo $i_message['install_prev'];?>" onClick="history.go(-1)">&nbsp;
	<input type="submit" class="submit" name="next" value="<?php echo $i_message['install_next'];?>">
</form>
</div>
<script type="text/javascript" language="javascript">
function check(obj)
{
	if (!obj.db_host.value)
	{
		alert('<?php echo $i_message['install_mysql_host_empty'];?>');
		obj.db_host.focus();
		return false;
	}
	else if (!obj.db_username.value)
	{
		alert('<?php echo $i_message['install_mysql_username_empty'];?>');
		obj.db_username.focus();
		return false;
	}
	else if (!obj.db_name.value)
	{
		alert('<?php echo $i_message['install_mysql_name_empty'];?>');
		obj.db_name.focus();
		return false;
	}
	else if (!obj.site_url.value)
	{
		alert('<?php echo $i_message['install_mysql_siteurl_empty'];?>');
		obj.site_url.focus();
		return false;
	}
	return true;
}
</script>

<?php include_once("foot.php") ?>
