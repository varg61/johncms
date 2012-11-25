<ul class="nav">
    <li><h1<?= $this->user['id'] == Vars::$USER_ID ? ' class="section-personal"' : '' ?>><?= __('change_nickname') ?></h1></li>
</ul>
<div class="form-container">
    <div class="form-block">
        <?= Functions::displayUser($this->user, array('iphide' => 1,)) ?>
    </div>
    <div class="form-block">
        <form action="<?= Vars::$URI ?>?act=edit&amp;mod=nickname&amp;user=<?= $this->user['id'] ?>" method="post">
            <label><?= __('attention') ?></label><br/>
            <ul>
                <li><?= __('change_nickname_help_1') ?></li>
                <li><?= __('change_nickname_help_2') . ' ' . Vars::$USER_SYS['change_period'] . ' ' . __('days') ?></li>
            </ul>

            <label for="nickname"><?= __('nickname') ?></label><br/>
            <?php if (isset($this->error['login'])) : ?>
            <small class="red"><b><?= __('error') ?></b>: <?= $this->error['login'] ?><br/></small>
            <?php endif; ?>
            <?php if (isset($this->available)) : ?>
            <small><?= __('nick_available') ?><br/></small>
            <?php endif; ?>
            <input id="nickname" type="text" name="nickname" maxlength="20" value="<?= htmlspecialchars($this->nickname) ?>" <?= (isset($this->error['login']) ? 'class="error"' : '') ?>/>
            <input type="submit" name="check_login" value="?"/>
            <span class="input-help"><?= __('login_help') ?></span>
        </form>
    </div>
</div>

<?php if (!empty($this->error) && isset($_POST['submit'])) : ?>
<div class="rmenu" style="padding-top: 8px; padding-bottom: 10px">
    <?= __('errors_occurred') ?>
</div>
<?php endif; ?>

<div class="gmenu">
    <div class="formblock">
        <label for="password"><?= __('your_password') ?></label><br/>
        <?php if (isset($this->error['password'])) : ?>
        <small class="red"><b><?= __('error') ?></b>: <?= $this->error['password'] ?><br/></small>
        <?php endif; ?>
        <input id="password" type="password" name="password" <?= (isset($this->error['password']) ? 'class="error"' : '') ?>/>
    </div>
    <div class="formblock">
        <input type="submit" value="<?= __('save') ?>" name="submit"/>
    </div>
</div>
<input type="hidden" name="form_token" value="<?= $this->form_token ?>"/>