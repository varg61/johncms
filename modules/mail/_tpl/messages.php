<div class="phdr"><h3><?= lng('сorrespondence') ?> <a href="<?= Vars::$HOME_URL ?>/users/profile.php?user=<?= Vars::$ID ?>"><?= $this->login ?></a></h3></div>
<?= $this->error_add ?>
<?php if (!$this->ignor): ?>
<div>
    <form name="form" action="<?= Vars::$MODULE_URI ?>?act=messages&amp;id=<?=Vars::$ID?>" method="post" enctype="multipart/form-data">
        <div class="gmenu">
            <strong><?= lng('message') ?>:</strong><br/>
            <? if (!Vars::$IS_MOBILE): ?>
            <?= TextParser::autoBB('form', 'text') ?>
            <? endif ?>
            <textarea rows="<?=Vars::$USER_SET['field_h']?>" name="text"><?= $this->text ?></textarea><br/>
            <strong><?= lng('file') ?>:</strong><br/>
            <input type="file" name="0"/>
            <p><input type="submit" name="submit" value="<?= lng('sent') ?>"/></p>
        </div>
    </form>
</div>
<div class="phdr">
    <a href="<?= Vars::$HOME_URL ?>/help?act=trans"><?= lng('translit') ?></a> | <a href="<?= Vars::$HOME_URL ?>/smileys"><?= lng('smileys') ?></a>
</div>
<?php endif ?>
<?= $this->ignor ?>
<?= $this->list ?>
<p>&laquo; <a href="<?= Vars::$MODULE_URI ?>"><?= lng('contacts') ?></a></p>
