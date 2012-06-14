<div class="phdr"><strong><?= lng('archive') ?></strong></div>
<? if ($this->total): ?><form action="<?= Vars::$MODULE_URI ?>?act=archive" method="post"><div><? endif ?>
<?= $this->contacts ?>
<? if ($this->total): ?>
    <div class="gmenu">
        <?= lng('noted_contacts') ?>:<br/>
        <input type="hidden" name="token" value="<?= $this->token ?>"/>
        <input type="submit" name="delete" value="<?= lng('delete') ?>"/><br/>
    </div>
	</div></form>
	<div class="phdr"><?= lng('total') ?>: <?= $this->total ?></div>
<?php if ($this->total > Vars::$USER_SET['page_size']): ?>
    <div class="topmenu"><?= $this->display_pagination ?></div>
    <form action="" method="post"><p><input type="text" name="page" size="2" value="<?= Vars::$PAGE ?>"/>
        <input type="submit" value="<?= lng('to_page') ?> &gt;&gt;"/></p></form>
    <? endif ?>
<? endif ?>
<p>
    <a href="<?= Vars::$HOME_URL ?>/mail"><?= lng('mail') ?></a><br/>
    <a href="<?= Vars::$MODULE_URI ?>"><?= lng('contacts') ?></a>
</p>