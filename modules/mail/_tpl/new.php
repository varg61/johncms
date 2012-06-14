<div class="phdr"><strong><?= lng('new_messages') ?></strong></div>
<? if ($this->total): ?>
<form action="<?= Vars::$MODULE_URI ?>?act=new" method="post">
    <div>
        <?= $this->contacts ?>
        <div class="gmenu">
            <?= lng('noted_contacts') ?>:<br/>
            <input type="hidden" name="token" value="<?= $this->token ?>"/>
            <input type="submit" name="addnew" value="<?= lng('select_read') ?>"/><br/>
        </div>
    </div>
</form>

<div class="phdr"><?= lng('total') ?>: <?= $this->total ?></div>
<?php if ($this->total > Vars::$USER_SET['page_size']): ?>
    <div class="topmenu"><?= $this->display_pagination ?></div>
    <form action="" method="post"><p><input type="text" name="page" size="2" value="<?= Vars::$PAGE ?>"/>
        <input type="submit" value="<?= lng('to_page') ?> &gt;&gt;"/></p></form>
    <? endif ?>
<?php else: ?>
<div class="rmenu"><?= lng('no_messages') ?>!</div>
<? endif ?>
<p>
    <a href="<?= Vars::$MODULE_URI ?>"><?= lng('mail') ?></a><br/>
    <a href="<?= Vars::$HOME_URL ?>/contacts"><?= lng('contacts') ?></a>
</p>