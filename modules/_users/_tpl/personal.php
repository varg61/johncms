<ul class="nav">
    <li><h1<?= $this->user['id'] == Vars::$USER_ID ? ' class="section-personal"' : '' ?>><?= lng('settings') ?></h1></li>
</ul>
<div class="user-block"><?= Functions::displayUser($this->user) ?></div>
<ul class="nav">
    <li><h2><?= lng('personal_data') ?></h2></li>
    <li><a href="<?= Vars::$MODULE_URI ?>/profile?act=edit&amp;user=<?= $this->user['id'] ?>"><i class="icn-edit"></i><?= lng('profile_edit') ?><i class="icn-arrow right"></i></a></li>
    <li><a href="#"><i class="icn-edit"></i><?= lng('change_photo') ?><i class="icn-arrow"></i></a></li>
    <li><a href="<?= Vars::$MODULE_URI ?>/profile?act=edit&amp;mod=avatar&amp;user=<?= $this->user['id'] ?>"><i class="icn-edit"></i><?= lng('avatar') ?><i class="icn-arrow"></i></a></li>
    <?php if (Vars::$USER_SYS['change_status']): ?>
    <li><a href="<?= Vars::$MODULE_URI ?>/profile?act=edit&amp;mod=status&amp;user=<?= $this->user['id'] ?>"><i class="icn-edit"></i><?= lng('status') ?><i class="icn-arrow"></i></a></li>
    <?php endif ?>
    <li><h2><?= lng('settings') ?></h2></li>
    <li><a href="<?= Vars::$MODULE_URI ?>/profile?act=settings&amp;user=<?= $this->user['id'] ?>"><i class="icn-settings"></i><?= lng('system_settings') ?><i class="icn-arrow"></i></a></li>
    <li><a href="<?= Vars::$MODULE_URI ?>/profile?act=password&amp;user=<?= $this->user['id'] ?>"><i class="icn-shield-red"></i><?= lng('change_password') ?><i class="icn-arrow"></i></a></li>
    <?php if (Vars::$USER_SYS['change_nickname'] || Vars::$USER_RIGHTS >= 7): ?>
    <li><a href="<?= Vars::$MODULE_URI ?>/profile?act=edit&amp;mod=nickname&amp;user=<?= $this->user['id'] ?>"><i class="icn-shield-red"></i><?= lng('change_nickname') ?><i class="icn-arrow"></i></a></li>
    <?php endif ?>
</ul>