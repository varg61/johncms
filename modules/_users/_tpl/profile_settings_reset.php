<ul class="nav">
    <li><h1<?= $this->user['id'] == Vars::$USER_ID ? ' class="section-personal"' : '' ?>><?= lng('system_settings') ?></h1></li>
</ul>

<div class="form-container">
    <form action="<?= Vars::$URI ?>?act=settings&amp;mod=reset&amp;user=<?= $this->user['id'] ?>" method="post">
        <div class="form-block align-center">
            <div class="info-message"><?= lng('reset_settings_warning') ?></div>
            <input class="btn btn-primary btn-large" type="submit" name="submit" value="<?= lng('save') ?>"/>
            <a class="btn btn-large" href="<?= Vars::$URI ?>?act=settings&amp;user=<?= $this->user['id'] ?>"><?= lng('cancel') ?></a>
            <input type="hidden" name="form_token" value="<?= $this->form_token ?>"/>
        </div>
    </form>
</div>
<div class="phdr">
    <a href="<?= Vars::$URI ?>?act=settings&amp;user=<?= $this->user['id'] ?>"><?= lng('back') ?></a>
</div>