<div class="phdr">
    <a href="<?= Vars::$URI ?>?act=settings"><b><?= lng('settings') ?></b></a> | <?= lng('reset_settings') ?>
</div>
<div class="menu">
    <form action="<?= Vars::$URI ?>?act=settings&amp;mod=reset" method="post">
        <div class="formblock">
            <?= lng('reset_settings_warning') ?>
        </div>
        <div class="formblock">
            <input type="submit" name="submit" value="<?= lng('save') ?>"/>
        </div>
        <input type="hidden" name="form_token" value="<?= $this->form_token ?>"/>
    </form>
</div>
<div class="phdr">
    <a href="<?= htmlspecialchars($_SERVER['HTTP_REFERER']) ?>"><?= lng('back') ?></a>
</div>