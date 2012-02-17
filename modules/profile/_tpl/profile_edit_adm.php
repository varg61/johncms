<div class="phdr">
    <a href="<?= Vars::$MODULE_URI ?>?user=<?= $this->user['id'] ?>"><b><?= ($this->user['id'] != Vars::$USER_ID ? Vars::$LNG['profile'] : $this->lng['my_profile']) ?></b></a> |
    <?= Vars::$LNG['edit'] ?>
</div>
<div class="topmenu"><a href="<?= Vars::$URI ?>?user=<?= $this->user['id'] ?>"><?= $this->lng['user'] ?></a> | <b><?= $this->lng['administration'] ?></b></div>
<div class="user">
    <p><?= Functions::displayUser($this->user, $this->userarg) ?></p>
</div>
<form action="<?= Vars::$URI ?>?act=administration&amp;user=<?= $this->user['id'] ?>" method="post">
    <div class="gmenu">
        <div class="formblock">
            <label for="sex"><?= $this->lng['sex'] ?></label><br/>
            <input id="sex" type="radio" value="m" name="sex" <?= ($this->user['sex'] == 'm' ? 'checked="checked"' : '') ?>/>&#160;<?= $this->lng['sex_m'] ?><br/>
            <input type="radio" value="w" name="sex" <?= ($this->user['sex'] == 'w' ? 'checked="checked"' : '') ?>/>&#160;<?= $this->lng['sex_w'] ?>
        </div>
    </div>
    <div class="rmenu">
        <div class="formblock">
            <label><?= $this->lng['rank'] ?></label><br/>
            <input type="radio" value="0" name="rights" <?= (!$this->user['rights'] ? 'checked="checked"' : '') ?>/>&#160;<b><?= $this->lng['rank_0'] ?></b><br/>
            <input type="radio" value="3" name="rights" <?= ($this->user['rights'] == 3 ? 'checked="checked"' : '') ?>/>&#160;<?= $this->lng['rank_3'] ?><br/>
            <input type="radio" value="4" name="rights" <?= ($this->user['rights'] == 4 ? 'checked="checked"' : '') ?>/>&#160;<?= $this->lng['rank_4'] ?><br/>
            <input type="radio" value="5" name="rights" <?= ($this->user['rights'] == 5 ? 'checked="checked"' : '') ?>/>&#160;<?= $this->lng['rank_5'] ?><br/>
            <input type="radio" value="6" name="rights" <?= ($this->user['rights'] == 6 ? 'checked="checked"' : '') ?>/>&#160;<?= $this->lng['rank_6'] ?>
            <?php if (Vars::$USER_RIGHTS == 9) : ?>
            <br/><input type="radio" value="7" name="rights" <?= ($this->user['rights'] == 7 ? 'checked="checked"' : '') ?>/>&#160;<?= $this->lng['rank_7'] ?>
            <br/><input type="radio" value="9" name="rights" <?= ($this->user['rights'] == 9 ? 'checked="checked"' : '') ?>/>&#160;<span class="red"><b><?= $this->lng['rank_9'] ?></b></span>
            <?php endif ?>
        </div>
        <div class="formblock">
            <input type="submit" value="<?= Vars::$LNG['save'] ?>" name="submit"/>
        </div>
    </div>
</form>
<div class="phdr">
    <a href="<?= Vars::$MODULE_URI ?>?user=<?= $this->user['id'] ?>"><?= Vars::$LNG['back'] ?></a>
</div>