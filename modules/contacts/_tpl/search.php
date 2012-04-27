<div class="phdr"><strong><?php echo lng( 'search' ) ?></strong></div>
<div class="gmenu">
  <form action="<?php echo Vars::$MODULE_URI ?>?act=search" method="post">
   <div><?php echo lng( 'search_contact' ) ?>:<br />
    <input type="hidden" name="token" value="<?= $this->token ?>"/>
	<input type="text" name="q" value="<?php echo $this->search ?>"/>&#160;<input type="submit" name="search" value="<?php echo lng( 'search' ) ?>"/>
   </div>
  </form>
</div>
<?php if($this->total): ?>
	<?php foreach($this->query as $row): ?>
	<div class="<?php echo $row['list'] ?>"><?php echo $row['icon'] ?>
		<a href="<?php echo $row['url'] ?>"><?php echo $row['nickname'] ?></a> <?php echo $row['online'] ?> (<?php echo $row['count'] ?>) <span class="red"><?php echo $row['count_new'] ?></span>
	</div>
	<? endforeach ?>
<? endif ?>
<?php if($this->total > Vars::$USER_SET['page_size']): ?>
<div class="phdr"><?php echo lng( 'total' ) ?>: <?php echo $this->total ?></div>
<div class="topmenu"><?php echo $this->display_pagination ?></div>
<form action="" method="post"><p><input type="text" name="page" size="2" value="<?php echo Vars::$PAGE ?>"/>
<input type="submit" value="<?php echo lng( 'to_page' ) ?> &gt;&gt;"/></p></form>
<? endif ?>
<p>
<a href="<?php echo Vars::$HOME_URL ?>/mail"><?php echo lng( 'mail' ) ?></a><br />
<a href="<?php echo Vars::$MODULE_URI ?>"><?php echo lng( 'contacts' ) ?></a>
</p>