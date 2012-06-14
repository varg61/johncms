<div class="phdr"><strong><?= lng('сorrespondence') ?> <a href="<?= Vars::$HOME_URL ?>/profile?user=<?= Vars::$ID ?>"><?= $this->login ?></a></strong></div>
<?= $this->error_add ?>
<?php if (!$this->ignor): ?>
<div>
    <form name="form" action="<?= Vars::$MODULE_URI ?>?act=messages&amp;id=<?= Vars::$ID ?>" method="post" enctype="multipart/form-data">
        <div class="gmenu">
            <strong><?= lng('message') ?>:</strong><br/>
            <?php if (!Vars::$IS_MOBILE): ?>
            <?= TextParser::autoBB('form', 'text') ?>
            <?php endif ?>
            <textarea rows="<?= Vars::$USER_SET['field_h'] ?>" name="text"><?= $this->text ?></textarea><br/>
            <small><?= lng('text_size') ?></small>
            <br/>
            <strong><?= lng('file') ?>:</strong><br/>
            <input type="file" name="0"/><br/>
            <small><?= lng('max_file_size') ?> <?= $this->size ?> кб.</small>
            <br/>
            <input type="hidden" name="MAX_FILE_SIZE" value="<?= $this->maxsize ?>"/>
            <input type="hidden" name="token" value="<?= $this->token ?>"/>
            <p><input type="submit" name="submit" value="<?= lng('sent') ?>"/></p>
        </div>
    </form>
</div>
<div class="phdr"><a href="<?= Vars::$HOME_URL ?>/help?act=trans"><?= lng('translit') ?></a> | <a href="<?= Vars::$HOME_URL ?>/smileys"><?= lng('smileys') ?></a></div>
<?php endif ?>
<?= $this->ignor ?>
<?= $this->list ?>
<p>
<div class="func">
    <a href="<?= Vars::$MODULE_URI ?>?act=messages&amp;mod=cleaning&amp;id=<?= Vars::$ID ?>"><?= lng('cleaning') ?></a>
</div>
</p>
<p>
    <a href="<?= Vars::$MODULE_URI ?>"><?= lng('mail') ?></a><br/>
    <a href="<?= Vars::$HOME_URL ?>/contacts"><?= lng('contacts') ?></a>
</p>
