<div class="phdr"><strong><?php echo $this->phdr ?></strong></div>
<div class="gmenu">
<form name="form" action="<?php echo $this->urlSelect ?>" method="post"><div>
<p><h3><?php echo lng('clear_param') ?></h3>
<input type="radio" name="cl" value="0" checked="checked" /><?php echo lng('clear_month') ?><br />
<input type="radio" name="cl" value="1" /><?php echo lng('clear_week') ?><br />
<input type="radio" name="cl" value="2" /><?php echo lng('clear_all') ?></p>
<input type="hidden" name="token" value="<?= $this->token ?>"/>
<input type="submit" name="submit" value="<?php echo $this->submit ?>"/>
</div></form></div>
<p>
<a href="<?php echo Vars::$MODULE_URI ?>"><?php echo lng( 'mail' ) ?></a><br />
<a href="<?php echo Vars::$HOME_URL ?>/contacts"><?php echo lng( 'contacts' ) ?></a>
</p>