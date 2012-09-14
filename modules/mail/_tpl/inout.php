<div class="phdr"><strong><?= $this->tit ?></strong></div>
<?php if ($this->total): ?>
<form action="<?= Vars::$MODULE_URI ?>?act=<?= $this->pages_type ?>" method="post">
    <div>
        <?php foreach ($this->query as $row): ?>
        <div class="<?= $row['list'] ?>">
            <input type="checkbox" name="delch[]" value="<?= $row['id'] ?>"/> <a href="<?= Vars::$MODULE_URI ?>?act=read&amp;id=<?= $row['id'] ?>"><?= $this->pref_in ?>: <?= $row['nickname'] ?></a> <?= $row['online'] ?> (<?= $row['time'] ?>
            )
            <?php if ($row['file']): ?>
            <div class="func">
                + Вложение
            </div>
            <? endif ?>
        </div>
        <?php endforeach ?>
        <div class="gmenu">
            <?= lng('noted_mess') ?>:<br/>
            <input type="hidden" name="token" value="<?= $this->token ?>"/>
            <input type="submit" name="delete_mess" value="<?= lng('delete') ?>"/><br/>
        </div>
    </div>
</form>
<div class="phdr"><?= lng('total') ?>: <?= $this->total ?></div>
<?php if ($this->total > Vars::$USER_SET['page_size']): ?>
    <div class="topmenu"><?= $this->display_pagination ?></div>
    <form action="" method="post"><p><input type="text" name="page" size="2" value="<?= Vars::$PAGE ?>" style="font-size: x-small;"/>
        <input type="submit" value="<?= lng('to_page') ?> &gt;&gt;" style="font-size: x-small;"/></p></form>
    <?php endif ?>
<p>
<div class="func">
    <?php if ($this->pages_type == 'inmess'): ?>
    <a href="<?= Vars::$MODULE_URI ?>?act=inmess&amp;mod=delete_read"><?= lng('delete_read') ?></a><br/>
    <a href="<?= Vars::$MODULE_URI ?>?act=inmess&amp;mod=cleaning"><?= lng('cleaning') ?></a><br/>
    <?php else: ?>
    <a href="<?= Vars::$MODULE_URI ?>?act=outmess&amp;mod=cleaning"><?= lng('cleaning') ?></a><br/>
    <?php endif ?>
</div>
</p>
<?php else: ?>
<div class="rmenu"><?= $this->mess_err ?></div>
<?php endif ?>
<p>
    <a href="<?= Vars::$MODULE_URI ?>"><?= lng('mail') ?></a><br/>
    <a href="<?= Vars::$HOME_URL ?>/contacts"><?= lng('contacts') ?></a>
</p>