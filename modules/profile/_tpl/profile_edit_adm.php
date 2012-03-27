<div class="phdr">
    <a href="<?= Vars::$MODULE_URI ?>?user=<?= $this->user['id'] ?>"><b><?= ($this->user['id'] != Vars::$USER_ID ? lng('profile') : lng('my_profile')) ?></b></a> |
    <?= lng('edit') ?>
</div>
<div class="topmenu"><a href="<?= Vars::$URI ?>?user=<?= $this->user['id'] ?>"><?= lng('user') ?></a> | <b><?= lng('administration') ?></b></div>
<div class="user">
    <p><?= Functions::displayUser($this->user, $this->userarg) ?></p>
</div>
<form action="<?= Vars::$URI ?>?act=administration&amp;user=<?= $this->user['id'] ?>" method="post">
    <div class="rmenu">
        <?php if (!Vars::$USER_SYS['change_sex']) : ?>
        <div class="formblock">
            <label for="sex"><?= lng('sex') ?></label><br/>
            <input id="sex" type="radio" value="m" name="sex" <?= ($this->user['sex'] == 'm' ? 'checked="checked"' : '') ?>/>&#160;<?= lng('sex_m') ?><br/>
            <input type="radio" value="w" name="sex" <?= ($this->user['sex'] == 'w' ? 'checked="checked"' : '') ?>/>&#160;<?= lng('sex_w') ?>
        </div>
        <?php endif; ?>
        <div class="formblock">
            <label><?= lng('rank') ?></label><br/>
            <input type="radio" value="0" name="rights" <?= (!$this->user['rights'] ? 'checked="checked"' : '') ?>/>&#160;<b><?= lng('rank_0') ?></b><br/>
            <input type="radio" value="3" name="rights" <?= ($this->user['rights'] == 3 ? 'checked="checked"' : '') ?>/>&#160;<?= lng('rank_3') ?><br/>
            <input type="radio" value="4" name="rights" <?= ($this->user['rights'] == 4 ? 'checked="checked"' : '') ?>/>&#160;<?= lng('rank_4') ?><br/>
            <input type="radio" value="5" name="rights" <?= ($this->user['rights'] == 5 ? 'checked="checked"' : '') ?>/>&#160;<?= lng('rank_5') ?><br/>
            <input type="radio" value="6" name="rights" <?= ($this->user['rights'] == 6 ? 'checked="checked"' : '') ?>/>&#160;<?= lng('rank_6') ?>
            <?php if (Vars::$USER_RIGHTS == 9) : ?>
            <br/><input type="radio" value="7" name="rights" <?= ($this->user['rights'] == 7 ? 'checked="checked"' : '') ?>/>&#160;<?= lng('rank_7') ?>
            <br/><input type="radio" value="9" name="rights" <?= ($this->user['rights'] == 9 ? 'checked="checked"' : '') ?>/>&#160;<span class="red"><b><?= lng('rank_9') ?></b></span>
            <?php endif; ?>
        </div>
        <div class="formblock">
            <input type="submit" value="<?= lng('save') ?>" name="submit"/>
        </div>
    </div>
</form>
<div class="phdr">
    <a href="<?= Vars::$MODULE_URI ?>?user=<?= $this->user['id'] ?>"><?= lng('back') ?></a>
</div>