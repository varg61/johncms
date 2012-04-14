<div class="phdr">
    <a href="<?= Vars::$MODULE_URI ?>?user=<?= $this->user['id'] ?>"><b><?= ($this->user['id'] != Vars::$USER_ID ? lng('profile') : lng('my_profile')) ?></b></a> |
    <?= lng('delete_avatar') ?>
</div>
<form action="<?= Vars::$URI ?>?act=edit&amp;mod=delete_avatar&amp;user=<?= $this->user['id'] ?>" method="post">
    <div class="rmenu">
        <div class="formblock">
            <img src="<?= Vars::$HOME_URL ?>/files/users/avatar/<?= $this->user['id'] ?>.gif" width="32" height="32" alt="" border="0"/>
        </div>
        <div class="formblock">
            <?= lng('delete_avatar_warning') ?>
        </div>
        <div class="formblock">
            <input type="submit" value="<?= lng('delete') ?>" name="submit"/>
        </div>
        <input type="hidden" name="form_token" value="<?= $this->form_token ?>"/>
    </div>
</form>
<div class="phdr"><a href="<?= Vars::$URI ?>?act=avatar&amp;user=<?= $this->user['id'] ?>"><?= lng('back') ?></a></div>