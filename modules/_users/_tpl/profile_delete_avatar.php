<ul class="nav">
    <li><h1<?= $this->user['id'] == Vars::$USER_ID ? ' class="section-personal"' : '' ?>><?= lng('delete_avatar') ?></h1></li>
</ul>
<div class="form-container">
    <div class="form-block">
        <?= Functions::displayUser($this->user, array('iphide' => 1,)) ?>
    </div>
    <form action="<?= Vars::$URI ?>?act=edit&amp;mod=delete_avatar&amp;user=<?= $this->user['id'] ?>" method="post">
        <div class="form-block align-center">
            <div class="info-message"><?= lng('delete_avatar_warning') ?></div>
            <input class="btn btn-primary btn-large" type="submit" value=" <?= lng('delete') ?> " name="submit"/>
            <a class="btn btn-large" href="<?= Vars::$MODULE_URI ?>?act=edit&amp;mod=avatar"><?= lng('cancel') ?></a>
            <input type="hidden" name="form_token" value="<?= $this->form_token ?>"/>
        </div>
    </form>
</div>