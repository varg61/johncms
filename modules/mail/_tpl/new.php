<div class="phdr"><strong><?= __('new_messages') ?></strong></div>
<? if ($this->total): ?>
<form action="<?= Router::getUri(2) ?>?act=new" method="post">
    <div>
        <?= $this->contacts ?>
        <div class="gmenu">
            <?= __('noted_contacts') ?>:<br/>
            <input type="hidden" name="token" value="<?= $this->token ?>"/>
            <input type="submit" name="addnew" value="<?= __('select_read') ?>"/><br/>
        </div>
    </div>
</form>

<div class="phdr"><?= __('total') ?>: <?= $this->total ?></div>
<?php if ($this->total > Vars::$USER_SET['page_size']): ?>
    <div class="topmenu"><?= $this->display_pagination ?></div>
    <form action="" method="post"><p><input type="text" name="page" size="2" value="<?= Vars::$PAGE ?>"/>
        <input type="submit" value="<?= __('to_page') ?> &gt;&gt;"/></p></form>
    <? endif ?>
<?php else: ?>
<div class="rmenu"><?= __('no_messages') ?>!</div>
<? endif ?>
<p>
    <a href="<?= Router::getUri(2) ?>"><?= __('mail') ?></a><br/>
    <a href="<?= Vars::$HOME_URL ?>contacts/"><?= __('contacts') ?></a>
</p>