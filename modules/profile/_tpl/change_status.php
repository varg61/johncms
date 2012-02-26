<div class="phdr">
    <a href="<?= Vars::$MODULE_URI ?>?user=<?= $this->user['id'] ?>"><b><?= ($this->user['id'] != Vars::$USER_ID ? Vars::$LNG['profile'] : $this->lng['my_profile']) ?></b></a> | <?= Vars::$LNG['edit'] ?>
</div>
<form action="<?= Vars::$URI ?>?user=<?= $this->user['id'] ?>" method="post">
    <div class="gmenu">
        <div class="formblock">
            <label for="status"><?= Vars::$LNG['status'] ?></label><br/>
            <input id="status" type="text" value="<?= $this->user['status'] ?>" name="status"/>
            <div class="desc"><?= $this->lng['status_lenght'] ?></div>
        </div>
        <div class="formblock">
            <input type="submit" value="<?= Vars::$LNG['save'] ?>" name="submit"/>
        </div>
    </div>
</form>
<div class="phdr">
    <a href="<?= Vars::$URI ?>?user=<?= $this->user['id'] ?>"><?= Vars::$LNG['back'] ?></a>
</div>