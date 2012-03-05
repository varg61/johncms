<?php $error_style = 'style="background-color: #FFCCCC"'; ?>
<?php if (isset($_POST['submit']) && !empty($this->login->error)) : ?>
<div class="rmenu"><p><?= lng('errors_occurred') ?></p></div>
<?php endif ?>
<form action="<?= Vars::$URI ?>" method="post">
    <div class="gmenu">
        <div class="formblock">
            <label for="login"><?= lng('nickname') ?></label><br/>
            <?php if (isset(Validate::$error['login'])) : ?>
            <small class="red"><b><?= lng('error') ?></b>: <?= Validate::$error['login'] ?><br/></small>
            <?php endif ?>
            <?php if (isset($_POST['check_login']) && empty(Validate::$error)) : ?>
            <small><?= lng('nick_available') ?><br/></small>
            <?php endif ?>
            <input id="login" type="text" name="login" maxlength="20" value="<?= htmlspecialchars($this->reg_data['login']) ?>" <?= (isset(Validate::$error['login']) ? $error_style : '') ?>/>
            <input type="submit" name="check_login" value="?"/>
        </div>
        <div class="formblock">
            <label for="password"><?= lng('password') ?></label><br/>
            <?php if (isset(Validate::$error['password'])) : ?>
            <small class="red"><b><?= lng('error') ?></b>: <?= Validate::$error['password'] ?><br/></small>
            <?php endif ?>
            <input id="password" type="password" name="password" maxlength="20" value="<?= htmlspecialchars($this->reg_data['password']) ?>" <?= (isset(Validate::$error['password']) ? $error_style : '') ?>/><br/>
            <small><?= lng('repeat_password') ?></small>
            <br/>
            <?php if (isset($this->login->error['password_confirm'])) : ?>
            <small class="red"><b><?= lng('error') ?></b>: <?= $this->login->error['password_confirm'] ?><br/></small>
            <?php endif ?>
            <input type="password" name="password_confirm" maxlength="20" value="<?= htmlspecialchars($this->reg_data['password_confirm']) ?>" <?= (isset($this->login->error['password_confirm']) ? $error_style : '') ?>/>
        </div>
        <div class="formblock">
            <label for="email">E-mail</label><br/>
            <?php if (isset(Validate::$error['email'])) : ?>
            <small class="red"><b><?= lng('error') ?></b>: <?= Validate::$error['email'] ?><br/></small>
            <?php endif ?>
            <input id="email" type="text" name="email" maxlength="50" value="<?= htmlspecialchars($this->reg_data['email']) ?>" <?= (isset(Validate::$error['email']) ? $error_style : '') ?>/>
        </div>
        <div class="formblock">
            <label for="sex"><?= lng('sex') ?></label><br/>
            <?php if (isset($this->login->error['sex'])) : ?>
            <small class="red"><b><?= lng('error') ?></b>: <?= $this->login->error['sex'] ?><br/></small>
            <?php endif ?>
            <input id="sex" type="radio" value="1" name="sex" <?= ($this->reg_data['sex'] == 1 ? 'checked="checked"' : '') ?>/>&#160;
            <?= Functions::getImage('usr_m.png', '', 'align="middle"') ?>&#160;<?= lng('sex_m') ?><br/>
            <input type="radio" value="2" name="sex" <?= ($this->reg_data['sex'] == 2 ? 'checked="checked"' : '') ?>/>&#160;
            <?= Functions::getImage('usr_w.png', '', 'align="middle"') ?>&#160;<?= lng('sex_w') ?>
        </div>
        <div class="formblock">
            <label for="captcha"><?= lng('captcha') ?></label><br/>
            <?= Captcha::display(0) ?><br/>
            <?php if (isset($this->login->error['captcha'])) : ?>
            <small class="red"><b><?= lng('error') ?></b>: <?= $this->login->error['captcha'] ?><br/></small>
            <?php endif ?>
            <input id="captcha" type="text" size="5" maxlength="5" name="captcha" <?= (isset($this->login->error['captcha']) ? $error_style : '') ?>/>
        </div>
        <div class="formblock">
            <input type="submit" name="submit" value="<?= lng('do_registration') ?>"/>
        </div>
    </div>
</form>
<div class="phdr" style="font-size: x-small;">
    <p><b><?= lng('mandatory_fields') ?></b></p>
    <p><b class="green"><?= mb_strtoupper(lng('login')) ?></b>: <?= lng('login_help') ?></p>
    <p><b class="green"><?= mb_strtoupper(lng('password')) ?></b>: <?= lng('password_help') ?></p>
</div>
<?php if (Vars::$SYSTEM_SET['mod_reg'] == 1) : ?>
<div class="topmenu">
    <small class="red"><p><?= lng('moderation_warning') ?></p></small>
</div>
<?php endif ?>