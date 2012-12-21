<ul class="nav">
    <li><h1><?= __('news_on_frontpage') ?></h1></li>
</ul>
<div class="form-container">
    <?php if (isset($this->save)): ?>
    <div class="form-block confirm"><?= __('settings_saved') ?></div>
    <?php endif ?>
    <?php if (isset($_GET['default'])): ?>
    <div class="form-block confirm"><?= __('settings_default') ?></div>
    <?php endif ?>
    <div class="form-block">
        <?= $this->form ?>
    </div>
</div>