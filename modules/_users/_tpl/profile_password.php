<ul class="nav">
    <li><h1<?= ($this->user['id'] == Vars::$USER_ID ? ' class="section"' : '') ?>><?= lng('change_password') ?></h1></li>
</ul>

<?php if (isset($this->error)) : ?>
<div class="rmenu" style="padding-top: 8px; padding-bottom: 10px">
    <?= lng('errors_occurred') ?>
</div>
<?php endif; ?>

<div class="form-container">
    <div class="form-block">
        <?= Functions::displayUser($this->user, array('iphide' => 1,)) ?>
    </div>
    <form action="<?= Vars::$URI ?>?act=password&amp;mod=change&amp;user=<?= $this->user['id'] ?>" method="post">
        <div class="form-block">
            <label for="oldpass"><?= ($this->user['id'] == Vars::$USER_ID ? lng('old_password') : lng('your_password')) ?> <span class="attn">*</span></label><br/>
            <?php if (isset($this->error['oldpass'])) : ?>
            <small class="red"><b><?= lng('error') ?></b>: <?= $this->error['oldpass'] ?><br/></small>
            <?php endif; ?>
            <input id="oldpass" name="oldpass" type="password" <?= (isset($this->error['oldpass']) ? 'class="error"' : '') ?>/><br/><br/>

            <label for="newpass"><?= lng('new_password') ?> <span class="attn">*</span></label><br/>
            <?php if (isset($this->error['newpass'])) : ?>
            <small class="red"><b><?= lng('error') ?></b>: <?= $this->error['newpass'] ?><br/></small>
            <?php endif; ?>
            <input id="newpass" name="newpass" type="password" <?= (isset($this->error['newpass']) ? 'class="error"' : '') ?>/><br/>

            <label for="newconf"><?= lng('repeat_password') ?> <span class="attn">*</span></label><br/>
            <?php if (isset($this->error['newconf'])) : ?>
            <small class="red"><b><?= lng('error') ?></b>: <?= $this->error['newconf'] ?><br/></small>
            <?php endif; ?>
            <input id="newconf" name="newconf" type="password" <?= (isset($this->error['newconf']) ? 'class="error"' : '') ?>/><br/>
            <span class="input-help"><?= lng('password_change_help') ?></span><br/><br/>

            <input class="btn btn-primary btn-large" type="submit" value="<?= lng('save') ?>" name="submit"/>
            <a class="btn btn-large" href="<?= Vars::$MODULE_URI ?>/personal"><?= lng('cancel') ?></a>
            <input type="hidden" name="form_token" value="<?= $this->form_token ?>"/>
        </div>
    </form>
</div>