<div class="phdr">
    <b><?= ($this->user['id'] != Vars::$USER_ID ? $this->lng['user_profile'] : $this->lng['my_profile']) ?></b>
</div>
<?php if (isset($this->menu)) : ?>
<div class="topmenu">
    <?= $this->menu ?>
</div>
<?php endif ?>
<div class="user">
    <p><?= Functions::displayUser($this->user, $this->userarg) ?></p>
</div>
<div class="list2">
    <p>
    <div><?= Functions::getImage('contacts.png') ?>&#160;<a href="profile.php?act=info&amp;user=<?= $this->user['id'] ?>"><?= Vars::$LNG['information'] ?></a></div>
    <div><?= Functions::getImage('user_edit.png') ?>&#160;<a href="profile.php?act=activity&amp;user=<?= $this->user['id'] ?>"><?= $this->lng['activity'] ?></a></div>
    <div><?= Functions::getImage('rating.png') ?>&#160;<a href="profile.php?act=stat&amp;user=<?= $this->user['id'] ?>"><?= Vars::$LNG['statistics'] ?></a></div>
    <?php if ($this->bancount) : ?>
    <div><?= Functions::getImage('user_block.png') ?>&#160;<a href="profile.php?act=ban&amp;user=<?= $this->user['id'] ?>"><?= Vars::$LNG['infringements'] ?></a> (<?= $this->bancount ?>)</div>
    <?php endif ?>
    <br/>
    <div><?= Functions::getImage('album_4.png') ?>&#160;<a href="album.php?act=list&amp;user=<?= $this->user['id'] ?>"><?= Vars::$LNG['photo_album'] ?></a>&#160;(<?= $this->total_photo ?>)</div>
    <div><?= Functions::getImage('comments.png') ?>&#160;<a href="profile.php?act=guestbook&amp;user=<?= $this->user['id'] ?>"><?= Vars::$LNG['guestbook'] ?></a>&#160;(<?= $this->user['comm_count'] ?>)</div>
    </p>
</div>
<div class="phdr">
    <a href="<?= Vars::$HOME_URL ?>/users"><?= Vars::$LNG['users'] ?></a>
</div>