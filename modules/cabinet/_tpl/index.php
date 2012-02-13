<div class="phdr">
    <b><?= $this->lng['my_office'] ?></b>
</div>
<div class="list2">
    <p>
    <div><?= Functions::getImage('contacts.png') ?>&#160;<a href="<?= Vars::$MODULE_URI ?>/profile9"><?= $this->lng['my_profile'] ?></a></div>
    <div><?= Functions::getImage('rating.png') ?>&#160;<a href="profile.php?act=stat"><?= Vars::$LNG['statistics'] ?></a></div>
    <div><?= Functions::getImage('album_4.png') ?>&#160;<a href="album.php?act=list"><?= Vars::$LNG['photo_album'] ?></a>&#160;(<?= $this->total_photo ?>)</div>
    <div><?= Functions::getImage('comments.png') ?>&#160;<a href="profile.php?act=guestbook"><?= Vars::$LNG['guestbook'] ?></a>&#160;()</div>
    <?php if (Vars::$USER_RIGHTS) : ?>
    <div><?= Functions::getImage('blocked.png') ?>&#160;<a href="../guestbook/index.php?act=ga&amp;do=set"><?= Vars::$LNG['admin_club'] ?></a> (<span class="red">()</span>)</div>
    <?php endif ?>
    </p>
</div>
<div class="menu">
    <p>
    <h3><?= Functions::getImage('mail-inbox.png') ?>&#160;<?= $this->lng['my_mail'] ?></h3>
    <ul>
        <li><a href=""><?= Vars::$LNG['mail_new'] ?></a>&#160;(x)</li>
        <li><a href="pm.php"><?= $this->lng['all_mail'] ?></a></li>
        <?php if (!isset(Vars::$USER_BAN['1']) && !isset(Vars::$USER_BAN['3'])) : ?>
        <form action="pm.php?act=write" method="post">
            <p><input type="submit" value=" <?= Vars::$LNG['write'] ?> "/></p>
        </form>
        <?php endif ?>
    </ul>
    <h3><?= Functions::getImage('contacts.png') ?>&#160;<?= Vars::$LNG['contacts'] ?></h3>
    <ul>
        <li><a href="cont.php"><?= Vars::$LNG['contacts'] ?></a>&#160;()</li>
        <li><a href="ignor.php"><?= Vars::$LNG['blocking'] ?></a>&#160;()</li>
    </ul>
    </p>
</div>
<div class="bmenu"><p>
    <h3><?= Functions::getImage('settings.png') ?>&#160;<?= $this->lng['my_settings'] ?></h3>
    <ul>
        <li><a href="profile.php?act=settings"><?= Vars::$LNG['system_settings'] ?></a></li>
        <li><a href="profile.php?act=edit"><?= $this->lng['profile_edit'] ?></a></li>
        <li><a href="profile.php?act=password"><?= Vars::$LNG['change_password'] ?></a></li>
        <?php if (Vars::$USER_RIGHTS) : ?>
        <li><span class="red"><a href="<?= Vars::$HOME_URL ?>/admin"><b><?= Vars::$LNG['admin_panel'] ?></b></a></span></li>
        <?php endif ?>
    </ul>
    </p></div>