<div class="phdr">
    <b><?= lng('registration') ?></b>
</div>
<div class="user">
    <p><?= Functions::displayUser($this->user) ?></p>
    <p>
    <table>
        <tr>
            <td align="right" valign="top"><?= lng('password') ?>:</td>
            <td>
                <?php if (isset($_GET['pass'])) : ?>
                <strong><?= htmlspecialchars($_SESSION['password']) ?></strong>
                <?php else : ?>
                <a href="<?= Vars::$URI ?>?pass<?= (isset($_GET['auto']) ? '&amp;auto' : '') ?>"><?= lng('show') ?></a>
                <?php endif ?>
            </td>
        </tr>
        <tr>
            <td><?= lng('autologin') ?>:</td>
            <td>
                <?php if (isset($_GET['auto'])) : ?>
                <input type="text" value="<?= Vars::$HOME_URL . '/login?id=' . $_SESSION['uid'] . '&amp;token=' . htmlspecialchars($_SESSION['token']) ?>"/>
                <?php else : ?>
                <a href="<?= Vars::$URI ?>?auto<?= (isset($_GET['pass']) ? '&amp;pass' : '') ?>"><?= lng('show') ?></a>
                <?php endif ?>
            </td>
        </tr>
    </table>
    </p>
</div>
<div class="gmenu">
    <h3><?= lng('thanks_for_registration') ?>!</h3>
</div>