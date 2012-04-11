<div class="phdr"><strong><?php echo $this->phdr ?></strong></div>
<div class="gmenu">
<form name="form" action="<?php echo $this->urlSelect ?>" method="post"><div>
<strong><?php echo $this->select ?></strong><br />
<input type="hidden" name="token" value="<?= $this->token ?>"/>
<input type="submit" name="submit" value="<?php echo $this->submit ?>"/>
</div></form></div>
<div class="phdr">
<a href="<?php echo $this->urlBack ?>"><?php echo lng( 'back' ) ?></a>
</div>