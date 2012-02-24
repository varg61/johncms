<?php $error_style = 'style="background-color: #FFCCCC"'; ?>
<div class="phdr">
    <b><?= Vars::$LNG['login'] ?></b>
</div>
<form action="<?= Vars::$URI ?>" method="post">
    <div class="gmenu">
        <div class="formblock">
            <label for="login"><?= Vars::$LNG['login_caption'] ?></label><br/>
            <?php if (isset($this->login->error['login'])) : ?>
            <small class="red"><b><?= Vars::$LNG['error'] ?></b>: <?= $this->login->error['login'] ?><br/></small>
            <?php endif ?>
            <input id="login" type="text" name="login" value="<?= (isset($_POST['login']) ? htmlspecialchars(trim($_POST['login'])) : '') ?>" maxlength="20" <?= (isset($this->login->error['login']) ? $error_style : '') ?>/>
        </div>
        <div class="formblock">
            <label for="password"><?= Vars::$LNG['password'] ?></label><br/>
            <?php if (isset($this->login->error['password'])) : ?>
            <small class="red"><b><?= Vars::$LNG['error'] ?></b>: <?= $this->login->error['password'] ?><br/></small>
            <?php endif ?>
            <input id="password" type="password" name="password" maxlength="20" <?= (isset($this->login->error['password']) ? $error_style : '') ?>/>
        </div>
        <div class="formblock">
            <input type="checkbox" name="remember" value="1" checked="checked"/><?= Vars::$LNG['remember'] ?>
        </div>
        <div class="formblock">
            <input type="submit" value="<?= Vars::$LNG['login'] ?>"/>
        </div>
    </div>
</form>
<div class="phdr"><a href=""><?= Vars::$LNG['forgotten_password'] ?></a></div>