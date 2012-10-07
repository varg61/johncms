<ul class="nav">
    <li><h1 class="section-personal"><?= lng('my_assets') ?></h1></li>
</ul>
<div class="user-block"><?= Functions::displayUser(Vars::$USER_DATA) ?></div>
<ul class="nav">
    <li><a href="<?= Vars::$MODULE_URI ?>/profile"><i class="icn-man"></i><?= lng('my_profile') ?><i class="icn-arrow"></i></a></li>
    <li><a href="<?= Vars::$HOME_URL ?>/album?act=list"><i class="icn-image"></i><?= lng('photo_album') ?><i class="icn-arrow"></i><span class="badge badge-right"><?= $this->total_photo ?></span></a></li>
    <li><a href="<?= Vars::$HOME_URL ?>/profile?act=guestbook"><i class="icn-dialogue"></i><?= lng('guestbook') ?><i class="icn-arrow"></i><span class="badge badge-right"><?= Vars::$USER_DATA['comm_count'] ?></span></a></li>

    <li><h2><?= lng('dialogue') ?></h2></li>
    <li><a href="<?= Vars::$HOME_URL ?>/contacts"><i class="icn-addressbook"></i><?= lng('contacts') ?><i class="icn-arrow"></i><span class="badge badge-right"><?= Functions::contactsCount() ?></span></a></li>
    <li><a href="<?= Vars::$HOME_URL ?>/friends"><i class="icn-man-woman"></i><?= lng('friends') ?><i class="icn-arrow"></i><span class="badge badge-right"><?= Functions::friendsCount(Vars::$USER_ID) ?></span></a></li>

    <?php if (Vars::$USER_RIGHTS) : ?>
    <li><h2><?= lng('administration') ?></h2></li>
    <li class="red"><a href="<?= Vars::$HOME_URL ?>/guestbook?mod=adm"><i class="icn-dialogue-red"></i><?= lng('admin_club') ?><i class="icn-arrow"></i><span class="badge badge-red badge-right"><?= $this->count->adminclub ?></span></a></li>
    <li class="red"><a href="<?= Vars::$HOME_URL ?>/admin"><i class="icn-shield-red"></i><?= lng('admin_panel') ?><i class="icn-arrow"></i></a></li>
    <?php endif ?>

    <li><h2><?= lng('settings') ?></h2></li>
    <li><a href="<?= Vars::$MODULE_URI ?>/settings"><i class="icn-settings"></i><?= lng('settings') ?><i class="icn-arrow"></i></a></li>
    <li><a href="<?= Vars::$HOME_URL ?>/users/exit"><i class="icn-exit"></i><?= lng('exit') ?><i class="icn-arrow"></i></a></li>
</ul>