<ul class="nav">
    <li><h1<?= ($this->user['id'] == Vars::$USER_ID ? ' class="section-personal"' : '') ?>><?= lng('change_password') ?></h1></li>
</ul>
<div class="form-container">
    <div class="form-block">
        <?= Functions::displayUser($this->user, array('iphide' => 1,)) ?>
    </div>
    <form action="<?= Vars::$URI ?>?act=password&amp;mod=change&amp;user=<?= $this->user['id'] ?>" method="post">
        <div class="form-block">
            <label for="oldpass"><?= ($this->user['id'] == Vars::$USER_ID ? lng('old_password') : lng('your_password')) ?> <span class="attn">*</span></label><br/>
            <?php if (isset($this->error['oldpass'])) : ?>
            <span class="label label-red"><?= $this->error['oldpass'] ?></span><br/>
            <?php endif ?>
            <input id="oldpass" name="oldpass" type="password" <?= (isset($this->error['oldpass']) ? 'class="error"' : '') ?>/>
        </div>
        <div class="form-block">
            <label for="newpass"><?= lng('new_password') ?> <span class="attn">*</span></label><br/>
            <?php if (isset($this->error['newpass'])) : ?>
            <span class="label label-red"><?= $this->error['newpass'] ?></span><br/>
            <?php endif ?>
            <input id="newpass" name="newpass" type="password" <?= (isset($this->error['newpass']) ? 'class="error"' : '') ?>/><br/>

            <label for="newconf"><?= lng('repeat_password') ?> <span class="attn">*</span></label><br/>
            <?php if (isset($this->error['newconf'])) : ?>
            <span class="label label-red"><?= $this->error['newconf'] ?></span><br/>
            <?php endif ?>
            <input id="newconf" name="newconf" type="password" <?= (isset($this->error['newconf']) ? 'class="error"' : '') ?>/><br/>
            <span class="input-help"><?= lng('password_change_help') ?></span><br/><br/>

            <input class="btn btn-primary btn-large" type="submit" value="<?= lng('save') ?>" name="submit"/>
            <a class="btn" href="<?= Vars::$MODULE_URI ?>/settings&amp;user=<?= $this->user['id'] ?>"><?= lng('cancel') ?></a>
            <input type="hidden" name="form_token" value="<?= $this->form_token ?>"/>
        </div>
    </form>
</div>