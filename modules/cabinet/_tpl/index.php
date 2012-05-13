<div class="phdr">
    <b><?= lng('my_office') ?></b>
</div>
<div class="list2">
    <div class="formblock">
        <div><?= Functions::getImage('contacts.png') ?>&#160;<a href="<?= Vars::$HOME_URL ?>/profile"><?= lng('my_profile') ?></a></div>
        <div><?= Functions::getImage('rating.png') ?>&#160;<a href="<?= Vars::$HOME_URL ?>/profile?act=stat"><?= lng('statistics') ?></a></div>
    </div>
    <div class="formblock">
        <div>
            <?= Functions::getImage('album_4.png') ?>&#160;<a href="<?= Vars::$HOME_URL ?>/album?act=list"><?= lng('photo_album') ?></a>&#160;(<?= $this->total_photo ?>)
        </div>
        <div>
            <?= Functions::getImage('comments.png') ?>&#160;<a href="<?= Vars::$HOME_URL ?>/profile?act=guestbook"><?= lng('guestbook') ?></a>&#160;(<?= Vars::$USER_DATA['comm_count'] ?>)
        </div>
    </div>
    <?php if (Vars::$USER_RIGHTS) : ?>
    <div class="formblock">
        <?= Functions::getImage('blocked.png') ?>&#160;<a href="../guestbook/index.php?act=ga&amp;do=set"><?= lng('admin_club') ?></a> (<span class="red">()</span>)
    </div>
    <?php endif; ?>
</div>
<div class="menu">
    <div class="formblock">
        <div>
            <?php echo Functions::getImage('mail-copy.png') ?>&#160;<a href="<?php echo Vars::$HOME_URL ?>/mail"><?php echo lng('mail') ?></a>&#160;(<?php echo Functions::mailCount() ?>)
        </div>
    </div>
    <div class="formblock">
        <div>
            <?= Functions::getImage('users.png') ?>&#160;<a href="<?php echo Vars::$HOME_URL ?>/contacts"><?= lng('contacts') ?></a>&#160;(<?php echo Functions::contactsCount() ?>)
        </div>
        <div>
            <?= Functions::getImage('friends.png') ?>&#160;<a href="<?php echo Vars::$HOME_URL ?>/friends"><?= lng('friends') ?></a>&#160;(<?php echo Functions::friendsCount($this->user['id']) ?>)
        </div>
    </div>
</div>
<div class="bmenu">
    <p>

    <h3><?= Functions::getImage('settings.png') ?>&#160;<?= lng('my_settings') ?></h3>
    <ul>
        <li><a href="<?= Vars::$HOME_URL ?>/profile?act=settings"><?= lng('system_settings') ?></a></li>
        <li><a href="<?= Vars::$HOME_URL ?>/profile?act=edit"><?= lng('profile_edit') ?></a></li>
        <li><a href="<?= Vars::$HOME_URL ?>/profile?act=password"><?= lng('change_password') ?></a></li>
        <?php if (Vars::$USER_RIGHTS) : ?>
        <li><span class="red"><a href="<?= Vars::$HOME_URL ?>/admin"><b><?= lng('admin_panel') ?></b></a></span></li>
        <?php endif; ?>
    </ul>
    </p>
</div>
<div class="phdr">
    <?= Functions::getImage('exit.png', '', 'align="middle"') ?>&#160;<a href="<?= Vars::$HOME_URL ?>/exit"><?= lng('exit') ?></a>
</div>