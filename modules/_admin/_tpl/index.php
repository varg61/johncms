<ul class="nav">
    <li><h1 class="section-warning"><?= lng('admin_panel') ?></h1></li>

    <li><h2><?= lng('community') ?></h2></li>
    <li><a href="<?= Vars::$HOME_URL ?>/users/search"><i class="icn-man-woman"></i><?= lng('users') ?><i class="icn-arrow"></i><span class="badge badge-right"><?= $this->usrTotal ?></span></a></li>
    <?php if (Vars::$USER_RIGHTS >= 7) : ?>
    <li><a href="<?= Vars::$MODULE_URI ?>"><i class="icn-user-add"></i><?= lng('users_reg') ?><i class="icn-arrow"></i><span class="badge badge-right"><?= $this->regTotal ?></span></a></li>
    <li><a href="<?= Vars::$URI ?>?act=users_settings"><i class="icn-settings"></i><?= lng('settings') ?><i class="icn-arrow right"></i></a></li>

    <li><h2><?= lng('modules') ?></h2></li>
    <li><a href="<?= Vars::$HOME_URL ?>/forum/admin"><i class="icn-comments"></i><?= lng('forum') ?><i class="icn-arrow right"></i></a></li>
    <li><a href="<?= Vars::$HOME_URL ?>/download/admin"><i class="icn-download"></i><?= lng('downloads') ?><i class="icn-arrow right"></i></a></li>
    <li><a href="<?= Vars::$HOME_URL ?>/news/admin"><i class="icn-news"></i><?= lng('news') ?><i class="icn-arrow right"></i></a></li>
    <li><a href="<?= Vars::$URI ?>/counters"><i class="icn-meter"></i><?= lng('counters') ?><i class="icn-arrow right"></i></a></li>
    <li><a href="<?= Vars::$URI ?>/sitemap"><i class="icn-map"></i><?= lng('sitemap') ?><i class="icn-arrow right"></i></a></li>
    <li><a href="<?= Vars::$URI ?>/links"><i class="icn-flower"></i><?= lng('advertisement') ?><i class="icn-arrow right"></i></a></li>

    <?php if (Vars::$USER_RIGHTS == 9) : ?>
        <li><h2><?= lng('system') ?></h2></li>
        <li><a href="<?= Vars::$URI ?>?act=settings"><i class="icn-settings"></i><?= lng('system_settings') ?><i class="icn-arrow right"></i></a></li>
        <li><a href="<?= Vars::$URI ?>/languages"><i class="icn-settings"></i><?= lng('language_settings') ?><i class="icn-arrow right"></i></a></li>
        <li><a href="<?= Vars::$HOME_URL ?>/smileys?act=refresh"><i class="icn-smile"></i><?= lng('smileys') ?><i class="icn-arrow right"></i></a></li>
    <?php endif ?>

    <li><h2><?= lng('security') ?></h2></li>
    <li><a href="<?= Vars::$URI ?>/acl"><i class="icn-shield"></i><?= lng('acl') ?><i class="icn-arrow right"></i></a></li>
    <?php if (Vars::$USER_RIGHTS == 9) : ?>
        <li><a href="<?= Vars::$URI ?>/ip"><i class="icn-shield"></i><?= lng('ip_accesslist') ?><i class="icn-arrow right"></i></a></li>
    <?php endif ?>
    <li><a href="<?= Vars::$URI ?>/antispy"><i class="icn-shield"></i><?= lng('antispy') ?><i class="icn-arrow right"></i></a></li>
    <?php endif ?>
</ul>