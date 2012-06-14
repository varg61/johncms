<div class="phdr">
    <strong>
        <?= lng('friends_online')?>
    </strong>
</div>
<?php if ($this->total): ?>
<?php foreach ($this->query as $row): ?>
    <div class="<?= $row['list'] ?>">
        <?= $row['icon'] ?> <a href="<?= Vars::$HOME_URL ?>/profile?user=<?= $row['id'] ?>"><?= $row['nickname'] ?></a>
        <div class="sub">
            <a href="<?= Vars::$MODULE_URI ?>?act=delete&amp;id=<?= $row['id']?>"><?= lng('delete') ?></a>
        </div>
    </div>
    <?php endforeach ?>
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
<?php else: ?>
<div class="rmenu"><?= lng('friends_not_online') ?></div>
<?php endif ?>
<p><a href="<?= Vars::$MODULE_URI ?>"><?= lng('friends') ?></a></p>