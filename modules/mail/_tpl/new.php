<div class="phdr"><strong><?php echo lng( 'new_messages' ) ?></strong></div>
<?if($this->total): ?>
<form action="<?php echo Vars::$MODULE_URI ?>?act=new" method="post"><div>

<?php echo $this->contacts ?>
	
	<div class="gmenu">
	<?php echo lng( 'noted_contacts' ) ?>:<br />
	<input type="hidden" name="token" value="<?= $this->token ?>"/>
	<input type="submit" name="addnew" value="<?php echo lng( 'select_read' ) ?>"/><br />
	</div>
	</div></form>
	
	<div class="phdr"><?php echo lng( 'total' ) ?>: <?php echo $this->total ?></div>
	<?php if($this->total > Vars::$USER_SET['page_size']): ?>
	<div class="topmenu"><?php echo $this->display_pagination ?></div>
	<form action="" method="post"><p><input type="text" name="page" size="2" value="<?php echo Vars::$PAGE ?>"/>
	<input type="submit" value="<?php echo lng( 'to_page' ) ?> &gt;&gt;"/></p></form>
	<? endif ?>
<?php else: ?>
<div class="rmenu"><?php echo lng( 'no_messages' ) ?>!</div>
<? endif ?>
<p><a href="<?php echo Vars::$MODULE_URI ?>"><?php echo lng( 'contacts' ) ?></a></p>