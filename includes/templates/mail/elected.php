<div class="phdr"><h3><?=$this->lng['elected']?></h3></div>
<form action="<?= Vars::$MODULE_URI ?>?act=elected" method="post"><div>

<?=$this->contacts?>

<?if($this->total):?>
	<div class="gmenu">
	<?=$this->lng['noted_contacts']?>:<br />
	<input type="submit" name="delete" value="<?=Vars::$LNG['delete']?>"/><br />
	</div>
	</div></form>

	<div class="phdr"><?=Vars::$LNG['total']?>: <?= $this->total ?></div>
	<? if($this->total > Vars::$USER_SET['page_size']): ?>
	<div class="topmenu"><?= $this->display_pagination ?></div>
	<form action="" method="post"><p><input type="text" name="page" size="2" value="<?=Vars::$PAGE?>"/>
	<input type="submit" value="<?=Vars::$LNG['to_page']?> &gt;&gt;"/></p></form>
	<? endif ?>
<? endif ?>
<p>&laquo; <a href="<?= Vars::$MODULE_URI ?>"><?=Vars::$LNG['contacts']?></a></p>