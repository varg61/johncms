<div class="phdr"><strong><?php echo $this->tit ?></strong></div>
<?php if( $this->total ): ?>
<form action="<?php echo Vars::$MODULE_URI ?>?act=<?php echo $this->pages_type ?>" method="post"><div>
<?php foreach($this->query as $row): ?>
	<div class="<?php echo $row['list'] ?>">
	<input type="checkbox" name="delch[]" value="<?php echo $row['id'] ?>"/> <a href="<?php echo Vars::$MODULE_URI ?>?act=read&amp;id=<?php echo $row['id'] ?>"><?php echo $this->pref_in ?>: <?php echo $row['nickname'] ?></a> <?php echo $row['online'] ?> (<?php echo $row['time'] ?>)
	<?php if( $row['file']): ?>
     <div class="func">
		+ Вложение
     </div>
	<? endif ?>
	</div>
<?php endforeach ?>

<div class="gmenu">
	<?php echo lng( 'noted_mess' ) ?>:<br />
	<input type="hidden" name="token" value="<?= $this->token ?>"/>
	<input type="submit" name="delete_mess" value="<?php echo lng( 'delete' ) ?>"/><br />
	</div>
</div>
</form>

<div class="phdr"><?php echo lng( 'total' ) ?>: <?php echo $this->total ?></div>
<?php if($this->total > Vars::$USER_SET['page_size']): ?>
   <div class="topmenu"><?php echo $this->display_pagination ?></div>
   <form action="" method="post"><p><input type="text" name="page" size="2" value="<?php echo Vars::$PAGE ?>" style="font-size: x-small;"/>
   <input type="submit" value="<?php echo lng( 'to_page' ) ?> &gt;&gt;" style="font-size: x-small;"/></p></form>
<?php endif ?>

<?php else: ?>
<div class="rmenu"><?php echo $this->mess_err ?></div>
<?php endif ?>
<p>
<a href="<?php echo Vars::$MODULE_URI ?>"><?php echo lng( 'mail' ) ?></a><br />
<a href="<?php echo Vars::$HOME_URL ?>/contacts"><?php echo lng( 'contacts' ) ?></a>
</p>