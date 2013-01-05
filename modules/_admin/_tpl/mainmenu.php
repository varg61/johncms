<ul class="nav">
    <li><h1 class="section-warning"><?= __('admin_panel') ?></h1></li>

    <li><h2><?= __('community') ?></h2></li>
    <li><a href="<?= Vars::$HOME_URL ?>/users/search"><i class="icn-man-woman"></i><?= __('users') ?><i class="icn-arrow"></i><span class="badge badge-right"><?= $this->usrTotal ?></span></a></li>

    <?php if (Vars::$USER_RIGHTS >= 7) : ?>
    <li><a href="<?= Vars::$MODULE_URI ?>"><i class="icn-user-add"></i><?= __('users_reg') ?><i class="icn-arrow"></i><span class="badge badge-right"><?= $this->regTotal ?></span></a></li>
    <li><a href="<?= Vars::$URI ?>?act=users_settings"><i class="icn-settings"></i><?= __('settings') ?><i class="icn-arrow right"></i></a></li>
    <li><h2><?= __('modules') ?></h2></li>
    <li><a href="<?= Vars::$HOME_URL ?>/forum/admin"><i class="icn-comments"></i><?= __('forum') ?><i class="icn-arrow right"></i></a></li>
    <li><a href="<?= Vars::$HOME_URL ?>/download/admin"><i class="icn-download"></i><?= __('downloads') ?><i class="icn-arrow right"></i></a></li>
    <li><a href="<?= Vars::$HOME_URL ?>/news/admin"><i class="icn-news"></i><?= __('news') ?><i class="icn-arrow right"></i></a></li>
    <li><a href="<?= Vars::$URI ?>/links"><i class="icn-flower"></i><?= __('advertisement') ?><i class="icn-arrow right"></i></a></li>
    <?php if (Vars::$USER_RIGHTS == 9): ?>
        <li><a href="<?= Vars::$URI ?>/counters"><i class="icn-meter"></i><?= __('counters') ?><i class="icn-arrow right"></i></a></li>
        <li><a href="<?= Vars::$URI ?>?act=sitemap"><i class="icn-map"></i><?= __('sitemap') ?><i class="icn-arrow right"></i></a></li>
        <li><h2><?= __('system') ?></h2></li>
        <li><a href="<?= Vars::$URI ?>?act=system_settings"><i class="icn-settings"></i><?= __('system_settings') ?><i class="icn-arrow right"></i></a></li>
        <li><a href="<?= Vars::$URI ?>?act=language"><i class="icn-settings"></i><?= __('language_settings') ?><i class="icn-arrow right"></i></a></li>
        <li><a href="<?= Vars::$HOME_URL ?>/smileys?act=refresh"><i class="icn-smile"></i><?= __('smileys') ?><i class="icn-arrow right"></i></a></li>
        <?php endif ?>
    <li><a href="<?= Vars::$URI ?>?act=whois"><i class="icn-info"></i>WHOIS<i class="icn-arrow right"></i></a></li>
    <li><h2><?= __('security') ?></h2></li>
    <li><a href="<?= Vars::$URI ?>?act=acl"><i class="icn-shield"></i><?= __('acl') ?><i class="icn-arrow right"></i></a></li>
    <?php if (Vars::$USER_RIGHTS == 9) : ?>
        <li><a href="<?= Vars::$URI ?>?act=firewall"><i class="icn-shield"></i><?= __('firewall') ?><i class="icn-arrow right"></i></a></li>
        <li><a href="<?= Vars::$URI ?>?act=scanner"><i class="icn-shield"></i><?= __('antispy') ?><i class="icn-arrow right"></i></a></li>
        <?php endif ?>
    <?php endif ?>
</ul>