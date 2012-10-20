<?php if (!empty($this->error)): ?>
<div class="form-block error">
    <span class="input-help error"><b><?= lng('errors_occurred') ?></b></span>
</div>
<?php elseif(isset($this->save)): ?>
<div class="form-block confirm">
    <?= lng('settings_saved') ?>
</div>
<?php endif ?>