<?=$this->titleTest?>
<? if($this->total > Vars::$USER_SET['page_size']): ?>
<div class="topmenu">
  <?= $this->display_pagination ?>
</div>
<? endif ?>
<? foreach($this->query as $row): ?>
	<div class="<?= $row['list'] ?>">
	  <?= $row['icon'] ?> <a href="<?= Vars::$HOME_URL ?>/users/profile.php?user=<?= $row['id'] ?>"><?= $row['nickname'] ?></a> <?= $row['online'] ?> (<?= $row['time'] ?>)
	  <div class="">
	    <?= $row['text'] ?>
		<? if($row['file']): ?>
          <div class="func">
		  <?=$this->lng['file']?>: <?= $row['file'] ?>
          </div>
		<? endif ?>
	  </div>
	  
	  <div class="sub">
	  <? if($row['selectBar']): ?>
	    <?= $row['selectBar'] ?>
	  <? endif ?>
	  <? if(!$row['selectBar']): ?>
		[<span class="red">х</span>&#160;<a href="<?= $row['urlDelete'] ?>"><?=$this->lng['delete']?></a>]
		<? if($row['elected']): ?>
		 [<a href="<?= Vars::$HOME_URL ?>/mail?act=messages&amp;mod=elected&amp;id=<?=$row['mid']?>"><?=$this->lng['in_elected']?></a>]
		<? endif ?>
	  <? endif ?>
	  </div>
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
<?=$this->urlTest?>