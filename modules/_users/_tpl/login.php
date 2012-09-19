<ul class="nav">
    <li><h1><?= lng('login') ?></h1></li>
</ul>
<div class="form-container">
    <div class="form-block align-center" style="padding: 20px">
        <form action="<?= Vars::$URI ?>" method="post">
            <label for="login"><?= lng('login_caption') ?></label><br/>
            <?php if (isset($this->error['login'])) : ?>
            <span class="label label-red"><?= $this->error['login'] ?></span><br/>
            <?php endif ?>
            <input id="login" type="text" name="login" value="<?= (isset($_POST['login']) ? htmlspecialchars(trim($_POST['login'])) : '') ?>" maxlength="20" <?= (isset($this->error['login']) ? 'class="error"' : '') ?>/><br/>

            <label for="password"><?= lng('password') ?></label><br/>
            <?php if (isset($this->error['password'])) : ?>
            <span class="label label-red"><?= $this->error['password'] ?></span><br/>
            <?php endif; ?>
            <input id="password" type="password" name="password" maxlength="20" <?= (isset($this->error['password']) ? 'class="error"' : '') ?>/>
            <br/><br/>
            <input type="checkbox" name="remember" value="1" checked="checked"/>&#160;<?= lng('remember') ?><br/><br/>

            <input class="btn btn-primary btn-large btn-login" type="submit" value=" <?= lng('login') ?> "/>
            <br/><br/>
            <a class="btn btn-large btn-login" href="<?= Vars::$MODULE_URI ?>/registration"><?= lng('registration') ?></a>
            <br/><br/>
            <a class="btn" href="#"><?= lng('forgotten_password') ?></a>
        </form>
    </div>
</div>
