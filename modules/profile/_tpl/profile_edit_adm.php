<div class="phdr">
    <a href="<?= Vars::$MODULE_URI ?>?user=<?= $this->user['id'] ?>"><b><?= ($this->user['id'] != Vars::$USER_ID ? lng('profile') : lng('my_profile')) ?></b></a> |
    <?= lng('edit') ?>
</div>
<div class="topmenu">
    <a href="<?= Vars::$URI ?>?act=edit&amp;user=<?= $this->user['id'] ?>"><?= lng('user') ?></a> | <b><?= lng('administration') ?></b>
</div>
<?php if (!empty($this->error)) : ?>
<div class="rmenu" style="padding-top: 8px; padding-bottom: 10px">
    <?= lng('errors_occurred') ?>
</div>
<?php endif; ?>
<?php if (isset($this->save)) : ?>
<div class="gmenu" style="padding-top: 8px; padding-bottom: 10px">
    <?= lng('settings_saved') ?>
</div>
<?php endif; ?>
<div class="user">
    <p><?= Functions::displayUser($this->user, $this->userarg) ?></p>
</div>
<form action="<?= Vars::$URI ?>?act=edit&amp;mod=administration&amp;user=<?= $this->user['id'] ?>" method="post">
    <div class="gmenu">
        <div class="formblock">
            <label><?= lng('rank') ?></label><br/>
            <p>
                <input type="radio" value="0" name="rights" <?= (!$this->user['rights'] ? 'checked="checked"' : '') ?>/>&#160;<b><?= lng('rank_0') ?></b>
            </p>
            <p>
                <input type="radio" value="3" name="rights" <?= ($this->user['rights'] == 3 ? 'checked="checked"' : '') ?>/>&#160;<?= lng('rank_3') ?><br/>
                <input type="radio" value="4" name="rights" <?= ($this->user['rights'] == 4 ? 'checked="checked"' : '') ?>/>&#160;<?= lng('rank_4') ?><br/>
                <input type="radio" value="5" name="rights" <?= ($this->user['rights'] == 5 ? 'checked="checked"' : '') ?>/>&#160;<?= lng('rank_5') ?><br/>
                <input type="radio" value="6" name="rights" <?= ($this->user['rights'] == 6 ? 'checked="checked"' : '') ?>/>&#160;<?= lng('rank_6') ?>
            </p>
            <?php if (Vars::$USER_RIGHTS == 9) : ?>
            <p>
                <input type="radio" value="7" name="rights" <?= ($this->user['rights'] == 7 ? 'checked="checked"' : '') ?>/>&#160;<?= lng('rank_7') ?>
                <br/><input type="radio" value="9" name="rights" <?= ($this->user['rights'] == 9 ? 'checked="checked"' : '') ?>/>&#160;<span class="red"><b><?= lng('rank_9') ?></b></span>
            </p>
            <?php endif; ?>
        </div>
        <div class="formblock">
            <label for="password"><?= lng('your_password') ?></label><br/>
            <?php if (isset($this->error['password'])) : ?>
            <small class="red"><b><?= lng('error') ?></b>: <?= $this->error['password'] ?><br/></small>
            <?php endif; ?>
            <input id="password" type="password" name="password" <?= (isset($this->error['password']) ? 'class="error"' : '') ?>/>
        </div>
        <div class="formblock">
            <input type="submit" value="<?= lng('save') ?>" name="submit"/>
        </div>
    </div>
    <input type="hidden" name="form_token" value="<?= $this->form_token ?>"/>
</form>
<div class="phdr">
    <a href="<?= Vars::$MODULE_URI ?>?user=<?= $this->user['id'] ?>"><?= lng('back') ?></a>
</div>