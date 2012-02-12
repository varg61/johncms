<div class="phdr"><h3><?=$this->lng['write_message']?></h3></div>
<?= $this->mail_error ?>
<div>
<form name="form" action="<?= Vars::$MODULE_URI ?>?act=add" method="post" enctype="multipart/form-data">
<div class="gmenu">
<b><?=Vars::$LNG['nick']?>:</b><br />
<input type="text" name="login" value="<?= $this->login ?>"/><br />
<strong><?=Vars::$LNG['message']?>:</strong><br />
<? if (!Vars::$IS_MOBILE): ?>
	<?= TextParser::autoBB('form', 'text') ?>
<? endif ?>
<textarea rows="<?=Vars::$USER_SET['field_h']?>" name="text"><?= $this->text ?></textarea><br />
<strong><?=$this->lng['file']?>:</strong><br />
<input type="file" name="0"/>
<p><input type="submit" name="submit" value="<?=Vars::$LNG['sent']?>"/></p>
</div>
</form>
</div>
<div class="phdr"><a href="<?= Vars::$HOME_URL ?>/pages/faq.php?act=trans"><?=Vars::$LNG['translit']?></a> | <a href="<?= Vars::$HOME_URL ?>/pages/smileys.php"><?=Vars::$LNG['smileys']?></a></div>
<p>&laquo; <a href="<?= Vars::$MODULE_URI ?>"><?=Vars::$LNG['contacts']?></a></p>