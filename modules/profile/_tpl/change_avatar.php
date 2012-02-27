<div class="phdr">
    <a href="<?= Vars::$URI ?>?user=<?= $this->user['id'] ?>"><b><?= ($this->user['id'] != Vars::$USER_ID ? Vars::$LNG['profile'] : $this->lng['my_profile']) ?></b></a> | <?= Vars::$LNG['edit'] ?>
</div>
<div class="gmenu">
    <div class="formblock">
        <label><?=  Vars::$LNG['avatar'] ?></label>
        <?php if (isset($this->avatar)) : ?>
        <br/><img src="<?= Vars::$HOME_URL ?>/files/users/avatar/<?= $this->user['id'] ?>.gif" width="32" height="32" alt="<?= $this->user['nickname'] ?>" border="0"/><br/>
        <?php endif ?>
    </div>
    <ul class="formblock">
        <li><a href="<?= Vars::$URI ?>?act=upload_avatar&amp;user=<?= $this->user['id'] ?>"><?= $this->lng['upload_image'] ?></a></li>
        <li><a href="<?= Vars::$URI ?>?act=upload_avatar&amp;user=<?= $this->user['id'] ?>"><?= $this->lng['upload_animation'] ?></a></li>
        <?php if ($this->user['id'] == Vars::$USER_ID) : ?>
        <li><a href="<?= Vars::$HOME_URL ?>/avatars"><?= $this->lng['select_in_catalog'] ?></a></li>
        <?php endif ?>
        <?php if (isset($this->avatar)) : ?>
        <li><a href="<?= Vars::$URI ?>?act=delete_avatar&amp;user=<?= $this->user['id'] ?>"><?= Vars::$LNG['delete'] ?></a></li>
        <?php endif ?>
    </ul>
</div>
<div class="phdr">
    <a href="<?= Vars::$URI ?>?user=<?= $this->user['id'] ?>"><?= Vars::$LNG['back'] ?></a>
</div>