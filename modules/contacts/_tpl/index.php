<p><a href="<?php echo Vars::$HOME_URL ?>/mail?act=add"><?php echo lng( 'write_message' ) ?></a></p>
<div class="phdr"><strong><?php echo lng( 'contacts' ) ?></strong></div>
<div class="topmenu"><a href="<?php echo Vars::$HOME_URL ?>/mail"><?php echo lng( 'mail' ) ?></a></div>
<?php if($this->total): ?>
<div><form action="<?php echo Vars::$MODULE_URI ?>/" method="post">
  <?php foreach($this->query as $row): ?>
   <div class="<?php echo $row['list'] ?>">
    <input type="checkbox" name="delch[]" value="<?php echo $row['id'] ?>"/> <?php echo $row['icon'] ?> <a href="<?php echo $row['url'] ?>"><?php echo $row['nickname'] ?></a> <?php echo $row['online'] ?> (<?php echo $row['count'] ?>) <span class="red"><?php echo $row['count_new'] ?></span>
   </div>
  <?php endforeach ?>
  <div class="gmenu"><?php echo lng( 'noted_contacts' ) ?>:<br />
  <input type="hidden" name="token" value="<?= $this->token ?>"/>
   <input type="submit" name="archive" value="<?php echo lng( 'in_archive' ) ?>"/>&#160;<input type="submit" name="delete" value="<?php echo lng( 'delete' ) ?>"/><br />
  </div>
	
 </form>
 <?php if($this->total > Vars::$USER_SET['page_size']): ?>
   <div class="phdr"><?php echo lng( 'total' ) ?>: <?php echo $this->total ?></div>
   <div class="topmenu"><?php echo $this->display_pagination ?></div>
   <form action="" method="post"><p><input type="text" name="page" size="2" value="<?php echo Vars::$PAGE ?>" style="font-size: x-small;"/>
   <input type="submit" value="<?php echo lng( 'to_page' ) ?> &gt;&gt;" style="font-size: x-small;"/></p></form>
  <?php endif ?>
</div>
<?php else: ?>
<div class="rmenu"><?php echo lng('no_contacts') ?></div>
<?php endif ?>
<div class="list2">
<?php echo Functions::getImage('mail-blocked.png') ?> <a href="<?php echo Vars::$MODULE_URI ?>?act=banned"><?php echo lng( 'banned' ) ?></a>&#160;(<?php echo $this->banned ?>)<br />
<?php echo Functions::getImage('mail-archive.png') ?> <a href="<?php echo Vars::$MODULE_URI ?>?act=archive"><?php echo lng( 'archive' ) ?></a>&#160;(<?php echo $this->archive ?>)<br />
<?php echo Functions::getImage('mail-search.png') ?> <a href="<?php echo Vars::$MODULE_URI ?>?act=search"><?php echo lng( 'search_contact' ) ?></a>
</div>