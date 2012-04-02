<div class="phdr">
    <b><?= lng('my_office') ?></b>
</div>
<div class="list2">
    <p>
    <div><?= Functions::getImage('contacts.png') ?>&#160;<a href="<?= Vars::$HOME_URL ?>/profile"><?= lng('my_profile') ?></a></div>
    <div><?= Functions::getImage('rating.png') ?>&#160;<a href="<?= Vars::$HOME_URL ?>/profile?act=stat"><?= lng('statistics') ?></a></div>
    </p>
	<p>
    <div><?= Functions::getImage('album_4.png') ?>&#160;<a href=""><?= lng('photo_album') ?></a>&#160;(<?= $this->total_photo ?>)</div>
    <div><?= Functions::getImage('comments.png') ?>&#160;<a href=""><?= lng('guestbook') ?></a>&#160;()</div>
    <div><?php echo Functions::getImage('mail-inbox.png') ?>&#160;<a href="<?php echo Vars::$HOME_URL ?>/mail"><?php echo lng('mail') ?></a>&#160;(<?php echo Functions::mailCount() ?>)</div>
	<?php if (Vars::$USER_RIGHTS) : ?>
    <div><?= Functions::getImage('blocked.png') ?>&#160;<a href="../guestbook/index.php?act=ga&amp;do=set"><?= lng('admin_club') ?></a> (<span class="red">()</span>)</div>
    <?php endif; ?>
    </p>
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