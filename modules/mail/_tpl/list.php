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
<!--      //TODO: Переделать ссылку      -->
            <?= $row['icon'] ?> <a href="<?= Vars::$HOME_URL ?>/profile?user=<?= $row['id'] ?>"><?= $row['nickname'] ?></a> <?= $row['online'] ?> (<?= $row['time'] ?>)
            <div class="">
                <?= $row['text'] ?>
                <?php if ($row['file']): ?>
                <div class="func">
                    <?= __('file') ?>: <?= $row['file'] ?>
                </div>
                <? endif ?>
            </div>
            <div class="sub">
                <input type="checkbox" name="delch[]" value="<?= $row['mid'] ?>"/>
                <?php if (isset($row['read']) && $row['read'] == 0 && $row['user_id'] == Vars::$USER_ID): ?>
                [<a href="<?= Vars::$HOME_URL ?>mail/?act=messages&amp;mod=edit&amp;id=<?= $row['mid'] ?>"><?= __('edit') ?></a>]
                <? endif ?>
                <?php if ($row['selectBar']): ?>
                <?= $row['selectBar'] ?>
                <? endif ?>
                <?php if (!$row['selectBar']): ?>
                [<span class="red">х</span>&#160;<a href="<?= $row['urlDelete'] ?>"><?= __('delete') ?></a>]
                <?php if ($row['elected']): ?>
                    [<a href="<?= Vars::$HOME_URL ?>mail/?act=messages&amp;mod=elected&amp;id=<?= $row['mid'] ?>"><?= __('in_elected') ?></a>]
                    <? endif ?>
                <? endif ?>
            </div>
        </div>
        <? endforeach ?>
        <div class="gmenu">
            <?= __('noted_mess') ?>:<br/>
            <input type="hidden" name="token" value="<?= $this->token ?>"/>
            <input type="submit" name="delete_mess" value="<?= __('delete') ?>"/><br/>
        </div>
    </div>
</form>
<div class="phdr"><?= __('total') ?>: <?= $this->total ?></div>
<?php if ($this->total > Vars::$USER_SET['page_size']): ?>
<div class="topmenu">
    <?= $this->display_pagination ?>
</div>
<form action="" method="post"><p><input type="text" name="page" size="2" value="<?= Vars::$PAGE ?>"/>
    <input type="submit" value="<?= __('to_page') ?> &gt;&gt;"/></p></form>
<? endif ?>
<?= $this->urlTest ?>