<ul class="nav">
    <li><h1 class="section-personal"><?= $this->tit ?></h1></li>
</ul>
<?php if ($this->total): ?>
<form action="<?= $this->link ?>?act=<?= $this->pages_type ?>" method="post">
    <div>
        <?php foreach ($this->query as $row): ?>
        <div class="<?= $row['list'] ?>">
            <input type="checkbox" name="delch[]" value="<?= $row['id'] ?>"/> <a href="<?= $this->link ?>?act=read&amp;id=<?= $row['id'] ?>"><?= $this->pref_in ?>: <?= $row['nickname'] ?></a> <?= $row['online'] ?> (<?= $row['time'] ?>
            )
            <?php if ($row['file']): ?>
            <div class="func">
                + Вложение
            </div>
            <? endif ?>
        </div>
        <?php endforeach ?>
        <div class="gmenu">
            <?= __('noted_mess') ?>:<br/>
            <input type="hidden" name="token" value="<?= $this->token ?>"/>
            <input type="submit" name="delete_mess" value="<?= __('delete') ?>"/><br/>
        </div>
    </div>
</form>
<div class="phdr"><?= __('total') ?>: <?= $this->total ?></div>
<?php if ($this->total > Vars::$USER_SET['page_size']): ?>
    <div class="topmenu"><?= $this->display_pagination ?></div>
    <form action="" method="post"><p><input type="text" name="page" size="2" value="<?= Vars::$PAGE ?>" style="font-size: x-small;"/>
        <input type="submit" value="<?= __('to_page') ?> &gt;&gt;" style="font-size: x-small;"/></p></form>
    <?php endif ?>
<p>
<div class="func">
    <?php if ($this->pages_type == 'inmess'): ?>
    <a href="<?= $this->link ?>?act=inmess&amp;mod=delete_read"><?= __('delete_read') ?></a><br/>
    <a href="<?= $this->link ?>?act=inmess&amp;mod=cleaning"><?= __('cleaning') ?></a><br/>
    <?php else: ?>
    <a href="<?= $this->link ?>?act=outmess&amp;mod=cleaning"><?= __('cleaning') ?></a><br/>
    <?php endif ?>
</div>
</p>
<?php else: ?>
<div class="form-container">
	<div class="form-block align-center"><?= __('list_empty') ?></div>
</div>
<?php endif ?>
<div class="btn-panel">
    <a class="btn" href="<?= $this->link ?>?act=add"><i class="icn-edit"></i><?= __('write_message') ?></a>
</div>