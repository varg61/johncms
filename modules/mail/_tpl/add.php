<div class="phdr"><strong><?php echo lng( 'write_message' ) ?></strong></div>
<?php echo $this->mail_error ?>
<div>
<form name="form" action="<?php echo Vars::$MODULE_URI ?>?act=add" method="post" enctype="multipart/form-data">
<div class="gmenu">
<b><?php echo lng( 'nick' ) ?>:</b><br />
<input type="text" name="login" value="<?php echo $this->login ?>"/><br />
<strong><?php echo lng( 'message' ) ?>:</strong><br />
<?php if (!Vars::$IS_MOBILE): ?>
	<?php echo TextParser::autoBB('form', 'text') ?>
<? endif ?>
<textarea rows="<?php echo Vars::$USER_SET['field_h'] ?>" name="text"><?php echo $this->text ?></textarea><br />
<small><?php echo lng( 'text_size' ) ?></small><br />
<strong><?php echo lng( 'file' ) ?>:</strong><br />
<input type="file" name="0"/><br />
<small><?php echo lng( 'max_file_size' ) ?> <?php echo $this->size ?> кб.</small><br />
<input type="hidden" name="token" value="<?= $this->token ?>"/>
<input type="hidden" name="MAX_FILE_SIZE" value="<?php echo $this->maxsize ?>" />
<p><input type="submit" name="submit" value="<?php echo lng( 'sent' ) ?>"/></p>
</div>
</form>
</div>
<div class="phdr"><a href="<?php echo Vars::$HOME_URL ?>/help?act=trans"><?php echo lng( 'translit' ) ?></a> | <a href="<?php echo Vars::$HOME_URL ?>/smileys"><?php echo lng( 'smileys' ) ?></a></div>
<p>
<a href="<?php echo Vars::$MODULE_URI ?>"><?php echo lng( 'mail' ) ?></a><br />
<a href="<?php echo Vars::$MODULE_URI ?>?act=contacts"><?php echo lng( 'contacts' ) ?></a>
</p>