<div class="nav">
	<ul>
	<?php if (!MD::app()->user->isLogged()): ?>
	    <li class="fr"><a class="link" href="/signin">登录</a></li>
	    <li class="fr"><a class="link" href="/signup">注册</a></li>
	<?php else: ?>
		<li class="fr"><a class="link" href="/signout">退出</a></li>
	<?php endif; ?>
	</ul>
</div>	
