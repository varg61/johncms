<?php if(isset($this->hbar)): ?>
<ul class="nav">
    <li><h1><?= $this->hbar ?></h1></li>
</ul>
<?php endif ?>
<div class="form-container">
    <div class="form-block align-center">
        <?php if (isset($this->message)): ?>
        <div class="info-message"><?= $this->message ?></div>
        <?php endif ?>
        <?php if(isset($this->continue)): ?>
        <a class="btn btn-primary btn-large" href="<?= $this->continue ?>"><?= lng('continue') ?></a>
        <?php endif ?>
        <?php if(isset($this->back)): ?>
        <a class="btn btn-large" href="<?= $this->back ?>"><?= lng('back') ?></a>
        <?php endif ?>
    </div>
</div>