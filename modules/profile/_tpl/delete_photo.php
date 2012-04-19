<div class="phdr">
    <a href="<?= Vars::$MODULE_URI ?>?act=edit&amp;user=<?= $this->user['id'] ?>"><b><?= ($this->user['id'] != Vars::$USER_ID ? lng('profile') : lng('my_profile')) ?></b></a> |
    <?= lng('delete_photo') ?>
</div>
<form action="<?= Vars::$URI ?>?act=edit&amp;mod=delete_photo&amp;user=<?= $this->user['id'] ?>" method="post">
    <div class="rmenu">
        <p><img src="<?= Vars::$HOME_URL ?>/files/users/photo/<?= $this->user['id'] ?>_small.jpg" alt="" border="0"/></p>
        <p><?= lng('delete_photo_warning') ?></p>
        <p style="margin-top: 10px"><input type="submit" value="<?= lng('delete') ?>" name="submit"/></p>
    </div>
</form>
<div class="phdr"><a href="<?= Vars::$URI ?>?act=edit&amp;user=<?= $this->user['id'] ?>"><?= lng('back') ?></a></div>