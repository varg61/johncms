<div class="phdr">
    <h3><?= lng('system') ?></h3>
</div>
<?php if ($this->total > Vars::$USER_SET['page_size']): ?>
<div class="topmenu">
    <?= $this->display_pagination ?>
</div>
<?php endif ?>
<?php if ($this->total): ?>
<?php foreach ($this->query as $row): ?>
    <div class="<?= $row['list'] ?>">
        <strong><?= $row['theme'] ?></strong> (<?= $row['time'] ?>)
        <div class="">
            <?= $row['text'] ?>
        </div>
        <div class="sub">
            [<span class="red">х</span>&#160;<a href="<?= Vars::$HOME_URL ?>/mail?act=systems&amp;mod=delete&amp;id=<?=$row['id']?>"><?= lng('delete') ?></a>]
        </div>
    </div>
    <?php endforeach ?>
<div class="phdr">
    <?= lng('total') ?>: <?= $this->total ?>
</div>
<?php endif ?>
<?= $this->error ?>
<?php if ($this->total > Vars::$USER_SET['page_size']): ?>
<div class="topmenu">
    <?= $this->display_pagination ?>
    <form action="" method="post"><p><input type="text" name="page" size="2" value="<?=Vars::$PAGE?>"/>
        <input type="submit" value="<?= lng('to_page') ?> &gt;&gt;"/></p>
    </form>
</div>
<?php endif ?>
<?php if ($this->total): ?>
<p><a href="<?= Vars::$MODULE_URI ?>?act=systems&amp;mod=clear"><?= lng('cleaning') ?></a></p>
<?php endif ?>
<p>&laquo; <a href="<?= Vars::$MODULE_URI ?>"><?= lng('contacts') ?></a></p>