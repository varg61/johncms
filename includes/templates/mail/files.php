<div class="phdr"><h3><?=Vars::$LNG['files']?></h3></div>
<? if($this->total > Vars::$USER_SET['page_size']): ?>
<div class="topmenu">
  <?= $this->display_pagination ?>
</div>
<? endif ?>
<? if($this->total): ?>
	<? foreach( $this->query as $row): ?>
	<div class="<?= $row['list'] ?>">
	  <?= $row['icon'] ?>&#160;<a href="<?= Vars::$MODULE_URI ?>?act=load&amp;id=<?= $row['id'] ?>"><?= $row['filename'] ?></a> (<?= $row['filesize'] ?>)(<?= $row['filecount'] ?>)
	</div>
	<? endforeach ?>
	<div class="phdr"><?=Vars::$LNG['total']?>: <?= $this->total ?></div>
	<? if($this->total > Vars::$USER_SET['page_size']): ?>
	<div class="topmenu">
	  <?= $this->display_pagination ?>
	</div>
	<form action="" method="post"><p><input type="text" name="page" size="2" value="<?=Vars::$PAGE?>"/>
	<input type="submit" value="<?=Vars::$LNG['to_page']?> &gt;&gt;"/></p></form>
	<? endif ?>
<? else: ?>
    <div class="rmenu"><?=$this->lng['no_files']?>!</div>
<? endif ?>
<p>&laquo; <a href="<?= Vars::$MODULE_URI ?>"><?=Vars::$LNG['contacts']?></a></p>