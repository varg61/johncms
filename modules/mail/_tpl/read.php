<div class="phdr"><?php echo $this->pref ?>: <a href="<?php echo Vars::$HOME_URL ?>/profile?user=<?php echo $this->user_id ?>"><?php echo $this->contact_login ?></a> (<?php echo $this->time_message ?>)</div>
<div class="list1"><?php echo $this->text ?></div>
<div class="phdr"><a href="<?php echo Vars::$MODULE_URI ?>?act=<?php echo $this->back ?>"><?php echo lng( 'back' ) ?></a></div>
<p>
<a href="<?php echo Vars::$MODULE_URI ?>?act=contacts"><?php echo lng( 'contacts' ) ?></a><br />
<a href="<?php echo Vars::$MODULE_URI ?>"><?php echo lng( 'mail' ) ?></a>
</p>