<ul class="nav">
    <li><h1 class="section-warning"><?= __('users') ?> :: <?= __('settings') ?></h1></li>
</ul>
<div class="form-container">
    <?php if (isset($this->save)) : ?>
    <div class="form-block confirm"><?= __('settings_saved') ?></div>
    <?php endif ?>
    <?php if (isset($this->reset)) : ?>
    <div class="form-block confirm"><?= __('settings_default') ?></div>
    <?php endif ?>
    <div class="form-block"><?= $this->form ?></div>
</div>