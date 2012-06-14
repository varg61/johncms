<div class="phdr">
    <a href="<?= Vars::$URI ?>?user=<?= $this->user['id'] ?>"><b><?= ($this->user['id'] != Vars::$USER_ID ? lng('user_profile') : lng('my_profile')) ?></b></a> | <?= lng('information') ?>
</div>
<div class="user">
    <p><?= Functions::displayUser($this->user, array('iphide' => 1,)) ?></p>
</div>
<div class="phdr">
    <strong>
        <?= lng('friends')?> <a href="<?= Vars::$HOME_URL ?>/profile?user=<?= $this->id ?>"><?= $this->nickname ?></a>
    </strong>
</div>
<?php if ($this->total): ?>
<?php foreach ($this->query as $row): ?>
    <div class="<?= $row['list'] ?>">
        <?php if ($row['id'] != Vars::$USER_ID): ?>
        <?= $row['icon'] ?> <a href="<?= Vars::$HOME_URL ?>/profile?user=<?= $row['id'] ?>"><?= $row['nickname'] . $row['online'] ?></a>
        <?php else: ?>
        <?= $row['icon'] ?> <strong><?= $row['nickname'] ?></strong><?= $row['online'] ?>
        <?php endif ?>
    </div>
    <?php endforeach ?>
<?php else: ?>
<div class="menu"><p><?= lng('list_empty') ?></p></div>
<?php endif ?>
<div class="phdr">
    <?= lng('total') ?>: <?= $this->total ?>
</div>
<?php if ($this->total > Vars::$USER_SET['page_size']): ?>
<div class="topmenu">
    <?= $this->display_pagination ?>
</div>
<form action="" method="post">
    <p>
        <input type="text" name="page" size="2" value="<?= Vars::$PAGE ?>"/>
        <input type="submit" value="<?= lng('to_page') ?> &gt;&gt;"/>
    </p>
</form>
<?php endif ?>