<div class="phdr"><strong><?php echo lng( 'сorrespondence' ) ?> <a href="<?php echo Vars::$HOME_URL ?>/profile?user=<?php echo Vars::$ID ?>"><?php echo $this->login ?></a></strong></div>
<?php echo $this->error_add ?>
<?php if (!$this->ignor): ?>
<div><form name="form" action="<?php echo Vars::$MODULE_URI ?>?act=messages&amp;id=<?php echo Vars::$ID ?>" method="post" enctype="multipart/form-data">
<div class="gmenu">
<strong><?php echo lng( 'message' ) ?>:</strong><br />
<?php if (!Vars::$IS_MOBILE): ?>
	<?php echo TextParser::autoBB('form', 'text') ?>
<? endif; ?>
<textarea rows="<?php echo Vars::$USER_SET['field_h'] ?>" name="text"><?php echo $this->text ?></textarea><br />
<small><?php echo lng( 'text_size' ) ?></small><br />
<strong><?php echo lng( 'file' ) ?>:</strong><br />
<input type="file" name="0"/><br />
<small><?php echo lng( 'max_file_size' ) ?> <?php echo $this->size ?> кб.</small><br />
<input type="hidden" name="MAX_FILE_SIZE" value="<?php echo $this->maxsize ?>" />
<input type="hidden" name="token" value="<?php echo $this->token ?>"/>
<p><input type="submit" name="submit" value="<?php echo lng( 'sent' ) ?>"/></p>
</div>
</form>
</div>
<div class="phdr"><a href="<?php echo Vars::$HOME_URL ?>/help?act=trans"><?php echo lng( 'translit' ) ?></a> | <a href="<?php echo Vars::$HOME_URL ?>/smileys"><?php echo lng( 'smileys' ) ?></a></div>
<? endif; ?>
<?php echo $this->ignor ?>
<?php echo $this->list ?>

<p><a href="<?php echo Vars::$MODULE_URI ?>"><?php echo lng( 'contacts' ) ?></a></p>
