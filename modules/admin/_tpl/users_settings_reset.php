<div class="phdr">
    <a href="<?= Vars::$MODULE_URI ?>?act=users_settings"><b><?= lng('users') ?></b></a> | <?= lng('reset_settings') ?>
</div>
<div class="menu">
    <form action="<?= Vars::$URI ?>?act=users_settings" method="post">
        <div class="formblock">
            <?= lng('reset_settings_warning') ?>
        </div>
        <div class="formblock">
            <input type="submit" name="reset" value="<?= lng('save') ?>"/>
        </div>
        <input type="hidden" name="token" value="<?= $this->token ?>"/>
    </form>
</div>
<div class="phdr">
    <a href="<?= Vars::$MODULE_URI ?>?act=users_settings"><?= lng('back') ?></a>
</div>