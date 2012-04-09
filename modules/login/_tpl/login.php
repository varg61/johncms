<div class="phdr">
    <b><?= lng('login') ?></b>
</div>
<form action="<?= Vars::$URI ?>" method="post">
    <div class="gmenu">
        <div class="formblock">
            <label for="login"><?= lng('login_caption') ?></label><br/>
            <?php if (isset($this->error['login'])) : ?>
            <small class="red"><b><?= lng('error') ?></b>: <?= $this->error['login'] ?><br/></small>
            <?php endif; ?>
            <input id="login" type="text" name="login" value="<?= (isset($_POST['login']) ? htmlspecialchars(trim($_POST['login'])) : '') ?>" maxlength="20" <?= (isset($this->error['login']) ? 'class="error"' : '') ?>/>
        </div>
        <div class="formblock">
            <label for="password"><?= lng('password') ?></label><br/>
            <?php if (isset($this->error['password'])) : ?>
            <small class="red"><b><?= lng('error') ?></b>: <?= $this->error['password'] ?><br/></small>
            <?php endif; ?>
            <input id="password" type="password" name="password" maxlength="20" <?= (isset($this->error['password']) ? 'class="error"' : '') ?>/>
        </div>
        <div class="formblock">
            <input type="checkbox" name="remember" value="1" checked="checked"/><?= lng('remember') ?>
        </div>
        <div class="formblock">
            <input type="submit" value="<?= lng('login') ?>"/>
        </div>
    </div>
</form>
<div class="phdr"><a href=""><?= lng('forgotten_password') ?></a></div>