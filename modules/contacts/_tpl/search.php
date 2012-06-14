<div class="phdr"><strong><?= lng('search') ?></strong></div>
<div class="gmenu">
    <form action="<?= Vars::$MODULE_URI ?>?act=search" method="post">
        <div><?= lng('search_contact') ?>:<br/>
            <input type="hidden" name="token" value="<?= $this->token ?>"/>
            <input type="text" name="q" value="<?= $this->search ?>"/>&#160;<input type="submit" name="search" value="<?= lng('search') ?>"/>
        </div>
    </form>
</div>
<?php if ($this->total): ?>
<?php foreach ($this->query as $row): ?>
    <div class="<?= $row['list'] ?>"><?= $row['icon'] ?>
        <a href="<?= $row['url'] ?>"><?= $row['nickname'] ?></a> <?= $row['online'] ?> (<?= $row['count'] ?>) <span class="red"><?= $row['count_new'] ?></span>
    </div>
    <? endforeach ?>
<? endif ?>
<?php if ($this->total > Vars::$USER_SET['page_size']): ?>
<div class="phdr"><?= lng('total') ?>: <?= $this->total ?></div>
<div class="topmenu"><?= $this->display_pagination ?></div>
<form action="" method="post"><p><input type="text" name="page" size="2" value="<?= Vars::$PAGE ?>"/>
    <input type="submit" value="<?= lng('to_page') ?> &gt;&gt;"/></p></form>
<? endif ?>
<p>
    <a href="<?= Vars::$HOME_URL ?>/mail"><?= lng('mail') ?></a><br/>
    <a href="<?= Vars::$MODULE_URI ?>"><?= lng('contacts') ?></a>
</p>