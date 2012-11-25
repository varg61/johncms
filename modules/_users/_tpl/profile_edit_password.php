<ul class="nav">
    <li><h1<?= ($this->user['id'] == Vars::$USER_ID ? ' class="section-personal"' : '') ?>><?= __('change_password') ?></h1></li>
</ul>
<div class="form-container">
    <?php if (!empty($this->error)): ?>
    <div class="form-block error">
        <?= __('errors_occurred') ?>
    </div>
    <?php elseif(isset($this->save)): ?>
    <div class="form-block confirm">
        <?= __('settings_saved') ?>
    </div>
    <?php endif ?>

    <div class="form-block">
        <?= Functions::displayUser($this->user, array('iphide' => 1,)) ?>
    </div>

    <form action="<?= Vars::$URI ?>?act=edit_password&amp;user=<?= $this->user['id'] ?>" method="post">
        <div class="form-block">
            <label for="oldpass"><?= ($this->user['id'] == Vars::$USER_ID ? __('old_password') : __('your_password')) ?> <span class="attn">*</span></label><br/>
            <?php if (isset($this->error['oldpass'])) : ?>
            <span class="label label-red"><?= $this->error['oldpass'] ?></span><br/>
            <?php endif ?>
            <input id="oldpass" name="oldpass" type="password" <?= (isset($this->error['oldpass']) ? 'class="error"' : '') ?>/>
            <br/><br/>
            <label for="newpass"><?= __('new_password') ?> <span class="attn">*</span></label><br/>
            <?php if (isset($this->error['newpass'])) : ?>
            <span class="label label-red"><?= $this->error['newpass'] ?></span><br/>
            <?php endif ?>
            <input id="newpass" name="newpass" type="password" <?= (isset($this->error['newpass']) ? 'class="error"' : '') ?>/>
            <br/>
            <label for="newconf"><?= __('repeat_password') ?> <span class="attn">*</span></label><br/>
            <?php if (isset($this->error['newconf'])) : ?>
            <span class="label label-red"><?= $this->error['newconf'] ?></span><br/>
            <?php endif ?>
            <input id="newconf" name="newconf" type="password" <?= (isset($this->error['newconf']) ? 'class="error"' : '') ?>/><br/>
            <span class="input-help"><?= __('password_change_help') ?></span>
            <br/><br/>
            <input class="btn btn-primary btn-large" type="submit" value="<?= __('save') ?>" name="submit"/>
            <a class="btn" href="<?= Vars::$URI ?>?act=settings&amp;user=<?= $this->user['id'] ?>"><?= __('back') ?></a>
            <input type="hidden" name="form_token" value="<?= $this->form_token ?>"/>
        </div>
    </form>
</div>