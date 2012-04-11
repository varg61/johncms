<div class="phdr"><strong><?php echo lng( 'archive' ) ?></strong></div>
<?if($this->total): ?><form action="<?php echo Vars::$MODULE_URI ?>?act=archive" method="post"><div><? endif ?>

<?php echo $this->contacts ?>

<?if($this->total): ?>
	<div class="gmenu">
	<?php echo lng( 'noted_contacts' ) ?>:<br />
	<input type="hidden" name="token" value="<?= $this->token ?>"/>
	<input type="submit" name="delete" value="<?php echo lng( 'delete' ) ?>"/><br />
	</div>
	</div></form>
	<div class="phdr"><?php echo lng( 'total' ) ?>: <?php echo $this->total ?></div>
	<?php if($this->total > Vars::$USER_SET['page_size']): ?>
	<div class="topmenu"><?php echo $this->display_pagination ?></div>
	<form action="" method="post"><p><input type="text" name="page" size="2" value="<?php echo Vars::$PAGE ?>"/>
	<input type="submit" value="<?php echo lng( 'to_page' ) ?> &gt;&gt;"/></p></form>
	<? endif ?>
<? endif ?>
<p>
<a href="<?php echo Vars::$MODULE_URI ?>"><?php echo lng( 'mail' ) ?></a><br />
<a href="<?php echo Vars::$MODULE_URI ?>?act=contacts"><?php echo lng( 'contacts' ) ?></a>
</p>