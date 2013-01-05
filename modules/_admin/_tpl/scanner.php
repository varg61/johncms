<ul class="nav">
    <li><h1 class="section-warning"><?= __('antispy') ?></h1></li>
</ul>
<div class="form-container">
    <?php if (isset($this->errormsg)): ?>
    <div class="form-block error">
        <?= $this->errormsg ?>
    </div>
    <?php elseif (isset($this->ok)): ?>
    <div class="form-block confirm">
        <?= $this->ok ?>
    </div>
    <?php endif ?>

    <div class="form-block">
        <?= $this->form ?>
    </div>

    <?php if (isset($this->files)): ?>
    <div class="form-block error">
        <?= __('antispy_dist_bad_help') ?><br/>
        <?= __('total') ?>: <?= count($this->files) ?><br/><br/>
        <?php foreach ($this->files as $file): ?>
        <div style="font-size: small; font-weight: bold; padding-bottom: 4px">
            <?= htmlspecialchars($file['file_path']) ?>
            <div style="font-size: x-small; font-weight: normal; color: #696969">
                <?= __('date') . ': ' . $file['file_date'] ?><br/>
                <?= __('size') . ': ' . round($file['file_size'] / 1024, 2)?> kB
            </div>
        </div>
        <?php endforeach ?>
    </div>
    <?php endif ?>
</div>