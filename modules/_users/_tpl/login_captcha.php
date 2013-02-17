<ul class="title">
    <li class="center"><h1><?= __('login') ?></h1></li>
</ul>
<div class="content form-container">
    <div style="text-align: center">
        <form action="<?= Router::getUri(3) ?>" method="post">
            <fieldset>
                <legend><?= __('captcha') ?></legend><br/>
                <?= Captcha::display() ?><br/>
                <?php if (isset($this->error)) : ?>
                <span class="error-text"><?= $this->error ?><br/></span>
                <?php endif; ?>
                <input id="captcha" type="text" style="width: 100px; text-align: center" maxlength="5" name="captcha" <?= (isset($this->error['captcha']) ? 'class="error"' : '') ?>/>
            </fieldset>
            <fieldset>
                <input type="submit" name="submit" class="btn btn-primary btn-large" value="<?= __('continue') ?>"/>
                <input type="hidden" name="login" value="<?= htmlspecialchars($this->data['login']) ?>"/>
                <input type="hidden" name="password" value="<?= htmlspecialchars($this->data['password']) ?>"/>
                <?php if (isset($this->data['remember']) && $this->data['remember']) : ?>
                <input type="hidden" name="remember" value="1"/>
                <?php endif ?>
                <input type="hidden" name="form_token" value="<?= $this->form_token ?>"/>
            </fieldset>
        </form>
    </div>
</div>
