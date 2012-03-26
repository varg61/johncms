<div class="phdr"><strong><?php echo lng( 'files' ) ?></strong></div>
<?php if($this->total > Vars::$USER_SET['page_size']): ?>
<div class="topmenu">
 <?php echo $this->display_pagination ?>
</div>
<? endif; ?>
<?php if($this->total): ?>
	<?php foreach( $this->query as $row): ?>
	<div class="<?php echo $row['list'] ?>">
	 <?php echo $row['icon'] ?>&#160;<a href="<?php echo Vars::$MODULE_URI ?>?act=load&amp;id=<?php echo $row['id'] ?>"><?php echo $row['filename'] ?></a> (<?php echo $row['filesize'] ?>)(<?php echo $row['filecount'] ?>)
	</div>
	<? endforeach ?>
	<div class="phdr"><?php echo lng( 'total' ) ?>: <?php echo $this->total ?></div>
	<?php if($this->total > Vars::$USER_SET['page_size']): ?>
	<div class="topmenu">
	 <?php echo $this->display_pagination ?>
	</div>
	<form action="" method="post"><p><input type="text" name="page" size="2" value="<?php echo Vars::$PAGE ?>"/>
	<input type="submit" value="<?php echo lng( 'to_page' ) ?> &gt;&gt;"/></p></form>
	<? endif; ?>
<?php else: ?>
  <div class="rmenu"><?php echo lng( 'no_files' ) ?>!</div>
<? endif; ?>
<p><a href="<?php echo Vars::$MODULE_URI ?>"><?php echo lng( 'contacts' ) ?></a></p>