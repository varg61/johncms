<p><a href="<?php echo Vars::$MODULE_URI ?>?act=add"><?php echo lng( 'write_message' ) ?></a></p>
<div class="phdr"><strong><?php echo lng( 'mail' ) ?></strong></div>
<div class="topmenu"><a href="<?php echo Vars::$HOME_URL ?>/contacts"><?php echo lng( 'contacts' ) ?></a></div>
<div class="list1">
<p>
<?php echo Functions::getImage('mail-inbox.png') ?>&#160;<a href="<?php echo Vars::$MODULE_URI ?>?act=inmess"><?php echo lng( 'inmess' ) ?></a>&#160;(<?php echo $this->inmess ?>) <span class="red"><?php echo $this->newmess ?></span><br />
<?php echo Functions::getImage('mail-outbox.png') ?>&#160;<a href="<?php echo Vars::$MODULE_URI ?>?act=outmess"><?php echo lng( 'outmess' ) ?></a>&#160;(<?php echo $this->outmess ?>)<br />
</p>
</div>
<div class="gmenu"><?php echo lng( 'can_write' ) ?>:&#160;<a href="<?php Vars::$MODULE_URI ?>?act=settings"><?php echo $this->receive_mail ?></a></div>
<div class="list2">
<p>
<?php echo Functions::getImage('mail-elected.png') ?>&#160;<a href="<?php echo Vars::$MODULE_URI ?>?act=elected"><?php echo lng( 'elected' ) ?></a>&#160;(<?php echo $this->elected ?>)<br />
<?php echo Functions::getImage('mail-files.png') ?>&#160;<a href="<?php echo Vars::$MODULE_URI ?>?act=files"><?php echo lng( 'files' ) ?></a>&#160;(<?php echo $this->files ?>)<br />
<?php echo Functions::getImage('mail-trash.png') ?>&#160;<a href="<?php echo Vars::$MODULE_URI ?>?act=basket"><?php echo lng( 'basket' ) ?></a>&#160;(<?php echo $this->delete ?>)<br />
</p>
</div>