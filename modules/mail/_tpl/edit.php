<div class="phdr"><strong><?= __('edit_message') ?></strong></div>
<?= $this->mail_error ?>
<div>
    <form name="form" action="<?= $this->url ?>" method="post">
        <div class="gmenu">
			<strong><?= __('message') ?>:</strong><br/>
            <?php if (!Vars::$IS_MOBILE): ?>
            <?= TextParser::autoBB('form', 'text') ?>
            <? endif ?>
            <textarea rows="<?= Vars::$USER_SET['field_h'] ?>" name="text"><?php echo $this->text ?></textarea><br/>
            <small><?= __('text_size') ?></small><br/>
            <input type="hidden" name="token" value="<?= $this->token ?>"/>
            <p><input type="submit" name="submit" value="<?= __('sent') ?>"/></p>
        </div>
    </form>
</div>
<div class="phdr"><a href="<?= Vars::$HOME_URL ?>help/?act=trans"><?= __('translit') ?></a> | <a href="<?= Vars::$HOME_URL ?>smilies/"><?= __('smileys') ?></a></div>
<p>
    <a href="<?= Router::getUrl(2) ?>"><?= __('mail') ?></a><br/>
    <a href="<?= Vars::$HOME_URL ?>contacts/"><?= __('contacts') ?></a>
</p>