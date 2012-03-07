<div class="phdr">
    <h3><?= lng('elected') ?></h3>
</div>
<form action="<?= Vars::$MODULE_URI ?>?act=elected" method="post">
<div>
<?= $this->contacts ?>
<?php if ($this->total): ?>
    <div class="gmenu">
        <?= lng('noted_contacts') ?>:<br/>
        <input type="submit" name="delete" value="<?= lng('delete') ?>"/><br/>
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
    <form action="" method="post">
        <p><input type="text" name="page" size="2" value="<?=Vars::$PAGE?>"/>
        <input type="submit" value="<?= lng('to_page') ?> &gt;&gt;"/></p>
    </form>
    <?php endif ?>
<?php endif ?>
<p>&laquo; <a href="<?= Vars::$MODULE_URI ?>"><?= lng('contacts') ?></a></p>