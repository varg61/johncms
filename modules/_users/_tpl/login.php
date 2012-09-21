<ul class="nav">
    <li><h1><?= lng('login') ?></h1></li>
</ul>
<div class="form-container">
    <div class="form-block align-center" style="padding: 20px">
        <form action="<?= Vars::$URI ?>" method="post">
            <div style="max-width: 240px; margin: 0 auto">
                <label for="login"><?= lng('login_caption') ?></label><br/>
                <?php if (isset($this->error['login'])) : ?>
                <span class="label label-red"><?= $this->error['login'] ?></span><br/>
                <?php endif ?>
                <input class="relative largetext<?= (isset($this->error['login']) ? ' error' : '') ?>" id="login" type="text" name="login" value="<?= (isset($_POST['login']) ? htmlspecialchars(trim($_POST['login'])) : '') ?>" maxlength="20"/><br/>
                <label for="password"><?= lng('password') ?></label><br/>
                <?php if (isset($this->error['password'])) : ?>
                <span class="label label-red"><?= $this->error['password'] ?></span><br/>
                <?php endif ?>
                <input class="relative largetext<?= (isset($this->error['password']) ? ' error' : '') ?>" id="password" type="password" name="password" maxlength="20"/><br/>
                <label class="small"><input type="checkbox" name="remember" value="1" checked="checked"/>&#160;<?= lng('remember') ?></label><br/><br/>
                <input class="btn btn-primary btn-large btn-block" type="submit" value=" <?= lng('login') ?> "/><br/>
                <a class="btn btn-large btn-block" href="<?= Vars::$MODULE_URI ?>/registration"><?= lng('registration') ?></a><br/>
                <a class="btn" href="#"><?= lng('forgotten_password') ?></a>
            </div>
        </form>
    </div>
</div>
