<div class="phdr">
    <b><?= lng('admin_panel') ?></b>
</div>
<div class="user" style="padding-top: 2px; padding-bottom: 6px">
    <h3><?= Functions::getImage('users.png', '', 'class="left"') ?>&#160;<?= lng('users') ?></h3>
    <ul>
        <?php if (Vars::$USER_RIGHTS >= 7 && $this->regTotal) : ?>
        <li><span class="red"><a href="<?= Vars::$MODULE_URI ?>"><b><?= lng('users_reg') ?></a>&#160;(<?= $this->regTotal ?>)</b></span></li>
        <?php endif; ?>
        <li><a href="<?= Vars::$HOME_URL ?>/users/search"><?= lng('users_list') ?></a>&#160;(<?= $this->usrTotal ?>)</li>
        <?php if (Vars::$USER_RIGHTS >= 7) : ?>
        <li><a href="<?= Vars::$MODULE_URI ?>?act=users_settings"><?= lng('settings') ?></a></li>
        <?php endif; ?>
    </ul>
</div>
<div class="gmenu" style="padding-top: 2px; padding-bottom: 6px">
    <h3><?= Functions::getImage('modules.png', '', 'class="left"') ?>&#160;<?= lng('modules') ?></h3>
    <ul>
        <li><a href="<?= Vars::$HOME_URL ?>/forum/admin"><?= lng('forum') ?></a></li>
    </ul>
</div>
<?php if (Vars::$USER_RIGHTS >= 7) : ?>
<div class="menu" style="padding-top: 2px; padding-bottom: 6px">
    <h3><?= Functions::getImage('settings.png', '', 'class="left"') ?>&#160;<?= lng('system') ?></h3>
    <ul>
        <li><a href="<?= Vars::$URI ?>?act=system"><?= lng('system_settings') ?></a></li>
        <li><a href="<?= Vars::$URI ?>/languages"><?= lng('language_settings') ?></a></li>
        <li><a href="<?= Vars::$HOME_URL ?>/smileys?act=refresh"><?= lng('smileys') ?></a></li>
    </ul>
</div>
<div class="rmenu" style="padding-top: 2px; padding-bottom: 6px">
    <h3><?= Functions::getImage('blocked.png', '', 'class="left"') ?>&#160;<?= lng('security') ?></h3>
    <ul>
        <?php if (Vars::$USER_RIGHTS == 9) : ?>
        <li><a href="<?= Vars::$URI ?>/ip_acl"><?= lng('ip_accesslist') ?></a></li>
        <?php endif; ?>
    </ul>
</div>
<?php endif; ?>
<div class="phdr">
    &#160;
</div>