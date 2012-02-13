<div class="phdr">
    <a href="profile.php?user=<?= $this->user['id'] ?>"><b><?= ($this->user['id'] != Vars::$USER_ID ? Vars::$LNG['profile'] : $this->lng['my_profile']) ?></b></a> | <?= Vars::$LNG['edit'] ?>
</div>
<form action="profile.php?act=edit&amp;user=' . $user['user_id'] . '" method="post">
    <div class="gmenu">
        <h3 class="block">ID: <?= $this->user['id'] ?></h3>
        <?php if (Vars::$USER_RIGHTS >= 7) : ?>
        <?= Vars::$LNG['nick'] ?>: (<?= $this->lng['nick_lenght'] ?>)<br/><input type="text" value="<?= $this->user['nickname'] ?>" name="name"/><br/>
        <?= Vars::$LNG['status'] ?>: (<?= $this->lng['status_lenght'] ?>)<br/><input type="text" value="<?= $this->user['status'] ?>" name="status"/>
        <?php else : ?>
        <span class="gray"><?= Vars::$LNG['nick'] ?>:</span> <b><?= $this->user['nickname'] ?></b><br/>
        <span class="gray"><?= Vars::$LNG['status'] ?>:</span> <?= $this->user['status'] ?>
        <?php endif ?>
        <h3 class="block"><?=  Vars::$LNG['avatar'] ?></h3>
    </div>
</form>