<table id="top-bar">
    <tr>
        <td class="logo"><a href="<?= Vars::$HOME_URL ?>"><?= Functions::loadImage('logo.png', 24, '', 'JohnCMS') ?></a></td>
        <td style="width: 44px;">&#160;</td>
    </tr>
</table>
<table id="nav-top">
    <tr>
        <td><a href="<?= Vars::$HOME_URL ?>" <?= (empty(Vars::$PLACE) && !Vars::$ACT ? 'class="select"' : '') ?> title="<?= lng('homepage') ?>"><?= Functions::loadImage('toolbar-home.png', 44, '', lng('homepage')) ?></a></td>
        <?php if (Vars::$USER_ID): ?>
        <td><a href="<?= Vars::$HOME_URL ?>/mail" <?= (Vars::$PLACE == 'mail' ? 'class="select"' : '') ?> title="<?= lng('mail') ?>"><?= Functions::loadImage('toolbar-mail.png', 44, '', lng('mail')) ?></a></td>
        <td><a href="<?= Vars::$HOME_URL ?>/users/profile?act=assets" <?= (Vars::$PLACE == 'users/assets' ? 'class="select"' : '') ?> title="<?= lng('personal') ?>"><?= Functions::loadImage('toolbar-account.png', 44, '', lng('personal')) ?></a></td>
        <?php else: ?>
        <td><a href="<?= Vars::$HOME_URL ?>/users/login" <?= (Vars::$PLACE == 'login' ? 'class="select"' : '') ?> title="<?= lng('login') ?>"><?= Functions::loadImage('toolbar-login.png', 44, '', lng('login')) ?></a></td>
        <?php endif ?>
    </tr>
</table>