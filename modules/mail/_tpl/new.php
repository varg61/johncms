<div class="phdr">
    <h3><?= lng('new_messages') ?></h3>
</div>
<form action="<?= Vars::$MODULE_URI ?>?act=new" method="post"><div>
<?= $this->contacts ?>
<? if ($this->total): ?>
    <div class="gmenu">
        <?= lng('noted_contacts') ?>:<br/>
        <input type="submit" name="addnew" value="<?= lng('select_read') ?>"/><br/>
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
        <input type="submit" value="<?= lng('to_page') ?> &gt;&gt;"/></p>
    </form>
    <? endif ?>
<? endif ?>
<p>&laquo; <a href="<?= Vars::$MODULE_URI ?>"><?= lng('contacts') ?></a></p>