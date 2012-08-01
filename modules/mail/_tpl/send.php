<div class="phdr"><strong><?= lng('write_message') ?></strong></div>
<?= $this->mail_error ?>
<div>
    <form name="form" action="<?= $this->url ?>" method="post">
        <div class="gmenu">
            <strong><?= lng('nick') ?>:</strong><br/>
            <input type="text" name="login" value="<?= $this->login ?>"/><br/>
            <?php if ($this->count_contact): ?>
            <strong>Или выберите из списка:</strong><br/>
            <select name="contact_id">
                <option value="">Выбрать контакт</option>
                <?php foreach ($this->query as $row): ?>
                <option value="<?= $row['id'] ?>"><?= $row['nickname'] ?></option>
                <?php endforeach ?>
            </select><br/>
            <? endif ?>
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