<div class="phdr">
    <b><?= lng('my_office') ?></b>
</div>
<div class="list2">
    <div class="formblock">
        <ul style="list-style: none; padding-left: 0">
            <li><?= Functions::getIcon('profile.png') ?>&#160;<a href="<?= Vars::$HOME_URL ?>/profile"><?= lng('my_profile') ?></a></li>
            <li><?= Functions::getIcon('chart.png') ?>&#160;<a href="<?= Vars::$HOME_URL ?>/profile?act=stat"><?= lng('statistics') ?></a></li>
            <br style="line-height: 8px"/>
            <li><?= Functions::getIcon('photo-album.png') ?>&#160;<a href="<?= Vars::$HOME_URL ?>/album?act=list"><?= lng('photo_album') ?></a>&#160;(<?= $this->total_photo ?>)</li>
            <li><?= Functions::getIcon('comments.png') ?>&#160;<a href="<?= Vars::$HOME_URL ?>/profile?act=guestbook"><?= lng('guestbook') ?></a>&#160;(<?= Vars::$USER_DATA['comm_count'] ?>)</li>
            <?php if (Vars::$USER_RIGHTS) : ?>
            <br style="line-height: 8px"/>
            <li><?= Functions::getIcon('comments-warn.png') ?>&#160;<a href="<?= Vars::$HOME_URL ?>/guestbook?mod=adm"><?= lng('admin_club') ?></a> <span class="red">(<?= $this->count->adminclub ?>)</span></li>
            <li><?= Functions::getIcon('wrench-screwdriver.png') ?>&#160;<span class="red"><a href="<?= Vars::$HOME_URL ?>/admin"><b><?= lng('admin_panel') ?></b></a></span></li>
            <?php endif ?>
        </ul>
    </div>
</div>
<div class="menu">
    <div class="formblock">
        <ul style="list-style: none; padding-left: 0">
            <li><?= Functions::getIcon('mail.png') ?>&#160;<a href="<?= Vars::$HOME_URL ?>/mail"><?= lng('mail') ?></a>&#160;(<?= Functions::mailCount() ?>)</li>
            <li><?= Functions::getIcon('cards-address.png') ?>&#160;<a href="<?= Vars::$HOME_URL ?>/contacts"><?= lng('contacts') ?></a>&#160;(<?= Functions::contactsCount() ?>)</li>
            <li><?= Functions::getIcon('friend.png') ?>&#160;<a href="<?= Vars::$HOME_URL ?>/friends"><?= lng('friends') ?></a>&#160;(<?= Functions::friendsCount($this->user['id']) ?>)</li>
        </ul>
    </div>
</div>
<div class="bmenu">
    <div class="formblock">
        <ul>
            <li><a href="<?= Vars::$HOME_URL ?>/profile?act=settings"><?= lng('system_settings') ?></a></li>
            <li><a href="<?= Vars::$HOME_URL ?>/profile?act=edit"><?= lng('profile_edit') ?></a></li>
            <li><a href="<?= Vars::$HOME_URL ?>/profile?act=password"><?= lng('change_password') ?></a></li>
        </ul>
    </div>
</div>
<div class="phdr">
    <?= Functions::getIcon('exit.png', '', '', 'align="middle"') ?>&#160;<a href="<?= Vars::$HOME_URL ?>/exit"><?= lng('exit') ?></a>
</div>