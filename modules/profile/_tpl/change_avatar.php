<div class="phdr">
    <a href="<?= Vars::$MODULE_URI ?>?user=<?= $this->user['id'] ?>"><b><?= ($this->user['id'] != Vars::$USER_ID ? Vars::$LNG['profile'] : $this->lng['my_profile']) ?></b></a> | <?= Vars::$LNG['edit'] ?>
</div>
<form action="<?= Vars::$URI ?>?user=<?= $this->user['id'] ?>" method="post">
    <div class="gmenu">
        <div class="formblock">
            <label><?=  Vars::$LNG['avatar'] ?></label>
            <?php if (isset($this->avatar)) : ?>
            <br/><img src="<?= Vars::$HOME_URL ?>/files/users/avatar/<?= $this->user['id'] ?>.gif" width="32" height="32" alt="<?= $this->user['nickname'] ?>" border="0"/><br/>
            <?php endif ?>
            <div class="small">
                <a href="<?= Vars::$HOME_URL ?>/avatars"><?= Vars::$LNG['select'] ?></a> |
                <a href=""><?= $this->lng['upload'] ?></a>
                <?php if (isset($this->avatar)) : ?>
                | <a href="<?= Vars::$URI ?>?act=delete_avatar&amp;user=<?= $this->user['id'] ?>"><?= Vars::$LNG['delete'] ?></a>
                <?php endif ?>
            </div>
        </div>
        <div class="formblock">
            <input type="submit" value="<?= Vars::$LNG['save'] ?>" name="submit"/>
        </div>
    </div>
</form>
<div class="phdr">
    <a href="<?= Vars::$URI ?>?user=<?= $this->user['id'] ?>"><?= Vars::$LNG['back'] ?></a>
</div>