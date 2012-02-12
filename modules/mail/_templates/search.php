<div class="phdr"><h3><?=Vars::$LNG['search']?></h3></div>
<div class="gmenu">
    <form action="<?= Vars::$MODULE_URI ?>?act=search" method="post">
      <div><?=$this->lng['search_contact']?>:<br />
        <input type="text" name="q" value="<?=$this->search?>"/>&#160;<input type="submit" name="search" value="<?= Vars::$LNG['search'] ?>"/>
      </div>
    </form>
</div>
<? if($this->total): ?>
	<? foreach($this->query as $row): ?>
	<div class="<?=$row['list']?>"><?= $row['icon'] ?>
		<a href="<?= $row['url'] ?>"><?= $row['nickname'] ?></a> <?= $row['online'] ?> (<?= $row['count_in'] ?>&#160;/&#160;<?= $row['count_out'] ?>) <span class="red"><?= $row['count_new'] ?></span>
	</div>
	<? endforeach ?>
<? endif ?>
<? if($this->total > Vars::$USER_SET['page_size']): ?>
<div class="phdr"><?=Vars::$LNG['total']?>: <?= $this->total ?></div>
<div class="topmenu"><?= $this->display_pagination ?></div>
<form action="" method="post"><p><input type="text" name="page" size="2" value="<?=Vars::$PAGE?>"/>
<input type="submit" value="<?=Vars::$LNG['to_page']?> &gt;&gt;"/></p></form>
<? endif ?>
<p>&laquo; <a href="<?= Vars::$MODULE_URI ?>"><?=Vars::$LNG['contacts']?></a></p>