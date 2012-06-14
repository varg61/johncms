<div class="phdr"><strong><?= lng('system') ?></strong></div>
<?php if ($this->total > Vars::$USER_SET['page_size']): ?>
<div class="topmenu">
    <?= $this->display_pagination ?>
</div>
<? endif ?>
<?php if ($this->total): ?>
<?php foreach ($this->query as $row): ?>
    <div class="<?= $row['list'] ?>">
        <strong><?= $row['theme'] ?></strong> (<?= $row['time'] ?>)
        <div class="">
            <?= $row['text'] ?>
        </div>
        <div class="sub">
            [<span class="red">х</span>&#160;<a href="<?= Vars::$HOME_URL ?>/mail?act=systems&amp;mod=delete&amp;id=<?= $row['id'] ?>"><?= lng('delete') ?></a>]
        </div>
    </div>
    <? endforeach ?>
<div class="phdr"><?= lng('total') ?>: <?= $this->total ?></div>
<? endif ?>
<?= $this->error ?>
<?php if ($this->total > Vars::$USER_SET['page_size']): ?>
<div class="topmenu">
    <?= $this->display_pagination ?>
    <form action="" method="post"><p><input type="text" name="page" size="2" value="<?= Vars::$PAGE ?>"/>
        <input type="submit" value="<?= lng('to_page') ?> &gt;&gt;"/></p></form>
</div>
<? endif ?>
<?php if ($this->total): ?>
<p><a href="<?= Vars::$MODULE_URI ?>?act=systems&amp;mod=clear"><?= lng('cleaning') ?></a></p>
<? endif ?>
<p>
    <a href="<?= Vars::$MODULE_URI ?>"><?= lng('mail') ?></a><br/>
    <a href="<?= Vars::$HOME_URL ?>/contacts"><?= lng('contacts') ?></a>
</p>