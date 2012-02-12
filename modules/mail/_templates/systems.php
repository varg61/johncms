<div class="phdr"><h3><?=$this->lng['system']?></h3></div>
<? if($this->total > Vars::$USER_SET['page_size']): ?>
<div class="topmenu">
  <?= $this->display_pagination ?>
</div>
<? endif ?>
<? if($this->total):?>
<? foreach($this->query as $row): ?>
	<div class="<?= $row['list'] ?>">
	 <strong><?= $row['theme'] ?></strong> (<?= $row['time'] ?>)
	  <div class="">
	    <?= $row['text'] ?>
	  </div>
	  <div class="sub">
		[<span class="red">х</span>&#160;<a href="<?= Vars::$HOME_URL ?>/mail?act=systems&amp;mod=delete&amp;id=<?=$row['id']?>"><?=Vars::$LNG['delete']?></a>]
	  </div>
	</div>
<? endforeach ?>
<div class="phdr"><?=Vars::$LNG['total']?>: <?= $this->total ?></div>
<? endif ?>
<?=$this->error?>
<? if($this->total > Vars::$USER_SET['page_size']): ?>
<div class="topmenu">
  <?= $this->display_pagination ?>
  <form action="" method="post"><p><input type="text" name="page" size="2" value="<?=Vars::$PAGE?>"/>
<input type="submit" value="<?=Vars::$LNG['to_page']?> &gt;&gt;"/></p></form>
</div>
<? endif ?>
<? if($this->total):?>
<p><a href="<?= Vars::$MODULE_URI ?>?act=systems&amp;mod=clear"><?=$this->lng['cleaning']?></a></p>
<? endif ?>
<p>&laquo; <a href="<?= Vars::$MODULE_URI ?>"><?=Vars::$LNG['contacts']?></a></p>