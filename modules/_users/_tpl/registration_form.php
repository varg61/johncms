<ul class="nav">
    <li><h1><?= lng('registration') ?></h1></li>
</ul>
<div class="form-container">
    <?php if (isset($_POST['submit']) && !empty($this->error)) : ?>
    <div class="form-block error">
        <span class="input-help error"><b><?= lng('errors_occurred') ?></b></span>
    </div>
    <?php endif ?>
    <form action="<?= Vars::$URI ?>" method="post">
        <div class="form-block">
            <label for="reg_login"><?= lng('nickname') ?></label><br/>
            <?php if (isset($this->error['login'])): ?>
            <span class="label label-red"><?= $this->error['login'] ?></span><br/>
            <?php endif ?>
            <?php if (isset($_POST['check_login']) && empty($this->error)): ?>
            <span class="label label-green"><?= lng('nick_available') ?></span><br/>
            <?php endif ?>
            <input id="reg_login" type="text" name="reg_login" maxlength="20" value="<?= htmlspecialchars($this->reg_data['login']) ?>" <?= (isset($this->error['login']) ? 'class="error"' : '') ?>/>
            <input class="btn" type="submit" name="check_login" value="?" style="margin-bottom: 3px;"/>
            <span class="input-help"><?= lng('login_help') ?></span><br/>

            <label for="reg_password"><?= lng('password') ?></label><br/>
            <?php if (isset($this->error['password'])) : ?>
            <span class="label label-red"><?= $this->error['password'] ?></span><br/>
            <?php endif ?>
            <input id="reg_password" type="password" name="reg_password" maxlength="20" value="<?= htmlspecialchars($this->reg_data['password']) ?>" <?= (isset($this->error['password']) ? 'class="error"' : '') ?>/><br/>
            <label for="password_confirm"><?= lng('repeat_password') ?></label><br/>
            <?php if (isset($this->error['password_confirm'])) : ?>
            <span class="label label-red"><?= $this->error['password_confirm'] ?></span><br/>
            <?php endif ?>
            <input id="password_confirm" type="password" name="password_confirm" maxlength="20" value="<?= htmlspecialchars($this->reg_data['password_confirm']) ?>" <?= (isset($this->error['password_confirm']) ? 'class="error"' : '') ?>/>
            <br/>
            <span class="input-help"><?= lng('password_help') ?></span><br/>

            <label for="sex"><?= lng('sex') ?></label><br/>
            <?php if (isset($this->error['sex'])) : ?>
            <span class="label label-red"><?= $this->error['sex'] ?></span><br/>
            <?php endif ?>
            <label class="small"><input id="sex" type="radio" value="1" name="sex" <?= ($this->reg_data['sex'] == 1 ? 'checked="checked"' : '') ?>/>&#160;<?= Functions::loadImage('user.png') ?>&#160;<?= lng('sex_m') ?></label><br/>
            <label class="small"><input type="radio" value="2" name="sex" <?= ($this->reg_data['sex'] == 2 ? 'checked="checked"' : '') ?>/>&#160;<?= Functions::loadImage('user-female.png') ?>&#160;<?= lng('sex_w') ?></label>

            <?php if (Vars::$USER_SYS['reg_email']) : ?>
            <label for="email">E-mail</label><br/>
            <?php if (isset(Validate::$error['email'])): ?>
                <span class="label label-red"><?= Validate::$error['email'] ?></span><br/>
                <?php endif ?>
            <input id="email" type="text" name="email" maxlength="50" value="<?= htmlspecialchars($this->reg_data['email']) ?>" <?= (isset(Validate::$error['email']) ? 'class="error"' : '') ?>/>
            <?php endif ?>
        </div>
        <div class="form-block">
            <?php if (Vars::$USER_SYS['reg_moderation']): ?>
            <span class="input-help error"><?= lng('moderation_warning') ?></span>
            <?php endif ?>
            <label for="captcha"><?= lng('captcha') ?></label><br/>
            <?= Captcha::display() ?><br/>
            <?php if (isset($this->error['captcha'])): ?>
            <span class="label label-red"><?= $this->error['captcha'] ?></span><br/>
            <?php endif ?>
            <input class="medium" id="captcha" type="text" size="5" maxlength="5" name="captcha" <?= (isset($this->error['captcha']) ? 'class="error"' : '') ?>/>
            <br/><br/><input class="btn btn-primary btn-large" type="submit" name="submit" value="<?= lng('registration') ?>"/>
            <a class="btn" href="<?= Vars::$MODULE_URI ?>/login"><?= lng('cancel') ?></a>
        </div>
    </form>
</div>