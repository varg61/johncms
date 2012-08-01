<div class="phdr"><strong><?= lng('sending') ?></strong></div>
<?= $this->mail_error ?>
<? if(empty($this->error) && $this->post) :?>
<div class="gmenu"><?php echo lng('sending_message_all_users') ?></div>
<? endif ?>
<div>
    <form name="form" action="<?= $this->url ?>" method="post">
        <div class="gmenu">
            <strong><?= lng('message') ?>:</strong><br />
            <?php if (!Vars::$IS_MOBILE): ?>
            <?= TextParser::autoBB('form', 'text') ?>
            <? endif ?>
            <textarea rows="<?= Vars::$USER_SET['field_h'] ?>" name="text"><?= $this->text ?></textarea><br />
            <small><?= lng('text_size') ?></small><br />
            <strong><?= lng('who_sending') ?>:</strong><br />
            <input type="radio" name="sending" value="0" checked="checked"/> <?= lng('all_users') ?><br />
			<input type="radio" name="sending" value="1"/> <?= lng('who_online') ?><br />
			<input type="radio" name="sending" value="2"/> <?= lng('girl_sending') ?><br />
			<input type="radio" name="sending" value="3"/> <?= lng('men_sending') ?><br />
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