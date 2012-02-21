<div class="phdr">
    <b><?= Vars::$LNG['login'] ?></b>
</div>
<form action="<?= Vars::$URI ?>" method="post">
    <div class="rmenu">
        <div class="formblock">
            <label for="captcha"><?= Vars::$LNG['captcha'] ?></label><br/>
            <?= Captcha::display(0) ?><br/>
            <?php if (isset($this->login->error['captcha'])) : ?>
            <small class="red"><b><?= Vars::$LNG['error'] ?></b>: <?= $this->login->error['captcha'] ?><br/></small>
            <?php endif ?>
            <input id="captcha" type="text" size="5" maxlength="5" name="captcha" <?= (isset($this->login->error['captcha']) ? 'style="background-color: #FFCCCC"' : '') ?>/>
        </div>
        <div class="formblock">
            <input type="submit" name="submit" value="<?= Vars::$LNG['continue'] ?>"/>
        </div>
    </div>
    <?php if (isset($_REQUEST['id']) && isset($_REQUEST['token'])) : ?>
    <input type="hidden" name="id" value="<?= intval($_REQUEST['id']) ?>"/>
    <input type="hidden" name="token" value="<?= htmlspecialchars($_REQUEST['token']) ?>"/>
    <?php else : ?>
    <input type="hidden" name="login" value="<?= htmlspecialchars($_POST['login']) ?>"/>
    <input type="hidden" name="password" value="<?= htmlspecialchars($_POST['password']) ?>"/>
    <input type="hidden" name="remember" value="<?= $_POST['remember'] ?>"/>
    <?php endif ?>
</form>