<div class="phdr">
    <a href="<?= Vars::$URI ?>?user=<?= $this->user['id'] ?>"><b><?= ($this->user['id'] != Vars::$USER_ID ? lng('profile') : lng('my_profile')) ?></b></a> | <?= lng('change_status') ?>
</div>
<form action="<?= Vars::$URI ?>?act=status&amp;user=<?= $this->user['id'] ?>" method="post">
    <div class="gmenu">
        <div class="formblock">
            <label for="status"><?= lng('status') ?></label><br/>
            <?php if(isset($this->error)) : ?>
            <span class="red"><?= $this->error ?></span><br/>
            <?php endif ?>
            <input id="status" type="text" value="<?= htmlspecialchars($this->status) ?>" name="status"/>
            <div class="desc"><?= lng('status_lenght') ?></div>
        </div>
        <div class="formblock">
            <input type="submit" value="<?= lng('save') ?>" name="submit"/>
        </div>
    </div>
</form>
<div class="phdr">
    <a href="<?= Vars::$URI ?>?user=<?= $this->user['id'] ?>"><?= lng('back') ?></a>
</div>