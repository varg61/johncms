<div class="phdr">
    <a href="<?= Vars::$URI ?>"><b><?= lng('users') ?></b></a> | <?= lng('reset_settings') ?>
</div>
<div class="menu">
    <form action="<?= Vars::$URI ?>" method="post">
        <div class="formblock">
            <?= lng('reset_settings_warning') ?>
        </div>
        <div class="formblock">
            <input type="submit" name="reset" value="<?= lng('save') ?>"/>
        </div>
        <input type="hidden" name="form_token" value="<?= $this->form_token ?>"/>
    </form>
</div>
<div class="phdr">
    <a href="<?= Vars::$URI ?>?act=users_settings"><?= lng('back') ?></a>
</div>