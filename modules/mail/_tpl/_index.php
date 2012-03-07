<p><a href="<?= Vars::$MODULE_URI ?>?act=add"><?= lng('write_message') ?></a></p>
<div class="phdr"><strong><?= lng('mail') ?></strong></div>

<?php if ($this->total): ?>
<div>
    <form action="<?= Vars::$MODULE_URI ?>" method="post">
        <?php foreach ($this->query as $row): ?>
        <div class="<?=$row['list']?>">
            <input type="checkbox" name="delch[]" value="<?= $row['id'] ?>"/> <?= $row['icon'] ?> <a
            href="<?= $row['url'] ?>"><?= $row['nickname'] ?></a> <?= $row['online'] ?> (<?= $row['count_in'] ?>&#160;/&#160;<?= $row['count_out'] ?>
            ) <span class="red"><?= $row['count_new'] ?></span>
        </div>
        <?php endforeach ?>
        <div class="gmenu"><?= lng('noted_contacts') ?>:<br/>
            <input type="submit" name="archive" value="<?= lng('in_archive') ?>"/>&#160;<input type="submit" name="delete" value="<?= lng('delete') ?>"/><br/>
        </div>
        <?php if ($this->total > Vars::$USER_SET['page_size']): ?>
        <div class="phdr"><?= lng('total') ?>: <?= $this->total ?></div>
        <div class="topmenu"><?= $this->display_pagination ?></div>
        <form action="" method="post"><p><input type="text" name="page" size="2" value="<?=Vars::$PAGE?>"
                                                style="font-size: x-small;"/>
            <input type="submit" value="<?= lng('to_page') ?> &gt;&gt;" style="font-size: x-small;"/></p></form>
        <?php endif ?>

    </form>
</div>
<?php endif ?>

<div class="list2"><p>
    <ul>
        <li><a href="<?= Vars::$MODULE_URI ?>?act=systems"><?= lng('system') ?></a>&#160;(<?=$this->systems?>)</li>
        <li><a href="<?= Vars::$MODULE_URI ?>?act=elected"><?= lng('elected') ?></a>&#160;(<?=$this->elected?>)
        </li>
        <li><a href="<?= Vars::$MODULE_URI ?>?act=archive"><?= lng('archive') ?></a>&#160;(<?=$this->archive?>)
        </li>
        <li><a href="<?= Vars::$MODULE_URI ?>?act=banned"><?= lng('banned') ?></a>&#160;(<?=$this->banned?>)</li>
        <li><a href="<?= Vars::$MODULE_URI ?>?act=files"><?= lng('files') ?></a>&#160;(<?=$this->files?>)</li>
        <li><a href="<?= Vars::$MODULE_URI ?>?act=basket"><?= lng('basket') ?></a>&#160;(<?=$this->delete?>)</li>
        <li><a href="<?= Vars::$MODULE_URI ?>?act=search"><?= lng('search_contact') ?></a></li>
    </ul>
    </p></div>