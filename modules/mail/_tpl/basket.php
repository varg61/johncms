<div class="phdr"><h3><?=$this->lng['basket']?></h3></div>
<?if($this->total):?><form action="<?= Vars::$MODULE_URI ?>?act=basket" method="post"><div><? endif ?>

<?=$this->contacts?>

<?if($this->total):?>
	<div class="gmenu">
	<?=$this->lng['noted_contacts']?>:<br />
	<input type="submit" name="restore" value="<?=$this->lng['restore']?>"/> <input type="submit" name="delete" value="<?=Vars::$LNG['delete']?>"/> <input type="submit" name="clear" value="<?=$this->lng['clean_basket']?>"/><br />
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