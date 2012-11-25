<?php if (!empty($this->error)): ?>
<div class="form-block error">
    <?= __('errors_occurred') ?>
</div>
<?php elseif(isset($this->save)): ?>
<div class="form-block confirm">
    <?= __('settings_saved') ?>
</div>
<?php endif ?>