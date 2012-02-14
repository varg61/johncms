<div class="phdr">
    <a href="<?= Vars::$MODULE_URI ?>?user=<?= $this->user['id'] ?>"><b><?= ($this->user['id'] != Vars::$USER_ID ? Vars::$LNG['profile'] : $this->lng['my_profile']) ?></b></a> |
    <?= $this->lng['delete_photo'] ?>
</div>
<form action="<?= Vars::$URI ?>?act=delete_photo&amp;user=<?= $this->user['id'] ?>" method="post">
    <div class="rmenu">
        <p><img src="<?= Vars::$HOME_URL ?>/files/users/photo/<?= $this->user['id'] ?>_small.jpg" alt="" border="0"/></p>
        <p><?= $this->lng['delete_photo_warning'] ?></p>
        <p style="margin-top: 10px"><input type="submit" value="<?= Vars::$LNG['delete'] ?>" name="submit"/></p>
    </div>
</form>
<div class="phdr"><a href="<?= Vars::$URI ?>?user=<?= $this->user['id'] ?>"><?= Vars::$LNG['back'] ?></a></div>