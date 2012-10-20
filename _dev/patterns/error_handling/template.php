<?php if (!empty($this->error)): ?>
<div class="form-block error">
    <?= lng('errors_occurred') ?>
</div>
<?php elseif(isset($this->save)): ?>
<div class="form-block confirm">
    <?= lng('settings_saved') ?>
</div>
<?php endif ?>