<ul class="nav">
    <li><h1 class="section-warning"><?= __('rank') ?></h1></li>
</ul>
<div class="form-container">
    <?php if (!empty($this->error)): ?>
    <div class="form-block error">
        <?= __('errors_occurred') ?>
    </div>
    <?php elseif (isset($this->save)): ?>
    <div class="form-block confirm">
        <?= __('settings_saved') ?>
    </div>
    <?php endif ?>

    <div class="form-block">
        <?= Functions::displayUser($this->user, array('iphide' => 1,)) ?>
    </div>

    <form action="<?= Vars::$URI ?>?act=edit_admin&amp;user=<?= $this->user['id'] ?>" method="post">
        <div class="form-block">
            <label class="small"><input type="radio" value="0" name="rights" <?= (!$this->user['rights'] ? 'checked="checked"' : '') ?>/>&#160;<?= __('rank_0') ?></label><br/><br/>
            <label class="small"><input type="radio" value="3" name="rights" <?= ($this->user['rights'] == 3 ? 'checked="checked"' : '') ?>/>&#160;<?= __('rank_3') ?></label><br/>
            <label class="small"><input type="radio" value="4" name="rights" <?= ($this->user['rights'] == 4 ? 'checked="checked"' : '') ?>/>&#160;<?= __('rank_4') ?></label><br/>
            <label class="small"><input type="radio" value="5" name="rights" <?= ($this->user['rights'] == 5 ? 'checked="checked"' : '') ?>/>&#160;<?= __('rank_5') ?></label><br/>
            <label class="small"><input type="radio" value="6" name="rights" <?= ($this->user['rights'] == 6 ? 'checked="checked"' : '') ?>/>&#160;<?= __('rank_6') ?></label><br/><br/>

            <?php if (Vars::$USER_RIGHTS == 9) : ?>
            <label class="small"><i class="icn-shield"></i><input type="radio" value="7" name="rights" <?= ($this->user['rights'] == 7 ? 'checked="checked"' : '') ?>/>&#160;<?= __('rank_7') ?></label><br/>
            <label class="small"><i class="icn-shield-red"></i><input type="radio" value="9" name="rights" <?= ($this->user['rights'] == 9 ? 'checked="checked"' : '') ?>/>&#160;<?= __('rank_9') ?></label><br/><br/>
            <?php endif ?>

            <label for="password"><?= __('your_password') ?></label><br/>
            <?php if (isset($this->error['password'])): ?>
            <span class="label label-red"><?= $this->error['password'] ?></span><br/>
            <?php endif ?>
            <input id="password" type="password" name="password" <?= (isset($this->error['password']) ? 'class="error"' : '') ?>/>
            <br/><br/>
            <input class="btn btn-primary btn-large" type="submit" value="<?= __('save') ?>" name="submit"/>
            <a class="btn" href="<?= Vars::$URI ?>?act=settings&amp;user=<?= $this->user['id'] ?>"><?= __('back') ?></a>
            <input type="hidden" name="form_token" value="<?= $this->form_token ?>"/>
        </div>
    </form>
</div>