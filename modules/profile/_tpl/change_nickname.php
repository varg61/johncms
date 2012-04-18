<div class="phdr">
    <a href="<?= Vars::$URI ?>?act=edit&amp;user=<?= $this->user['id'] ?>"><b><?= ($this->user['id'] != Vars::$USER_ID ? lng('profile') : lng('my_profile')) ?></b></a> | <?= lng('change_nickname') ?>
</div>
<?php if (isset($this->error)) : ?>
    <?= $this->error ?>
<?php endif; ?>
<form action="<?= Vars::$URI ?>?act=edit&amp;mod=nick&amp;user=<?= $this->user['id'] ?>" method="post">
    <div class="gmenu">
        <div class="formblock">
            <label for="nickname"><?= lng('nickname') ?></label><br/>
            <input id="nickname" type="text" value="<?= htmlspecialchars($this->nickname) ?>" name="nickname"/>
            <div class="desc">
                <?= lng('nick_lenght') ?>
            </div>
        </div>
        <div class="formblock">
            <span class="red"><?= lng('change_nickname_help') ?></span>
        </div>
        <div class="formblock">
            <input type="submit" value="<?= lng('save') ?>" name="submit"/>
        </div>
    </div>
    <input type="hidden" name="form_token" value="<?= $this->form_token ?>"/>
</form>
<div class="phdr">
    <a href="<?= Vars::$URI ?>?act=edit&amp;user=<?= $this->user['id'] ?>"><?= lng('back') ?></a>
</div>