<div class="phdr">
    <a href="<?= Vars::$URI ?>?user=<?= $this->user['id'] ?>"><b><?= ($this->user['id'] != Vars::$USER_ID ? Vars::$LNG['profile'] : $this->lng['my_profile']) ?></b></a> | <?= Vars::$LNG['edit'] ?>
</div>
<form action="<?= Vars::$URI ?>?user=<?= $this->user['id'] ?>" method="post">
    <div class="gmenu">
        <div class="formblock">
            <label for="nickname"><?= Vars::$LNG['nick'] ?></label><br/>
            <input id="nickname" type="text" value="<?= $this->user['nickname'] ?>" name="nickname"/>
            <div class="desc"><?= $this->lng['nick_lenght'] ?></div>
        </div>
        <div class="formblock">
            <input type="submit" value="<?= Vars::$LNG['save'] ?>" name="submit"/>
        </div>
    </div>
</form>
<div class="phdr">
    <a href="<?= Vars::$URI ?>?user=<?= $this->user['id'] ?>"><?= Vars::$LNG['back'] ?></a>
</div>