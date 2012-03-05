<div class="phdr">
    <a href="<?= Vars::$URI ?>?user=<?= $this->user['id'] ?>"><b><?= ($this->user['id'] != Vars::$USER_ID ? lng('profile') : lng('my_profile')) ?></b></a> | <?= lng('edit') ?>
</div>
<form action="<?= Vars::$URI ?>?user=<?= $this->user['id'] ?>" method="post">
    <div class="gmenu">
        <div class="formblock">
            <label for="nickname"><?= lng('nick') ?></label><br/>
            <input id="nickname" type="text" value="<?= $this->user['nickname'] ?>" name="nickname"/>
            <div class="desc"><?= lng('nick_lenght') ?></div>
        </div>
        <div class="formblock">
            <input type="submit" value="<?= lng('save') ?>" name="submit"/>
        </div>
    </div>
</form>
<div class="phdr">
    <a href="<?= Vars::$URI ?>?user=<?= $this->user['id'] ?>"><?= lng('back') ?></a>
</div>