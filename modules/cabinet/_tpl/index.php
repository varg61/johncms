<div class="phdr">
    <b><?= lng('my_office') ?></b>
</div>
<div class="list2">
    <p>
    <div><?= Functions::getImage('contacts.png') ?>&#160;<a href="<?= Vars::$HOME_URL ?>/profile"><?= lng('my_profile') ?></a></div>
    <div><?= Functions::getImage('rating.png') ?>&#160;<a href="profile.php?act=stat"><?= lng('statistics') ?></a></div>
    </p><p>
    <div><?= Functions::getImage('album_4.png') ?>&#160;<a href="album.php?act=list"><?= lng('photo_album') ?></a>&#160;(<?= $this->total_photo ?>)</div>
    <div><?= Functions::getImage('comments.png') ?>&#160;<a href="profile.php?act=guestbook"><?= lng('guestbook') ?></a>&#160;()</div>
    <?php if (Vars::$USER_RIGHTS) : ?>
    <div><?= Functions::getImage('blocked.png') ?>&#160;<a href="../guestbook/index.php?act=ga&amp;do=set"><?= lng('admin_club') ?></a> (<span class="red">()</span>)</div>
    <?php endif ?>
    </p>
</div>
<div class="menu">
    <p>
    <h3><?= Functions::getImage('mail-inbox.png') ?>&#160;<?= lng('my_mail') ?></h3>
    <ul>
        <li><a href=""><?= lng('mail_new') ?></a>&#160;(x)</li>
        <li><a href="pm.php"><?= lng('all_mail') ?></a></li>
        <?php if (!isset(Vars::$USER_BAN['1']) && !isset(Vars::$USER_BAN['3'])) : ?>
        <form action="pm.php?act=write" method="post">
            <p><input type="submit" value=" <?= lng('write') ?> "/></p>
        </form>
        <?php endif ?>
    </ul>
    <h3><?= Functions::getImage('contacts.png') ?>&#160;<?= lng('contacts') ?></h3>
    <ul>
        <li><a href="cont.php"><?= lng('contacts') ?></a>&#160;()</li>
        <li><a href="ignor.php"><?= lng('blocking') ?></a>&#160;()</li>
    </ul>
    </p>
</div>
<div class="bmenu"><p>
    <h3><?= Functions::getImage('settings.png') ?>&#160;<?= lng('my_settings') ?></h3>
    <ul>
        <li><a href="profile.php?act=settings"><?= lng('system_settings') ?></a></li>
        <li><a href="profile.php?act=edit"><?= lng('profile_edit') ?></a></li>
        <li><a href="profile.php?act=password"><?= lng('change_password') ?></a></li>
        <?php if (Vars::$USER_RIGHTS) : ?>
        <li><span class="red"><a href="<?= Vars::$HOME_URL ?>/admin"><b><?= lng('admin_panel') ?></b></a></span></li>
        <?php endif ?>
    </ul>
    </p></div>