<ul class="nav">
    <li><h1 class="section-personal"><?= __('elected') ?></h1></li>
</ul>
<? if ($this->total): ?><form action="<?= Router::getUri(2) ?>?act=elected" method="post"><div><? endif ?>
<? if ($this->total): ?>
<?= $this->contacts ?>
    <div class="gmenu">
        <?= __('noted_contacts') ?>:<br/>
        <input type="hidden" name="token" value="<?= $this->token ?>"/>
        <input type="submit" name="delete" value="<?= __('delete') ?>"/><br/>
    </div>
	</div></form>
	<div class="phdr"><?= __('total') ?>: <?= $this->total ?></div>
<?php if ($this->total > Vars::$USER_SET['page_size']): ?>
    <div class="topmenu"><?= $this->display_pagination ?></div>
    <form action="" method="post"><p><input type="text" name="page" size="2" value="<?= Vars::$PAGE ?>"/>
        <input type="submit" value="<?= __('to_page') ?> &gt;&gt;"/></p></form>
    <? endif ?>
<? else: ?>
<div class="form-container">
	<div class="form-block align-center"><?= __('list_empty') ?></div>
</div>
<? endif ?>