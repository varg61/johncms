<ul class="nav">
    <li><h1<?= Users::$data['id'] == Vars::$USER_ID ? ' class="section-personal"' : '' ?>><?= __('settings') ?></h1></li>
</ul>
<div class="user-block">
    <?= Functions::displayUser(Users::$data) ?>
</div>
<ul class="nav">
    <li><h2><?= __('profile') ?></h2></li>
    <li><a href="<?= $this->uri ?>edit/"><i class="icn-edit"></i><?= __('profile_edit') ?><i class="icn-arrow right"></i></a></li>
    <li><a href="#"><i class="icn-edit"></i><?= __('change_photo') ?><i class="icn-arrow"></i></a></li>
    <?php if (Vars::$USER_SYS['change_status']): ?>
    <li><a href="<?= $this->url ?>/profile?act=edit_status&amp;user=<?= $this->user['id'] ?>"><i class="icn-edit"></i><?= __('status') ?><i class="icn-arrow"></i></a></li>
    <?php endif ?>

    <li><h2><?= __('avatar') ?></h2></li>
    <?php if ($this->setUsers['upload_avatars'] || Vars::$USER_RIGHTS >= 7): ?>
    <li><a href="<?= $this->url ?>?act=avatar_upload&amp;user=<?= $this->user['id'] ?>"><i class="icn-upload"></i><?= __('upload_avatar') ?><i class="icn-arrow"></i></a></li>
    <?php endif ?>
    <?php if ($this->user['id'] == Vars::$USER_ID) : ?>
    <li><a href="<?= Vars::$HOME_URL ?>/avatars"><i class="icn-image"></i><?= __('select_in_catalog') ?><i class="icn-arrow"></i></a></li>
    <?php endif ?>
    <?php if (isset($this->avatar)) : ?>
    <li><a href="<?= $this->url ?>?act=avatar_delete&amp;user=<?= $this->user['id'] ?>"><i class="icn-trash"></i><?= __('delete') ?><i class="icn-arrow"></i></a></li>
    <?php endif ?>

    <li><h2><?= __('settings') ?></h2></li>
    <li><a href="<?= $this->url ?>/profile?act=edit_settings&amp;user=<?= $this->user['id'] ?>"><i class="icn-settings"></i><?= __('system_settings') ?><i class="icn-arrow"></i></a></li>
    <?php if (Vars::$USER_RIGHTS >= 7): ?>
    <li><a href="<?= $this->url ?>/profile?act=edit_admin&amp;user=<?= $this->user['id'] ?>"><i class="icn-shield-red"></i><?= __('rank') ?><i class="icn-arrow"></i></a></li>
    <?php endif ?>
    <li><a href="<?= $this->url ?>/profile?act=edit_password&amp;user=<?= $this->user['id'] ?>"><i class="icn-shield"></i><?= __('change_password') ?><i class="icn-arrow"></i></a></li>
    <?php if (Vars::$USER_SYS['change_nickname'] || Vars::$USER_RIGHTS >= 7): ?>
    <li><a href="<?= $this->url ?>/profile?act=edit&amp;mod=nickname&amp;user=<?= $this->user['id'] ?>"><i class="icn-shield"></i><?= __('change_nickname') ?><i class="icn-arrow"></i></a></li>
    <?php endif ?>
</ul>