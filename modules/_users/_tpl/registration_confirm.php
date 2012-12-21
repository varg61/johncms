<ul class="nav">
    <li><h1><?= __('registration') ?></h1></li>
</ul>
<div class="form-container">
    <div class="form-block">
        <?= Functions::displayUser(Vars::$USER_DATA, array('iphide' => 1,)) ?>
    </div>
    <div class="form-block">
        <span class="info-message"><?= __('thanks_for_registration') ?>!</span>
        <?php if (Vars::$USER_SYS['reg_moderation']): ?>
        <br/><br/><span class="description"><?= __('moderation_warning') ?></span>
        <?php endif ?>
        <br/><br/>
        <table>
            <tr>
                <td align="right" valign="top"><?= __('password') ?>:</td>
                <td>
                    <?php if (isset($_GET['pass'])): ?>
                    <strong><?= htmlspecialchars($_SESSION['password']) ?></strong>
                    <?php else: ?>
                    <a href="<?= Vars::$URI ?>?pass<?= (isset($_GET['auto']) ? '&amp;auto' : '') ?>"><?= __('show') ?></a>
                    <?php endif ?>
                </td>
            </tr>
            <tr>
                <td><?= __('autologin') ?>:</td>
                <td>
                    <?php if (isset($_GET['auto'])): ?>
                    <input type="text" value="<?= Vars::$HOME_URL . '/login?id=' . $_SESSION['uid'] . '&amp;token=' . htmlspecialchars($_SESSION['token']) ?>"/>
                    <?php else: ?>
                    <a href="<?= Vars::$URI ?>?auto<?= (isset($_GET['pass']) ? '&amp;pass' : '') ?>"><?= __('show') ?></a>
                    <?php endif ?>
                </td>
            </tr>
        </table>

        <br/><a class="btn btn-primary btn-large" href="<?= Vars::$HOME_URL ?>"><?= __('enter_on_site') ?></a>
    </div>
</div>