<p><a href="<?= Vars::$MODULE_URI ?>?act=add"><?= lng('write_message') ?></a></p>
<div class="phdr"><strong><?= lng('contacts') ?></strong></div>
<div class="topmenu"><a href="<?= Vars::$MODULE_URI ?>"><?= lng('mail') ?></a></div>
<?php if ($this->total): ?>
<div>
    <form action="<?= Vars::$MODULE_URI ?>?act=contacts" method="post">
        <?php foreach ($this->query as $row): ?>
        <div class="<?= $row['list'] ?>">
            <input type="checkbox" name="delch[]" value="<?= $row['id'] ?>"/> <?= $row['icon'] ?> <a href="<?= $row['url'] ?>"><?= $row['nickname'] ?></a> <?= $row['online'] ?> (<?= $row['count_in'] ?>&#160;/&#160;<?= $row['count_out'] ?>)
            <span class="red"><?= $row['count_new'] ?></span>
        </div>
        <?php endforeach ?>
        <div class="gmenu"><?= lng('noted_contacts') ?>:<br/>
            <input type="hidden" name="token" value="<?= $this->token ?>"/>
            <input type="submit" name="archive" value="<?= lng('in_archive') ?>"/>&#160;<input type="submit" name="delete" value="<?= lng('delete') ?>"/><br/>
        </div>

    </form>
    <?php if ($this->total > Vars::$USER_SET['page_size']): ?>
    <div class="phdr"><?= lng('total') ?>: <?= $this->total ?></div>
    <div class="topmenu"><?= $this->display_pagination ?></div>
    <form action="" method="post"><p><input type="text" name="page" size="2" value="<?= Vars::$PAGE ?>" style="font-size: x-small;"/>
        <input type="submit" value="<?= lng('to_page') ?> &gt;&gt;" style="font-size: x-small;"/></p></form>
    <?php endif ?>
</div>
<?php endif ?>
<div class="list2">
    <?= Functions::getImage('mail-friends.png') ?> <a href="<?= Vars::$HOME_URL ?>/friends"><?= lng('friends') ?></a>&#160;(<?= $this->friends ?>)<br/>
    <?= Functions::getImage('mail-blocked.png') ?> <a href="<?= Vars::$MODULE_URI ?>?act=banned"><?= lng('banned') ?></a>&#160;(<?= $this->banned ?>)<br/>
    <?= Functions::getImage('mail-archive.png') ?> <a href="<?= Vars::$MODULE_URI ?>?act=archive"><?= lng('archive') ?></a>&#160;(<?= $this->archive ?>)<br/>
    <?= Functions::getImage('mail-search.png') ?> <a href="<?= Vars::$MODULE_URI ?>?act=search"><?= lng('search_contact') ?></a>
</div>