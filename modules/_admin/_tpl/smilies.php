<ul class="nav">
    <li><h1 class="section-warning"><?= __('smilies') ?></h1></li>
</ul>
<div class="form-container">
    <?php if (isset($this->error)): ?>
    <div class="form-block error">
        <?= $this->error ?>
    </div>
    <?php elseif (isset($this->save)): ?>
    <div class="form-block confirm">
        <?= $this->save ?>
    </div>
    <?php endif ?>
    <div class="form-block ">
        <?= $this->form ?>
    </div>
</div>