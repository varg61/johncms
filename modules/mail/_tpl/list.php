<?= $this->titleTest ?>
<?php if ($this->total > Vars::$USER_SET['page_size']): ?>
<div class="topmenu">
    <?= $this->display_pagination ?>
</div>
<?php endif ?>
<?php foreach ($this->query as $row): ?>
<div class="<?= $row['list'] ?>">
    <?= $row['icon'] ?> <a href="<?= Vars::$HOME_URL ?>/users/profile.php?user=<?= $row['id'] ?>"><?= $row['nickname'] ?></a> <?= $row['online'] ?> (<?= $row['time'] ?>)
    <div class="">
        <?= $row['text'] ?>
        <?php if ($row['file']): ?>
        <div class="func">
            <?= lng('file') ?>: <?= $row['file'] ?>
        </div>
        <?php endif ?>
    </div>
    <div class="sub">
        <?php if ($row['selectBar']): ?>
        <?= $row['selectBar'] ?>
        <?php endif ?>
        <?php if (!$row['selectBar']): ?>
        [<span class="red">х</span>&#160;<a href="<?= $row['urlDelete'] ?>"><?= lng('delete') ?></a>]
        <?php if ($row['elected']): ?>
            [<a href="<?= Vars::$HOME_URL ?>/mail?act=messages&amp;mod=elected&amp;id=<?=$row['mid']?>"><?= lng('in_elected') ?></a>]
            <?php endif ?>
        <?php endif ?>
    </div>
</div>
<?php endforeach ?>
<div class="phdr">
    <?= lng('total') ?>: <?= $this->total ?>
</div>
<?php if ($this->total > Vars::$USER_SET['page_size']): ?>
<div class="topmenu">
    <?= $this->display_pagination ?>
</div>
<form action="" method="post"><p><input type="text" name="page" size="2" value="<?=Vars::$PAGE?>"/>
    <input type="submit" value="<?= lng('to_page') ?> &gt;&gt;"/></p></form>
<?php endif ?>
<?= $this->urlTest ?>