<div class="phdr">
    <strong><?= __('friendship')?></strong>
</div>
<div class="topmenu">
    <strong><?= __('friends_list') ?></strong> |
    <a href="<?= Router::getUri(2) ?>?act=demands"><?= __('my_demand') ?></a><?= ($this->demands ? '(<span class="red">' . $this->demands . '</span>)' : '') ?> |
    <a href="<?= Router::getUri(2) ?>?act=offers"><?= __('my_offers') ?></a>
    <?= ($this->offers ? '(<span class="red">' . $this->offers . '</span>)' : '') ?><?= ($this->total ? ' | <a href="' . Router::getUri(2) . '?act=online">' . __('online') . '</a>' : '')?>
</div>
<?php if ($this->total): ?>
<?php foreach ($this->query as $row): ?>
    <div class="<?= $row['list'] ?>">
<!--   //TODO: Переделать ссылку     -->
        <?= $row['icon'] ?> <a href="<?= Vars::$HOME_URL ?>/profile?user=<?= $row['id'] ?>"><?= $row['nickname'] ?></a><?= $row['online'] ?>
        <div class="sub">
            <a href="<?= Router::getUri(2) ?>?act=delete&amp;id=<?= $row['id']?>"><?= __('delete') ?></a>
        </div>
    </div>
    <?php endforeach ?>
<?php else: ?>
<div class="menu"><p><?= __('list_empty') ?></p></div>
<?php endif ?>
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