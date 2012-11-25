<p><a href="<?= Vars::$HOME_URL ?>/mail?act=add"><?= __('write_message') ?></a></p>
<div class="phdr"><strong><?= __('contacts') ?></strong></div>
<div class="topmenu"><a href="<?= Vars::$HOME_URL ?>/mail"><?= __('mail') ?></a></div>
<?php if ($this->total): ?>
<div>
    <form action="<?= Vars::$MODULE_URI ?>/" method="post">
        <?php foreach ($this->query as $row): ?>
        <div class="<?= $row['list'] ?>">
            <input type="checkbox" name="delch[]" value="<?= $row['id'] ?>"/> <?= $row['icon'] ?> <a href="<?= $row['url'] ?>"><?= $row['nickname'] ?></a> <?= $row['online'] ?> (<?= $row['count'] ?>) <span
            class="red"><?= $row['count_new'] ?></span>
        </div>
        <?php endforeach ?>
        <div class="gmenu"><?= __('noted_contacts') ?>:<br/>
            <input type="hidden" name="token" value="<?= $this->token ?>"/>
            <input type="submit" name="archive" value="<?= __('in_archive') ?>"/>&#160;<input type="submit" name="delete" value="<?= __('delete') ?>"/><br/>
        </div>
    </form>
    <?php if ($this->total > Vars::$USER_SET['page_size']): ?>
    <div class="phdr"><?= __('total') ?>: <?= $this->total ?></div>
    <div class="topmenu"><?= $this->display_pagination ?></div>
    <form action="" method="post"><p><input type="text" name="page" size="2" value="<?= Vars::$PAGE ?>" style="font-size: x-small;"/>
        <input type="submit" value="<?= __('to_page') ?> &gt;&gt;" style="font-size: x-small;"/></p></form>
    <?php endif ?>
</div>
<?php else: ?>
<div class="rmenu"><?= __('no_contacts') ?></div>
<?php endif ?>
<div class="list2">
    <?= Functions::getImage('mail-blocked.png') ?> <a href="<?= Vars::$MODULE_URI ?>?act=banned"><?= __('banned') ?></a>&#160;(<?= $this->banned ?>)<br/>
    <?= Functions::getImage('mail-archive.png') ?> <a href="<?= Vars::$MODULE_URI ?>?act=archive"><?= __('archive') ?></a>&#160;(<?= $this->archive ?>)<br/>
    <?= Functions::getImage('mail-search.png') ?> <a href="<?= Vars::$MODULE_URI ?>?act=search"><?= __('search_contact') ?></a>
</div>