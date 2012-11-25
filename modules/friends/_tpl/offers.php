<div class="phdr">
    <strong>
        <?= __('my_offers')?>
    </strong>
</div>
<div class="topmenu">
    <a href="<?= Vars::$MODULE_URI ?>?act=demands"><?= __('my_demand') ?></a> <?= ($this->demands ? '(<span class="red">' . $this->demands . '</span>)' : '') ?>
</div>
<?php if ($this->total): ?>
<?php foreach ($this->query as $row): ?>
    <div class="<?= $row['list'] ?>">
        <?= $row['icon'] ?> <a href="<?= Vars::$HOME_URL ?>/profile?user=<?= $row['id'] ?>"><?= $row['nickname'] ?></a>
        <div class="sub">
            <a href="<?= Vars::$MODULE_URI ?>?act=ok&amp;id=<?= $row['id']?>"><?= __('confirm') ?></a> | <a href="<?= Vars::$MODULE_URI ?>?act=no&amp;id=<?= $row['id']?>"><?= __('decline') ?></a>
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