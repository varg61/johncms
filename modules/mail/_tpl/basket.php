<ul class="nav">
    <li><h1 class="section-personal"><?= __('basket') ?></h1></li>
</ul>
<? if ($this->total): ?><form action="<?= Vars::$MODULE_URI ?>?act=basket" method="post"><div><? endif ?>
<? if ($this->total): ?>
	<?= $this->contacts ?>
    <div class="gmenu">
        <?= __('noted_contacts') ?>:<br/>
        <input type="hidden" name="token" value="<?= $this->token ?>"/>
        <input type="submit" name="restore" value="<?= __('restore') ?>"/> <input type="submit" name="delete" value="<?= __('delete') ?>"/> <input type="submit" name="clear" value="<?= __('clean_basket') ?>"/><br/>
    </div>
	</div></form>
	<div class="phdr"><?= __('total') ?>: <?= $this->total ?></div>
<?php if ($this->total > Vars::$USER_SET['page_size']): ?>
    <div class="topmenu"><?= $this->display_pagination ?></div>
    <form action="" method="post"><p><input type="text" name="page" size="2" value="<?= Vars::$PAGE ?>"/>
        <input type="submit" value="<?= __('to_page') ?> &gt;&gt;"/></p></form>
    <? endif ?>
<?php else: ?>
<div class="form-container">
	<div class="form-block align-center"><?= __('list_empty') ?></div>
</div>
<?php endif ?>