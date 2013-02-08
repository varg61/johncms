<!-- Заголовок раздела -->
<ul class="title private">
    <li class="center"><h1><?= __('my_assets') ?></h1></li>
</ul>

<div class="user-bloc"><?= Functions::displayUser(Vars::$USER_DATA) ?></div>

<ul class="nav">
    <li><a href="<?= $this->url ?>"><i class="icn-man"></i><?= __('my_profile') ?><i class="icn-arrow"></i></a></li>
    <li><a href="<?= Vars::$HOME_URL ?>album/?act=list"><i class="icn-image"></i><?= __('photo_album') ?><i class="icn-arrow"></i><span class="badge badge-right"><?= $this->total_photo ?></span></a></li>
    <li><a href="<?= Vars::$HOME_URL ?>profile/?act=guestbook"><i class="icn-dialogue"></i><?= __('guestbook') ?><i class="icn-arrow"></i><span class="badge badge-right"><?= Vars::$USER_DATA['comm_count'] ?></span></a></li>

    <li><h2><?= __('dialogue') ?></h2></li>
    <li><a href="<?= Vars::$HOME_URL ?>contacts/"><i class="icn-addressbook"></i><?= __('contacts') ?><i class="icn-arrow"></i><span class="badge badge-right"><?= Functions::contactsCount() ?></span></a></li>
    <li><a href="<?= Vars::$HOME_URL ?>friends/"><i class="icn-man-woman"></i><?= __('friends') ?><i class="icn-arrow"></i><span class="badge badge-right"><?= Functions::friendsCount(Vars::$USER_ID) ?></span></a></li>

    <?php if (Vars::$USER_RIGHTS) : ?>
    <li><h2><?= __('administration') ?></h2></li>
    <li class="red"><a href="<?= Vars::$HOME_URL ?>guestbook/?mod=adm"><i class="icn-dialogue-red"></i><?= __('admin_club') ?><i class="icn-arrow"></i><span class="badge badge-red badge-right"><?= $this->count->adminclub ?></span></a></li>
    <li class="red"><a href="<?= Vars::$HOME_URL ?>admin/"><i class="icn-shield-red"></i><?= __('admin_panel') ?><i class="icn-arrow"></i></a></li>
    <?php endif ?>

    <li><h2><?= __('settings') ?></h2></li>
    <li><a href="<?= $this->url ?>option/"><i class="icn-settings"></i><?= __('settings') ?><i class="icn-arrow"></i></a></li>
    <li><a href="<?= Vars::$HOME_URL ?>users/login/"><i class="icn-exit"></i><?= __('exit') ?><i class="icn-arrow"></i></a></li>
</ul>