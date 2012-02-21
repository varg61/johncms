<div class="phdr">
    <b><?= Vars::$LNG['registration'] ?></b>
</div>
<div class="user">
    <p><?= Functions::displayUser($this->user) ?></p>
    <p>
    <table>
        <tr>
            <td align="right" valign="top"><?= $this->lng['password'] ?>:</td>
            <td>
                <?php if (isset($_GET['pass'])) : ?>
                <strong><?= htmlspecialchars($_SESSION['password']) ?></strong>
                <?php else : ?>
                <a href="<?= Vars::$URI ?>?pass<?= (isset($_GET['auto']) ? '&amp;auto' : '') ?>"><?= $this->lng['show'] ?></a>
                <?php endif ?>
            </td>
        </tr>
        <tr>
            <td><?= $this->lng['autologin'] ?>:</td>
            <td>
                <?php if (isset($_GET['auto'])) : ?>
                <input type="text" value="<?= Vars::$HOME_URL . '/login.php?id=' . $_SESSION['uid'] . '&amp;token=' . htmlspecialchars($_SESSION['token']) ?>"/>
                <?php else : ?>
                <a href="<?= Vars::$URI ?>?auto<?= (isset($_GET['pass']) ? '&amp;pass' : '') ?>"><?= $this->lng['show'] ?></a>
                <?php endif ?>
            </td>
        </tr>
    </table>
    </p>
</div>
<div class="gmenu">
    <h3><?= $this->lng['thanks_for_registration'] ?>!</h3>
</div>