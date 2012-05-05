<div class="phdr">
    <b><?= lng('registration') ?></b>
</div>
<?php if (isset($_POST['submit']) && !empty($this->error)) : ?>
<div class="rmenu"><p><?= lng('errors_occurred') ?></p></div>
<?php endif; ?>
<form action="<?= Vars::$URI ?>" method="post">
    <div class="gmenu">
        <div class="formblock">
            <label for="login"><?= lng('nickname') ?></label><br/>
            <?php if (isset($this->error['login'])) : ?>
            <small class="red"><b><?= lng('error') ?></b>: <?= $this->error['login'] ?><br/></small>
            <?php endif; ?>
            <?php if (isset($_POST['check_login']) && empty($this->error)) : ?>
            <small><?= lng('nick_available') ?><br/></small>
            <?php endif; ?>
            <input id="login" type="text" name="login" maxlength="20" value="<?= htmlspecialchars($this->reg_data['login']) ?>" <?= (isset($this->error['login']) ? 'class="error"' : '') ?>/>
            <input type="submit" name="check_login" value="?"/>
            <div class="desc">
                <?= lng('login_help') ?>
            </div>
        </div>
        <div class="formblock">
            <label for="password"><?= lng('password') ?></label><br/>
            <?php if (isset($this->error['password'])) : ?>
            <small class="red"><b><?= lng('error') ?></b>: <?= $this->error['password'] ?><br/></small>
            <?php endif; ?>
            <input id="password" type="password" name="password" maxlength="20" value="<?= htmlspecialchars($this->reg_data['password']) ?>" <?= (isset($this->error['password']) ? 'class="error"' : '') ?>/>
            <br/>
            <small><?= lng('repeat_password') ?></small>
            <br/>
            <?php if (isset($this->error['password_confirm'])) : ?>
            <small class="red"><b><?= lng('error') ?></b>: <?= $this->error['password_confirm'] ?><br/></small>
            <?php endif; ?>
            <input type="password" name="password_confirm" maxlength="20" value="<?= htmlspecialchars($this->reg_data['password_confirm']) ?>" <?= (isset($this->error['password_confirm']) ? 'class="error"' : '') ?>/>
            <div class="desc">
                <?= lng('password_help') ?>
            </div>
        </div>
        <?php if (Vars::$USER_SYS['reg_email']) : ?>
        <div class="formblock">
            <label for="email">E-mail</label><br/>
            <?php if (isset(Validate::$error['email'])) : ?>
            <small class="red"><b><?= lng('error') ?></b>: <?= Validate::$error['email'] ?><br/></small>
            <?php endif; ?>
            <input id="email" type="text" name="email" maxlength="50" value="<?= htmlspecialchars($this->reg_data['email']) ?>" <?= (isset(Validate::$error['email']) ? 'class="error"' : '') ?>/>
        </div>
        <?php endif; ?>
        <div class="formblock">
            <label for="sex"><?= lng('sex') ?></label><br/>
            <?php if (isset($this->error['sex'])) : ?>
            <small class="red"><b><?= lng('error') ?></b>: <?= $this->error['sex'] ?><br/></small>
            <?php endif; ?>
            <input id="sex" type="radio" value="1" name="sex" <?= ($this->reg_data['sex'] == 1 ? 'checked="checked"' : '') ?>/>&#160;
            <?= Functions::getImage('usr_m.png', '', 'align="middle"') ?>&#160;<?= lng('sex_m') ?><br/>
            <input type="radio" value="2" name="sex" <?= ($this->reg_data['sex'] == 2 ? 'checked="checked"' : '') ?>/>&#160;
            <?= Functions::getImage('usr_w.png', '', 'align="middle"') ?>&#160;<?= lng('sex_w') ?>
        </div>
        <div class="formblock">
            <label for="captcha"><?= lng('captcha') ?></label><br/>
            <?= Captcha::display() ?><br/>
            <?php if (isset($this->error['captcha'])) : ?>
            <small class="red"><b><?= lng('error') ?></b>: <?= $this->error['captcha'] ?><br/></small>
            <?php endif; ?>
            <input id="captcha" type="text" size="5" maxlength="5" name="captcha" <?= (isset($this->error['captcha']) ? 'class="error"' : '') ?>/>
        </div>
        <div class="formblock">
            <input type="submit" name="submit" value="<?= lng('registration') ?>"/>
        </div>
    </div>
</form>
<div class="phdr">
    <a href="<?= Vars::$HOME_URL ?>/login"><?= lng('already_registered') ?>?</a>
</div>
<div class="topmenu"">
    <p><b><?= lng('mandatory_fields') ?></b></p>
</div>
<?php if (Vars::$USER_SYS['reg_moderation']) : ?>
<div class="topmenu">
    <span class="red"><p><?= lng('moderation_warning') ?></p></span>
</div>
<?php endif; ?>