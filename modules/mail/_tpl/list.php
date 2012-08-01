<?= $this->titleTest ?>
<?php if ($this->total > Vars::$USER_SET['page_size']): ?>
<div class="topmenu">
    <?= $this->display_pagination ?>
</div>
<? endif ?>
<form action="<?= $this->url_type ?>" method="post">
    <div>
        <?php foreach ($this->query as $row): ?>
        <div class="<?= $row['list'] ?>">
            <?= $row['icon'] ?> <a href="<?= Vars::$HOME_URL ?>/profile?user=<?= $row['id'] ?>"><?= $row['nickname'] ?></a> <?= $row['online'] ?> (<?= $row['time'] ?>)
            <div class="">
                <?= $row['text'] ?>
                <?php if ($row['file']): ?>
                <div class="func">
                    <?= lng('file') ?>: <?= $row['file'] ?>
                </div>
                <? endif ?>
            </div>
            <div class="sub">
                <input type="checkbox" name="delch[]" value="<?= $row['mid'] ?>"/>
                <?php if (isset($row['read']) && $row['read'] == 0 && $row['user_id'] == Vars::$USER_ID): ?>
                [<a href="<?= Vars::$HOME_URL ?>/mail?act=edit&amp;id=<?= $row['mid'] ?>"><?= lng('edit') ?></a>]
                <? endif ?>
                <?php if ($row['selectBar']): ?>
                <?= $row['selectBar'] ?>
                <? endif ?>
                <?php if (!$row['selectBar']): ?>
                [<span class="red">х</span>&#160;<a href="<?= $row['urlDelete'] ?>"><?= lng('delete') ?></a>]
                <?php if ($row['elected']): ?>
                    [<a href="<?= Vars::$HOME_URL ?>/mail?act=elected&amp;id=<?= $row['mid'] ?>"><?= lng('in_elected') ?></a>]
                    <? endif ?>
                <? endif ?>
            </div>
        </div>
        <? endforeach ?>
        <div class="gmenu">
            <?= lng('noted_mess') ?>:<br/>
            <input type="hidden" name="token" value="<?= $this->token ?>"/>
            <input type="submit" name="delete_mess" value="<?= lng('delete') ?>"/><br/>
        </div>
    </div>
</form>
<div class="phdr"><?= lng('total') ?>: <?= $this->total ?></div>
<?php if ($this->total > Vars::$USER_SET['page_size']): ?>
<div class="topmenu">
    <?= $this->display_pagination ?>
</div>
<form action="" method="post"><p><input type="text" name="page" size="2" value="<?= Vars::$PAGE ?>"/>
    <input type="submit" value="<?= lng('to_page') ?> &gt;&gt;"/></p></form>
<? endif ?>
<?= $this->urlTest ?>