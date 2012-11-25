<div class="phdr">
    <b><?= __('login') ?></b>
</div>
<form action="<?= Vars::$URI . (isset($this->data['id']) && isset($this->data['token']) ? '?id=' . $this->data['id'] . '&amp;token=' . htmlspecialchars($this->data['token']) : '') ?>" method="post">
    <div class="menu">
        <div class="formblock">
            <label for="captcha"><?= __('captcha') ?></label><br/>
            <?= Captcha::display() ?><br/>
            <?php if (isset($this->error['captcha'])) : ?>
            <small class="red"><b><?= __('error') ?></b>: <?= $this->error['captcha'] ?><br/></small>
            <?php endif; ?>
            <input id="captcha" type="text" size="5" maxlength="5" name="captcha" <?= (isset($this->error['captcha']) ? 'class="error"' : '') ?>/>
        </div>
        <div class="formblock">
            <input type="submit" name="submit" value="<?= __('continue') ?>"/>
        </div>
    </div>
    <input type="hidden" name="login" value="<?= htmlspecialchars($this->data['login']) ?>"/>
    <input type="hidden" name="password" value="<?= htmlspecialchars($this->data['password']) ?>"/>
    <?php if (isset($_POST['remember'])) : ?>
    <input type="hidden" name="remember" value="1"/>
    <?php endif; ?>
    <input type="hidden" name="form_token" value="<?= $this->form_token ?>"/>
</form>