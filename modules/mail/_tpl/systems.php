<div class="phdr"><strong><?php echo lng( 'system' ) ?></strong></div>
<?php if($this->total > Vars::$USER_SET['page_size']): ?>
<div class="topmenu">
 <?php echo $this->display_pagination ?>
</div>
<? endif; ?>
<?php if($this->total): ?>
<?php foreach($this->query as $row): ?>
	<div class="<?php echo $row['list'] ?>">
	 <strong><?php echo $row['theme'] ?></strong> (<?php echo $row['time'] ?>)
	 <div class="">
	  <?php echo $row['text'] ?>
	 </div>
	 <div class="sub">
		[<span class="red">х</span>&#160;<a href="<?php echo Vars::$HOME_URL ?>/mail?act=systems&amp;mod=delete&amp;id=<?php echo $row['id'] ?>"><?php echo lng( 'delete' ) ?></a>]
	 </div>
	</div>
<? endforeach ?>
<div class="phdr"><?php echo lng( 'total' ) ?>: <?php echo $this->total ?></div>
<? endif; ?>
<?php echo $this->error ?>
<?php if($this->total > Vars::$USER_SET['page_size']): ?>
<div class="topmenu">
 <?php echo $this->display_pagination ?>
 <form action="" method="post"><p><input type="text" name="page" size="2" value="<?php echo Vars::$PAGE ?>"/>
<input type="submit" value="<?php echo lng( 'to_page' ) ?> &gt;&gt;"/></p></form>
</div>
<? endif; ?>
<?php if($this->total): ?>
<p><a href="<?php echo Vars::$MODULE_URI ?>?act=systems&amp;mod=clear"><?php echo lng( 'cleaning' ) ?></a></p>
<? endif; ?>
<p><a href="<?php echo Vars::$MODULE_URI ?>"><?php echo lng( 'contacts' ) ?></a></p>