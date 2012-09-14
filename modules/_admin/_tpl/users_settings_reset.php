<ul class="nav">
    <li><h1 class="section-warning"><?= lng('users') ?> :: <?= lng('settings') ?></h1></li>
</ul>
<div class="form-container">
    <form action="<?= Vars::$URI ?>" method="post">
        <div class="form-block">
            <div class="info-message"><?= lng('reset_settings_warning') ?></div>
            <input class="btn btn-primary btn-large" type="submit" name="reset" value="<?= lng('save') ?>"/>
            <a class="btn btn-large" href="<?= Vars::$URI ?>?act=users_settings"><?= lng('cancel') ?></a>
        </div>
        <input type="hidden" name="form_token" value="<?= $this->form_token ?>"/>
    </form>
</div>