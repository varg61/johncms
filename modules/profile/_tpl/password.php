<div class="phdr">
    <a href="<?= Vars::$MODULE_URI ?>?user=<?= $this->user['id'] ?>"><b><?= ($this->user['id'] != Vars::$USER_ID ? lng('user_profile') : lng('my_profile')) ?></b></a> | <?= lng('change_password') ?>
</div>
<?php if (isset($this->error)) : ?>
<div class="rmenu" style="padding-top: 8px; padding-bottom: 10px">
    <?= lng('errors_occurred') ?>
</div>
<?php endif; ?>
<div class="user">
    <p><?= Functions::displayUser($this->user, array('iphide' => 1,)) ?></p>
</div>
<form action="<?= Vars::$URI ?>?act=password&amp;mod=change&amp;user=<?= $this->user['id'] ?>" method="post">
    <div class="menu">
        <div class="formblock">
            <label><?= ($this->user['id'] == Vars::$USER_ID ? lng('old_password') : lng('your_password')) ?></label><br/>
            <?php if(isset($this->error['oldpass'])) : ?>
            <small class="red"><b><?= lng('error') ?></b>: <?= $this->error['oldpass'] ?><br/></small>
            <?php endif; ?>
            <input type="password" name="oldpass" <?= (isset($this->error['oldpass']) ? 'class="error"' : '') ?>/>
        </div>
        <div class="formblock">
            <label><?= lng('new_password') ?></label><br/>
            <?php if(isset($this->error['newpass'])) : ?>
            <small class="red"><b><?= lng('error') ?></b>: <?= $this->error['newpass'] ?><br/></small>
            <?php endif; ?>
            <input type="password" name="newpass" <?= (isset($this->error['newpass']) ? 'class="error"' : '') ?>/><br/>
            <?= lng('repeat_password') ?><br/>
            <?php if(isset($this->error['newconf'])) : ?>
            <small class="red"><b><?= lng('error') ?></b>: <?= $this->error['newconf'] ?><br/></small>
            <?php endif; ?>
            <input type="password" name="newconf" <?= (isset($this->error['newconf']) ? 'class="error"' : '') ?>/>
            <div class="desc">
                <?= lng('password_change_help') ?>
            </div>
        </div>
        <div class="formblock">
            <input type="submit" value="<?= lng('save') ?>" name="submit"/>
        </div>
    </div>
    <input type="hidden" name="form_token" value="<?= $this->form_token ?>"/>
</form>
<div class="phdr">
    <a href="<?= Vars::$HOME_URL ?>/cabinet"><?= lng('back') ?></a>
</div>