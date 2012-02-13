<div class="phdr">
    <b><?= ($this->user['id'] != Vars::$USER_ID ? $this->lng['user_profile'] : $this->lng['my_profile']) ?></b>
</div>
<?php if (isset($this->menu)) : ?>
<div class="topmenu">
    <?= Functions::displayMenu($this->menu) ?>
</div>
<?php endif ?>
<div class="user">
    <p><?= Functions::displayUser($this->user, $this->userarg) ?></p>
</div>
<div class="list2">
    <div><?= Functions::getImage('contacts.png') ?>&#160;<a href="profile.php?act=info&amp;user=<?= $this->user['id'] ?>"><?= Vars::$LNG['information'] ?></a></div>
    <div><?= Functions::getImage('user_edit.png') ?>&#160;<a href="profile.php?act=activity&amp;user=<?= $this->user['id'] ?>"><?= $this->lng['activity'] ?></a></div>
    <div><?= Functions::getImage('rating.png') ?>&#160;<a href="profile.php?act=stat&amp;user=<?= $this->user['id'] ?>"><?= Vars::$LNG['statistics'] ?></a></div>
</div>