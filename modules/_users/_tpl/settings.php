<ul class="nav">
    <li><h1<?= Users::$data['id'] == Vars::$USER_ID ? ' class="section-personal"' : '' ?>><?= __('settings') ?></h1></li>
</ul>

<div class="form-container">
    <?php if (isset($_GET['save'])): ?>
    <div class="form-block confirm">
        <?= __('settings_saved') ?>
    </div>
    <?php endif ?>
    <div class="form-block">
        <?= Functions::displayUser(Users::$data, array('iphide' => 1)) ?>
    </div>
    <div class="form-block">
        <?= $this->form ?>
    </div>
</div>