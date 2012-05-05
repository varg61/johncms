<div class="phdr">
    <a href="<?= Vars::$URI ?>?act=edit&amp;user=<?= $this->user['id'] ?>"><b><?= ($this->user['id'] != Vars::$USER_ID ? lng('profile') : lng('my_profile')) ?></b></a> | <?= lng('change_nickname') ?>
</div>
<?php if (!empty($this->error) && isset($_POST['submit'])) : ?>
<div class="rmenu" style="padding-top: 8px; padding-bottom: 10px">
    <?= lng('errors_occurred') ?>
</div>
<?php endif; ?>
<form action="<?= Vars::$URI ?>?act=edit&amp;mod=nickname&amp;user=<?= $this->user['id'] ?>" method="post">
    <div class="gmenu">
        <div class="formblock">
            <label class="red"><?= lng('attention') ?></label><br/>
            <ul>
                <li><?= lng('change_nickname_help_1') ?></li>
                <li><?= lng('change_nickname_help_2') . ' ' . Vars::$USER_SYS['change_period'] . ' ' . lng('days') ?></li>
            </ul>
        </div>
        <div class="formblock">
            <label for="nickname"><?= lng('nickname') ?></label><br/>
            <?php if (isset($this->error['login'])) : ?>
            <small class="red"><b><?= lng('error') ?></b>: <?= $this->error['login'] ?><br/></small>
            <?php endif; ?>
            <?php if (isset($this->available)) : ?>
            <small><?= lng('nick_available') ?><br/></small>
            <?php endif; ?>
            <input id="nickname" type="text" name="nickname" maxlength="20" value="<?= htmlspecialchars($this->nickname) ?>" <?= (isset($this->error['login']) ? 'class="error"' : '') ?>/>
            <input type="submit" name="check_login" value="?"/>
            <div class="desc">
                <?= lng('login_help') ?>
            </div>
        </div>
        <div class="formblock">
            <label for="password"><?= lng('your_password') ?></label><br/>
            <?php if (isset($this->error['password'])) : ?>
            <small class="red"><b><?= lng('error') ?></b>: <?= $this->error['password'] ?><br/></small>
            <?php endif; ?>
            <input id="password" type="password" name="password" <?= (isset($this->error['password']) ? 'class="error"' : '') ?>/>
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
            <input type="submit" value="<?= lng('save') ?>" name="submit"/>
        </div>
    </div>
    <input type="hidden" name="form_token" value="<?= $this->form_token ?>"/>
</form>
<div class="phdr">
    <a href="<?= Vars::$URI ?>?act=edit&amp;user=<?= $this->user['id'] ?>"><?= lng('back') ?></a>
</div>