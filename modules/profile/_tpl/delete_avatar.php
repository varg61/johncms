<div class="phdr">
    <a href="<?= Vars::$MODULE_URI ?>?user=<?= $this->user['id'] ?>"><b><?= ($this->user['id'] != Vars::$USER_ID ? lng('profile') : lng('my_profile')) ?></b></a> |
    <?= lng('delete_avatar') ?>
</div>
<form action="<?= Vars::$URI ?>?act=delete_avatar&amp;user=<?= $this->user['id'] ?>" method="post">
    <div class="rmenu">
        <p><img src="<?= Vars::$HOME_URL ?>/files/users/avatar/<?= $this->user['id'] ?>.gif" width="32" height="32" alt="" border="0"/></p>
        <p><?= lng('delete_avatar_warning') ?></p>
        <p style="margin-top: 10px"><input type="submit" value="<?= lng('delete') ?>" name="submit"/></p>
    </div>
</form>
<div class="phdr"><a href="<?= Vars::$URI ?>?act=avatar&amp;user=<?= $this->user['id'] ?>"><?= lng('back') ?></a></div>