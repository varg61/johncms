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
<div class="rmenu"><?= lng('list_empty') ?></div>
<?php endif ?>