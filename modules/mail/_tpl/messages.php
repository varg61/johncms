<!-- //TODO: Переделать ссылку -->
<div class="phdr"><strong><?= __('сorrespondence') ?> <a href="<?= Vars::$HOME_URL ?>/profile?user=<?= Vars::$ID ?>"><?= $this->login ?></a></strong></div>
<?= $this->error_add ?>
<?php if (!$this->ignor): ?>
<div>
    <form name="form" action="<?= $this->link ?>?act=messages&amp;id=<?= Vars::$ID ?>" method="post" enctype="multipart/form-data">
        <div class="gmenu">
            <strong><?= __('message') ?>:</strong><br/>
            <?php if (!Vars::$IS_MOBILE): ?>
            <?= TextParser::autoBB('form', 'text') ?>
            <?php endif ?>
            <textarea rows="<?= Vars::$USER_SET['field_h'] ?>" name="text"><?= $this->text ?></textarea><br/>
            <small><?= __('text_size') ?></small>
            <br/>
            <strong><?= __('file') ?>:</strong><br/>
            <input type="file" name="0"/><br/>
            <small><?= __('max_file_size') ?> <?= $this->size ?> кб.</small>
            <br/>
            <input type="hidden" name="MAX_FILE_SIZE" value="<?= $this->maxsize ?>"/>
            <input type="hidden" name="token" value="<?= $this->token ?>"/>
            <p><input type="submit" name="submit" value="<?= __('sent') ?>"/></p>
        </div>
    </form>
</div>
<div class="phdr"><a href="<?= Vars::$HOME_URL ?>help/?act=trans"><?= __('translit') ?></a> | <a href="<?= Vars::$HOME_URL ?>smilies/"><?= __('smilies') ?></a></div>
<?php endif ?>
<?= $this->ignor ?>
<?= $this->list ?>
<p>
<div class="func">
    <a href="<?= $this->link ?>?act=messages&amp;mod=cleaning&amp;id=<?= Vars::$ID ?>"><?= __('cleaning') ?></a>
</div>
</p>
<p>
    <a href="<?= $this->link ?>"><?= __('mail') ?></a><br/>
    <a href="<?= Vars::$HOME_URL ?>contacts/"><?= __('contacts') ?></a>
</p>
