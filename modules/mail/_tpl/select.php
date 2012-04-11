<div class="phdr"><strong><?php echo $this->phdr ?></strong></div>
<div class="gmenu">
<form name="form" action="<?php echo $this->urlSelect ?>" method="post"><div>
<strong><?php echo $this->select ?></strong><br />
<input type="hidden" name="token" value="<?= $this->token ?>"/>
<input type="submit" name="submit" value="<?php echo $this->submit ?>"/>
</div></form></div>
<p>
<a href="<?php echo Vars::$MODULE_URI ?>"><?php echo lng( 'mail' ) ?></a><br />
<a href="<?php echo Vars::$MODULE_URI ?>?act=contacts"><?php echo lng( 'contacts' ) ?></a>
</p>