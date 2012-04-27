<?php echo $this->titleTest ?>
<?php if($this->total > Vars::$USER_SET['page_size']): ?>
<div class="topmenu">
 <?php echo $this->display_pagination ?>
</div>
<? endif ?>

<form action="<?php echo $this->url_type ?>" method="post"><div>

<?php foreach($this->query as $row): ?>
	<div class="<?php echo $row['list'] ?>">
	 <?php echo $row['icon'] ?> <a href="<?php echo Vars::$HOME_URL ?>/profile?user=<?php echo $row['id'] ?>"><?php echo $row['nickname'] ?></a> <?php echo $row['online'] ?> (<?php echo $row['time'] ?>)
	 <div class="">
	  <?php echo $row['text'] ?>
		<?php if($row['file']): ?>
     <div class="func">
		 <?php echo lng( 'file' ) ?>: <?php echo $row['file'] ?>
     </div>
		<? endif ?>
	 </div>
	 <div class="sub">
	 <input type="checkbox" name="delch[]" value="<?php echo $row['mid'] ?>"/> 
	 <?php if ( isset($row['read']) && $row['read'] == 0 && $row['user_id'] == Vars::$USER_ID ):?>
		[<a href="<?php echo Vars::$HOME_URL ?>/mail?act=messages&amp;mod=edit&amp;id=<?php echo $row['mid'] ?>"><?php echo lng( 'edit' ) ?></a>] 
	 <? endif ?>
	 <?php if($row['selectBar']): ?>
	  <?php echo $row['selectBar'] ?>
	 <? endif ?>
	 <?php if(!$row['selectBar']): ?>
		[<span class="red">х</span>&#160;<a href="<?php echo $row['urlDelete'] ?>"><?php echo lng( 'delete' ) ?></a>] 
		<?php if($row['elected']): ?>
		[<a href="<?php echo Vars::$HOME_URL ?>/mail?act=messages&amp;mod=elected&amp;id=<?php echo $row['mid'] ?>"><?php echo lng( 'in_elected' ) ?></a>]
		<? endif ?>
	 <? endif ?>
	 </div>
	</div>
<? endforeach ?>

<div class="gmenu">
	<?php echo lng( 'noted_mess' ) ?>:<br />
	<input type="hidden" name="token" value="<?= $this->token ?>"/>
	<input type="submit" name="delete_mess" value="<?php echo lng( 'delete' ) ?>"/><br />
	</div>
</div>
</form>

<div class="phdr"><?php echo lng( 'total' ) ?>: <?php echo $this->total ?></div>
<?php if($this->total > Vars::$USER_SET['page_size']): ?>
<div class="topmenu">
 <?php echo $this->display_pagination ?>
</div>
<form action="" method="post"><p><input type="text" name="page" size="2" value="<?php echo Vars::$PAGE ?>"/>
	<input type="submit" value="<?php echo lng( 'to_page' ) ?> &gt;&gt;"/></p></form>
<? endif ?>
<?php echo $this->urlTest ?>