<div class="phdr">
    <h3><?= lng('write_message') ?></h3>
</div>
<?= $this->mail_error ?>
<div>
    <form name="form" action="<?= Vars::$MODULE_URI ?>?act=add" method="post" enctype="multipart/form-data">
        <div class="gmenu">
            <b><?= lng('nick') ?>:</b><br/>
            <input type="text" name="login" value="<?= $this->login ?>"/><br/>
            <strong><?= lng('message') ?>:</strong><br/>
            <?php if (!Vars::$IS_MOBILE): ?>
            <?= TextParser::autoBB('form', 'text') ?>
            <?php endif ?>
            <textarea rows="<?=Vars::$USER_SET['field_h']?>" name="text"><?= $this->text ?></textarea><br/>
            <strong><?= lng('file') ?>:</strong><br/>
            <input type="file" name="0"/>
            <p><input type="submit" name="submit" value="<?= lng('sent') ?>"/></p>
        </div>
    </form>
</div>
<div class="phdr">
    <a href="<?= Vars::$HOME_URL ?>/pages/faq.php?act=trans"><?= lng('translit') ?></a> | <a href="<?= Vars::$HOME_URL ?>/pages/smileys.php"><?= lng('smileys') ?></a>
</div>
<p>&laquo; <a href="<?= Vars::$MODULE_URI ?>"><?= lng('contacts') ?></a></p>