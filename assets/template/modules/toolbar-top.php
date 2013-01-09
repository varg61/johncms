<table id="top-bar">
    <tr>
        <td class="logo"><a href="<?= Vars::$HOME_URL ?>"><?= Functions::loadImage('logo.png', 24, '', 'JohnCMS') ?></a></td>
        <td style="width: 44px;">&#160;</td>
    </tr>
</table>
<table id="nav-top">
    <tr>
        <td><a href="<?= Vars::$HOME_URL ?>" <?= (empty(Vars::$PLACE) && !Vars::$ACT ? 'class="select"' : '') ?> title="<?= __('homepage') ?>"><?= Functions::loadImage('toolbar-home.png', 44, '', __('homepage')) ?></a></td>
        <?php if (Vars::$USER_ID): ?>
        <td><a href="<?= Vars::$HOME_URL ?>mail/" <?= (Vars::$PLACE == 'mail' ? 'class="select"' : '') ?> title="<?= __('mail') ?>"><?= Functions::loadImage('toolbar-mail.png', 44, '', __('mail')) ?></a></td>
        <td>
            <a href="<?= Vars::$HOME_URL ?>users/<?= Vars::$USER_ID ?>/menu/" <?= (Vars::$PLACE == 'users/profile?act=assets' ? 'class="select"' : '') ?> title="<?= __('personal') ?>"><?= Functions::loadImage('toolbar-account.png', 44, '', __('personal')) ?></a>
        </td>
        <?php else: ?>
        <td><a href="<?= Vars::$HOME_URL ?>users/login/" <?= (Vars::$PLACE == 'login' ? 'class="select"' : '') ?> title="<?= __('login') ?>"><?= Functions::loadImage('toolbar-login.png', 44, '', __('login')) ?></a></td>
        <?php endif ?>
    </tr>
</table>