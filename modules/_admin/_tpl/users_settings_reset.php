<ul class="nav">
    <li><h1 class="section-warning"><?= __('users') ?> :: <?= __('settings') ?></h1></li>
</ul>
<div class="form-container">
    <form action="<?= Vars::$URI ?>?act=users_settings" method="post">
        <div class="form-block align-center">
            <div class="info-message"><?= __('reset_settings_warning') ?></div>
            <input class="btn btn-primary btn-large" type="submit" name="reset" value="<?= __('save') ?>"/>
            <a class="btn" href="<?= Vars::$URI ?>?act=users_settings"><?= __('cancel') ?></a>
            <input type="hidden" name="form_token" value="<?= $this->form_token ?>"/>
        </div>
    </form>
</div>