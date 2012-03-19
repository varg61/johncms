<div class="phdr">
    <a href="<?= Vars::$URI ?>?user=<?= $this->user['id'] ?>"><b><?= ($this->user['id'] != Vars::$USER_ID ? lng('profile') : lng('my_profile')) ?></b></a> | <?= lng('edit') ?>
</div>
<div class="gmenu">
    <div class="formblock">
        <label><?=  lng('avatar') ?></label>
        <?php if (isset($this->avatar)) : ?>
        <br/><img src="<?= Vars::$HOME_URL ?>/files/users/avatar/<?= $this->user['id'] ?>.gif" width="32" height="32" alt="<?= $this->user['nickname'] ?>" border="0"/><br/>
        <?php endif ?>
    </div>
    <ul class="formblock">
        <?php if ($this->setUsers['upload_avatars'] || Vars::$USER_RIGHTS >= 7) : ?>
        <li><a href="<?= Vars::$URI ?>?act=upload_avatar&amp;user=<?= $this->user['id'] ?>"><?= lng('upload_image') ?></a></li>
        <?php endif ?>
        <?php if ($this->setUsers['upload_animation'] || Vars::$USER_RIGHTS >= 7) : ?>
        <li><a href="<?= Vars::$URI ?>?act=upload_animation&amp;user=<?= $this->user['id'] ?>"><?= lng('upload_animation') ?></a></li>
        <?php endif ?>
        <?php if ($this->user['id'] == Vars::$USER_ID) : ?>
        <li><a href="<?= Vars::$HOME_URL ?>/avatars"><?= lng('select_in_catalog') ?></a></li>
        <?php endif ?>
        <?php if (isset($this->avatar)) : ?>
        <li><a href="<?= Vars::$URI ?>?act=delete_avatar&amp;user=<?= $this->user['id'] ?>"><?= lng('delete') ?></a></li>
        <?php endif ?>
    </ul>
</div>
<div class="phdr">
    <a href="<?= Vars::$URI ?>?user=<?= $this->user['id'] ?>"><?= lng('back') ?></a>
</div>