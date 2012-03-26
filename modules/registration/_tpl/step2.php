<div class="phdr">
    <b><?= lng('registration') ?></b>
</div>
<div class="user">
    <p><?= Functions::displayUser($this->user, array('iphide' => 1)) ?></p>
    <p>
    <table>
        <tr>
            <td align="right" valign="top"><?= lng('password') ?>:</td>
            <td>
                <?php if (isset($_GET['pass'])) : ?>
                <strong><?= htmlspecialchars($_SESSION['password']) ?></strong>
                <?php else : ?>
                <a href="<?= Vars::$URI ?>?pass<?= (isset($_GET['auto']) ? '&amp;auto' : '') ?>"><?= lng('show') ?></a>
                <?php endif; ?>
            </td>
        </tr>
        <tr>
            <td><?= lng('autologin') ?>:</td>
            <td>
                <?php if (isset($_GET['auto'])) : ?>
                <input type="text" value="<?= Vars::$HOME_URL . '/login?id=' . $_SESSION['uid'] . '&amp;token=' . htmlspecialchars($_SESSION['token']) ?>"/>
                <?php else : ?>
                <a href="<?= Vars::$URI ?>?auto<?= (isset($_GET['pass']) ? '&amp;pass' : '') ?>"><?= lng('show') ?></a>
                <?php endif; ?>
            </td>
        </tr>
    </table>
    </p>
</div>
<div class="gmenu">
    <h3><?= lng('thanks_for_registration') ?>!</h3>
    <?php if (Vars::$USER_SYS['reg_mode'] == 2) : ?>
    <p style="font-size: x-small"><?= lng('moderation_warning') ?></p>
    <?php endif; ?>
</div>
<div class="phdr">
    <a href="<?= Vars::$HOME_URL ?>"><?= lng('enter_on_site') ?></a>
</div>