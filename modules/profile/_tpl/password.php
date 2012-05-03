<div class="phdr">
    <b><?= lng('change_password') ?>:</b> <?= $this->user['nickname'] ?>
</div>
<form action="<?= Vars::$URI ?>?act=password&amp;mod=change&amp;user=<?= $this->user['id'] ?>" method="post">
    <div class="menu">
        <?php if ($this->user['id'] == Vars::$USER_ID) : ?>
        <div class="formblock">
            <label><?= lng('old_password') ?></label><br/>
            <input type="password" name="oldpass"/>
        </div>
        <?php endif; ?>
        <div class="formblock">
            <label><?= lng('new_password') ?></label><br/>
            <input type="password" name="newpass"/><br/>
            <?= lng('repeat_password') ?>:<br/>
            <input type="password" name="newconf"/>
        </div>
        <div class="formblock">
            <input type="submit" value="<?= lng('save') ?>" name="submit"/>
        </div>
    </div>
</form>
<div class="phdr">
    <small><?= lng('password_change_help') ?></small>
</div>
<p><a href="<?= Vars::$URI . '?user=' . $this->user['id'] ?>"><?= lng('profile') ?></a></p>