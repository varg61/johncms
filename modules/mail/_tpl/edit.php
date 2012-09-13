<div class="phdr"><strong><?= lng('edit_message') ?></strong></div>
<?= $this->mail_error ?>
<div>
    <form name="form" action="<?= $this->url ?>" method="post">
        <div class="gmenu">
			<strong><?= lng('message') ?>:</strong><br/>
            <?php if (!Vars::$IS_MOBILE): ?>
            <?= TextParser::autoBB('form', 'text') ?>
            <? endif ?>
            <textarea rows="<?= Vars::$USER_SET['field_h'] ?>" name="text"><?php echo $this->text ?></textarea><br/>
            <small><?= lng('text_size') ?></small><br/>
            <input type="hidden" name="token" value="<?= $this->token ?>"/>
            <p><input type="submit" name="submit" value="<?= lng('sent') ?>"/></p>
        </div>
    </form>
</div>
<div class="phdr"><a href="<?= Vars::$HOME_URL ?>/help?act=trans"><?= lng('translit') ?></a> | <a href="<?= Vars::$HOME_URL ?>/smileys"><?= lng('smileys') ?></a></div>
<p>
    <a href="<?= Vars::$MODULE_URI ?>"><?= lng('mail') ?></a><br/>
    <a href="<?= Vars::$HOME_URL ?>/contacts"><?= lng('contacts') ?></a>
</p>