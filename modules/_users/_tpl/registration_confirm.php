<ul class="nav">
    <li><h1><?= lng('registration') ?></h1></li>
</ul>
<div class="form-container">
    <div class="form-block">
        <?= Functions::displayUser(Vars::$USER_DATA, array('iphide' => 1,)) ?>
    </div>
    <div class="form-block">
        <span class="info-message"><?= lng('thanks_for_registration') ?>!</span>
        <?php if (Vars::$USER_SYS['reg_moderation']): ?>
        <br/><br/><span class="input-help"><?= lng('moderation_warning') ?></span>
        <?php endif ?>
        <br/><br/>
        <table>
            <tr>
                <td align="right" valign="top"><?= lng('password') ?>:</td>
                <td>
                    <?php if (isset($_GET['pass'])): ?>
                    <strong><?= htmlspecialchars($_SESSION['password']) ?></strong>
                    <?php else: ?>
                    <a href="<?= Vars::$URI ?>?pass<?= (isset($_GET['auto']) ? '&amp;auto' : '') ?>"><?= lng('show') ?></a>
                    <?php endif ?>
                </td>
            </tr>
            <tr>
                <td><?= lng('autologin') ?>:</td>
                <td>
                    <?php if (isset($_GET['auto'])): ?>
                    <input type="text" value="<?= Vars::$HOME_URL . '/login?id=' . $_SESSION['uid'] . '&amp;token=' . htmlspecialchars($_SESSION['token']) ?>"/>
                    <?php else: ?>
                    <a href="<?= Vars::$URI ?>?auto<?= (isset($_GET['pass']) ? '&amp;pass' : '') ?>"><?= lng('show') ?></a>
                    <?php endif ?>
                </td>
            </tr>
        </table>

        <br/><a class="btn btn-primary btn-large" href="<?= Vars::$HOME_URL ?>"><?= lng('enter_on_site') ?></a>
    </div>
</div>