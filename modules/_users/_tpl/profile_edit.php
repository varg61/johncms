<ul class="nav">
    <li><h1<?= $this->user['id'] == Vars::$USER_ID ? ' class="section-personal"' : '' ?>><?= __('profile_edit') ?></h1></li>
</ul>
<div class="form-container">
    <?php if (!empty($this->error)): ?>
        <div class="form-block error"><?= __('errors_occurred') ?></div>
    <?php elseif (isset($this->save)): ?>
        <div class="form-block confirm"><?= __('settings_saved') ?></div>
    <?php endif ?>

    <div class="form-block">
        <?= Functions::displayUser($this->user, array('iphide' => 1,)) ?>
    </div>

        <div class="form-block">
            <?= $this->form ?>
        </div>
</div>