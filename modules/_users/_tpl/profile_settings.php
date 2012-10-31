<ul class="nav">
    <li><h1<?= $this->user['id'] == Vars::$USER_ID ? ' class="section-personal"' : '' ?>><?= lng('settings') ?></h1></li>
</ul>
<div class="user-block"><?= Functions::displayUser($this->user) ?></div>
<ul class="nav">
    <li><h2><?= lng('profile') ?></h2></li>
    <li><a href="<?= Vars::$MODULE_URI ?>/profile?act=edit&amp;user=<?= $this->user['id'] ?>"><i class="icn-edit"></i><?= lng('profile_edit') ?><i class="icn-arrow right"></i></a></li>
    <li><a href="#"><i class="icn-edit"></i><?= lng('change_photo') ?><i class="icn-arrow"></i></a></li>
    <?php if (Vars::$USER_SYS['change_status']): ?>
    <li><a href="<?= Vars::$MODULE_URI ?>/profile?act=edit_status&amp;user=<?= $this->user['id'] ?>"><i class="icn-edit"></i><?= lng('status') ?><i class="icn-arrow"></i></a></li>
    <?php endif ?>

    <li><h2><?= lng('avatar') ?></h2></li>
    <?php if ($this->setUsers['upload_avatars'] || Vars::$USER_RIGHTS >= 7): ?>
    <li><a href="<?= Vars::$URI ?>?act=avatar_upload&amp;user=<?= $this->user['id'] ?>"><i class="icn-upload"></i><?= lng('upload_avatar') ?><i class="icn-arrow"></i></a></li>
    <?php endif ?>
    <?php if ($this->user['id'] == Vars::$USER_ID) : ?>
    <li><a href="<?= Vars::$HOME_URL ?>/avatars"><i class="icn-image"></i><?= lng('select_in_catalog') ?><i class="icn-arrow"></i></a></li>
    <?php endif ?>
    <?php if (isset($this->avatar)) : ?>
    <li><a href="<?= Vars::$URI ?>?act=avatar_delete&amp;user=<?= $this->user['id'] ?>"><i class="icn-trash"></i><?= lng('delete') ?><i class="icn-arrow"></i></a></li>
    <?php endif ?>

    <li><h2><?= lng('settings') ?></h2></li>
    <li><a href="<?= Vars::$MODULE_URI ?>/profile?act=edit_settings&amp;user=<?= $this->user['id'] ?>"><i class="icn-settings"></i><?= lng('system_settings') ?><i class="icn-arrow"></i></a></li>
    <?php if (Vars::$USER_RIGHTS >= 7): ?>
    <li><a href="<?= Vars::$MODULE_URI ?>/profile?act=edit_admin&amp;user=<?= $this->user['id'] ?>"><i class="icn-shield-red"></i><?= lng('rank') ?><i class="icn-arrow"></i></a></li>
    <?php endif ?>
    <li><a href="<?= Vars::$MODULE_URI ?>/profile?act=edit_password&amp;user=<?= $this->user['id'] ?>"><i class="icn-shield"></i><?= lng('change_password') ?><i class="icn-arrow"></i></a></li>
    <?php if (Vars::$USER_SYS['change_nickname'] || Vars::$USER_RIGHTS >= 7): ?>
    <li><a href="<?= Vars::$MODULE_URI ?>/profile?act=edit&amp;mod=nickname&amp;user=<?= $this->user['id'] ?>"><i class="icn-shield"></i><?= lng('change_nickname') ?><i class="icn-arrow"></i></a></li>
    <?php endif ?>
</ul>