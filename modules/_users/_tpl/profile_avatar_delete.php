<ul class="nav">
    <li><h1<?= $this->user['id'] == Vars::$USER_ID ? ' class="section-personal"' : '' ?>><?= __('delete_avatar') ?></h1></li>
</ul>
<div class="form-container">
    <div class="form-block">
        <?= Functions::displayUser($this->user, array('iphide' => 1,)) ?>
    </div>
    <form action="<?= Vars::$URI ?>?act=avatar_delete&amp;user=<?= $this->user['id'] ?>" method="post">
        <div class="form-block align-center">
            <div class="info-message"><?= __('delete_avatar_warning') ?></div>
            <input class="btn btn-primary btn-large" type="submit" value=" <?= __('delete') ?> " name="submit"/>
            <a class="btn" href="<?= Vars::$URI ?>?act=settings&amp;user=<?= $this->user['id'] ?>"><?= __('cancel') ?></a>
            <input type="hidden" name="form_token" value="<?= $this->form_token ?>"/>
        </div>
    </form>
</div>