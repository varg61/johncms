<div class="phdr">
    <strong><?= lng('friends_list') ?> <a href="<?= Vars::$HOME_URL ?>/profile?user=<?= Vars::$USER ?>"><?php echo $this->user['nickname'] ?></a></strong>
</div>
<?php if ($this->total): ?>
<?php foreach ($this->query as $row): ?>
    <div class="<?= $row['list'] ?>">
        <?= $row['icon'] ?> <a href="<?= Vars::$HOME_URL ?>/profile?user=<?= $row['id'] ?>"><?= $row['nickname'] ?></a><?= $row['online'] ?>
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