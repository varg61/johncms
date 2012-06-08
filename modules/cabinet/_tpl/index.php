<div class="phdr">
    <b><?= lng('my_office') ?></b>
</div>
<div class="list2">
    <div class="formblock">
        <ul style="list-style: none; padding-left: 0">
            <li><?= Functions::getImage('contacts.png') ?>&#160;<a href="<?= Vars::$HOME_URL ?>/profile"><?= lng('my_profile') ?></a></li>
            <li><?= Functions::getImage('rating.png') ?>&#160;<a href="<?= Vars::$HOME_URL ?>/profile?act=stat"><?= lng('statistics') ?></a></li>
            <br style="line-height: 8px"/>
            <li><?= Functions::getImage('album_4.png') ?>&#160;<a href="<?= Vars::$HOME_URL ?>/album?act=list"><?= lng('photo_album') ?></a>&#160;(<?= $this->total_photo ?>)</li>
            <li><?= Functions::getImage('comments.png') ?>&#160;<a href="<?= Vars::$HOME_URL ?>/profile?act=guestbook"><?= lng('guestbook') ?></a>&#160;(<?= Vars::$USER_DATA['comm_count'] ?>)</li>
            <?php if (Vars::$USER_RIGHTS) : ?>
            <br style="line-height: 8px"/>
            <li><?= Functions::getImage('comments_adm.png') ?>&#160;<a href="../guestbook/index.php?act=ga&amp;do=set"><?= lng('admin_club') ?></a> (<span class="red">()</span>)</li>
            <li><?= Functions::getImage('settings.png') ?>&#160;<span class="red"><a href="<?= Vars::$HOME_URL ?>/admin"><b><?= lng('admin_panel') ?></b></a></span></li>
            <?php endif ?>
        </ul>
    </div>
</div>
<div class="menu">
    <div class="formblock">
        <ul style="list-style: none; padding-left: 0">
            <li><?php echo Functions::getImage('mail-copy.png') ?>&#160;<a href="<?php echo Vars::$HOME_URL ?>/mail"><?php echo lng('mail') ?></a>&#160;(<?php echo Functions::mailCount() ?>)</li>
            <li><?= Functions::getImage('users.png') ?>&#160;<a href="<?php echo Vars::$HOME_URL ?>/contacts"><?= lng('contacts') ?></a>&#160;(<?php echo Functions::contactsCount() ?>)</li>
            <li><?= Functions::getImage('friends.png') ?>&#160;<a href="<?php echo Vars::$HOME_URL ?>/friends"><?= lng('friends') ?></a>&#160;(<?php echo Functions::friendsCount($this->user['id']) ?>)</li>
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
    <?= Functions::getImage('exit.png', '', 'align="middle"') ?>&#160;<a href="<?= Vars::$HOME_URL ?>/exit"><?= lng('exit') ?></a>
</div>