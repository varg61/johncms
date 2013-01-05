<ul class="nav">
    <li><h1 class="section-warning"><?= __('antispy') ?></h1></li>
</ul>
<div class="form-container">
    <?php if (isset($this->ok)): ?>
    <div class="form-block confirm">
        <?= $this->ok ?>
    </div>
    <?php elseif(isset($this->bad)): ?>
    <div class="form-block error">
        <?= $this->bad ?>
    </div>
    <?php endif; ?>

    <div class="form-block">
        <?= $this->form ?>
    </div>

    <?php if (isset($this->files)): ?>
    <div class="form-block error">
        <?= __('antispy_dist_bad_help') ?><br/><br/>
        <?php foreach ($this->files as $file): ?>
        <div><?= ltrim($file['file_path'], './') ?></div>
        <?php endforeach ?>
    </div>
    <?php endif ?>
</div>