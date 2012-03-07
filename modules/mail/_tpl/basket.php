<div class="phdr">
    <h3><?= lng('basket') ?></h3>
</div>
<?php if ($this->total): ?>
<form action="<?= Vars::$MODULE_URI ?>?act=basket" method="post">
<div>
<? endif ?>
<?= $this->contacts ?>
<?php if ($this->total) : ?>
    <div class="gmenu">
        <?= lng('noted_contacts') ?>:<br/>
        <input type="submit" name="restore" value="<?= lng('restore') ?>"/> <input type="submit" name="delete" value="<?= lng('delete') ?>"/> <input type="submit" name="clear" value="<?= lng('clean_basket') ?>"/><br/>
    </div>
	</div>
</form>
	<div class="phdr">
        <?= lng('total') ?>: <?= $this->total ?>
    </div>
<?php if ($this->total > Vars::$USER_SET['page_size']): ?>
    <div class="topmenu">
        <?= $this->display_pagination ?>
    </div>
    <form action="" method="post"><p><input type="text" name="page" size="2" value="<?=Vars::$PAGE?>"/>
        <input type="submit" value="<?= lng('to_page') ?> &gt;&gt;"/></p>
    </form>
    <? endif ?>
<? endif ?>
<p>&laquo; <a href="<?= Vars::$MODULE_URI ?>"><?= lng('contacts') ?></a></p>