<ul class="nav">
    <li><h1<?= ($this->user['id'] == Vars::$USER_ID ? ' class="section-personal"' : '') ?>><?= __('change_status') ?></h1></li>
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

    <form action="<?= Vars::$URI ?>?act=edit_status&amp;user=<?= $this->user['id'] ?>" method="post">
        <div class="form-block">
            <label for="status"><?= __('status') ?></label><br/>
            <?php if (isset($this->error['status'])): ?>
            <span class="label label-red"><?= $this->error['status'] ?></span><br/>
            <?php endif ?>
            <input <?= (isset($this->error['status']) ? 'class="error"' : '') ?> id="status" type="text" value="<?= $this->status ?>" name="status"/>
            <br/><span class="input-help"><?= __('status_lenght') ?></span><br/><br/>

            <input class="btn btn-primary btn-large" type="submit" value="<?= __('save') ?>" name="submit"/>
            <a class="btn" href="<?= Vars::$URI ?>?act=settings&amp;user=<?= $this->user['id'] ?>"><?= __('back') ?></a>
            <input type="hidden" name="form_token" value="<?= $this->form_token ?>"/>
        </div>
    </form>
</div>