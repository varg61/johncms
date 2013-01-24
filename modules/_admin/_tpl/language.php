<ul class="nav">
    <li><h1 class="section-warning"><?= __('language_settings') ?></h1></li>
</ul>
<div class="form-container">
    <?php if (isset($_GET['save'])): ?>
    <div class="form-block confirm">
        <?= __('settings_saved') ?>
    </div>
    <?php endif ?>
    <div class="form-block">
        <?= $this->form ?>
    </div>
</div>