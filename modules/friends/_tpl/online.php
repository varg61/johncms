<div class="phdr">
    <strong>
        <?= __('friends_online')?>
    </strong>
</div>
<?php if ($this->total): ?>
<?php foreach ($this->query as $row): ?>
    <div class="<?= $row['list'] ?>">
<!--   //TODO: Переделать ссылку     -->
        <?= $row['icon'] ?> <a href="<?= Vars::$HOME_URL ?>/profile?user=<?= $row['id'] ?>"><?= $row['nickname'] ?></a>
        <div class="sub">
            <a href="<?= Router::getUrl(2) ?>?act=delete&amp;id=<?= $row['id']?>"><?= __('delete') ?></a>
        </div>
    </div>
    <?php endforeach ?>
<div class="phdr">
    <?= __('total') ?>: <?= $this->total ?>
</div>
<?php if ($this->total > Vars::$USER_SET['page_size']): ?>
    <div class="topmenu">
        <?= $this->display_pagination ?>
    </div>
    <form action="" method="post">
        <p>
            <input type="text" name="page" size="2" value="<?= Vars::$PAGE ?>"/>
            <input type="submit" value="<?= __('to_page') ?> &gt;&gt;"/>
        </p>
    </form>
    <?php endif ?>
<?php else: ?>
<div class="rmenu"><?= __('friends_not_online') ?></div>
<?php endif ?>
<p><a href="<?= Router::getUrl(2) ?>"><?= __('friends') ?></a></p>