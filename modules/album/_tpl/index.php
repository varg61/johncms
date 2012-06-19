<div class="phdr">
    <b><?= lng('photo_albums') ?></b>
</div>
<div class="menu">
    <div class="formblock">
        <label><?= lng('new') ?></label>
        <ul style="list-style: none; padding-left: 0">
            <li><?= Functions::getIcon('image-plus.png') ?>&#160;<a href="<?= Vars::$URI ?>?act=new"><?= lng('photos') ?></a> (<?= $this->new ?>)</li>
            <li><?= Functions::getIcon('comments-add.png') ?>&#160;<a href="<?= Vars::$URI ?>/top?act=last_comm"><?= lng('comments') ?></a></li>
        </ul>
    </div>
    <div class="formblock">
        <label><?= lng('albums') ?></label>
        <ul style="list-style: none; padding-left: 0">
            <li><?= Functions::getIcon('user.png') ?>&#160;<a href="<?= Vars::$URI ?>?act=users&amp;mod=boys"><?= lng('mans') ?></a> (<?= $this->count_m ?>)</li>
            <li><?= Functions::getIcon('user-female.png') ?>&#160;<a href="<?= Vars::$URI ?>?act=users&amp;mod=girls"><?= lng('womans') ?></a> (<?= $this->count_w ?>)</li>
            <?php if (Vars::$USER_ID): ?>
            <li><?= Functions::getIcon('photo-album.png') ?>&#160;<a href="<?= Vars::$URI ?>?act=list"><?= lng('my_album') ?></a> (<?= $this->count_my ?>)</li>
            <?php endif ?>
        </ul>
    </div>
    <div class="formblock">
        <label><?= lng('rating') ?></label>
        <ul style="list-style: none; padding-left: 0">
            <li><?= Functions::getIcon('chart.png') ?>&#160;<a href="<?= Vars::$URI ?>/top"><?= lng('top_votes') ?></a></li>
            <li><?= Functions::getIcon('chart.png') ?>&#160;<a href="<?= Vars::$URI ?>/top?act=downloads"><?= lng('top_downloads') ?></a></li>
            <li><?= Functions::getIcon('chart.png') ?>&#160;<a href="<?= Vars::$URI ?>/top?act=views"><?= lng('top_views') ?></a></li>
            <li><?= Functions::getIcon('chart.png') ?>&#160;<a href="<?= Vars::$URI ?>/top?act=comments"><?= lng('top_comments') ?></a></li>
            <li><?= Functions::getIcon('chart.png') ?>&#160;<a href="<?= Vars::$URI ?>/top?act=trash"><?= lng('top_trash') ?></a></li>
        </ul>
    </div>
</div>
<div class="phdr"><a href="<?= Vars::$HOME_URL ?>/users"><?= lng('users') ?></a></div>