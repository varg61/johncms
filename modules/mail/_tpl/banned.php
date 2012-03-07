<div class="phdr">
    <h3><?= lng('banned_contacts') ?></h3>
</div>
<?php if ($this->total): ?>
<form action="<?= Vars::$MODULE_URI ?>?act=banned" method="post">
<div>
<?php endif ?>
<?= $this->contacts ?>
<?php if ($this->total): ?>
    <div class="gmenu">
        <?= lng('noted_contacts') ?>:<br/>
        <input type="submit" name="unban" value="<?= lng('unban') ?>"/> <input type="submit" name="delete" value="<?= lng('delete') ?>"/><br/>
    </div>
	</div>
</form>
	<div class="phdr">
        <?= lng('total') ?>: <?= $this->total ?>
    </div>
<? if ($this->total > Vars::$USER_SET['page_size']): ?>
    <div class="topmenu">
        <?= $this->display_pagination ?>
    </div>
    <form action="" method="post"><p><input type="text" name="page" size="2" value="<?=Vars::$PAGE?>"/>
        <input type="submit" value="<?= lng('to_page') ?> &gt;&gt;"/></p></form>
    <? endif ?>
<? endif ?>
<p>&laquo; <a href="<?= Vars::$MODULE_URI ?>"><?= lng('contacts') ?></a></p>