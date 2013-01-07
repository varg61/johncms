<ul class="nav">
    <li><h1 class="section-personal"><?= __('files') ?></h1></li>
</ul>
<?php if ($this->total): ?>
<?php foreach ($this->query as $row): ?>
    <div class="<?= $row['list'] ?>">
        <?= $row['icon'] ?>&#160;<a href="<?= Router::getUrl(2) ?>?act=load&amp;id=<?= $row['id'] ?>"><?= $row['filename'] ?></a> (<?= $row['filesize'] ?>)(<?= $row['filecount'] ?>)
    </div>
    <? endforeach ?>
<div class="phdr"><?= __('total') ?>: <?= $this->total ?></div>
<?php if ($this->total > Vars::$USER_SET['page_size']): ?>
    <div class="topmenu">
        <?= $this->display_pagination ?>
    </div>
    <form action="" method="post"><p><input type="text" name="page" size="2" value="<?= Vars::$PAGE ?>"/>
        <input type="submit" value="<?= __('to_page') ?> &gt;&gt;"/></p></form>
    <? endif ?>
<?php else: ?>
<div class="form-container">
	<div class="form-block align-center"><?= __('list_empty') ?></div>
</div>
<? endif ?>