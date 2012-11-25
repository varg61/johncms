<?php if (isset($this->hbar)): ?>
<ul class="nav">
    <li><h1<?= (isset($this->user) && $this->user['id'] == Vars::$USER_ID ? ' class="section-personal"' : '') ?>><?= $this->hbar ?></h1></li>
</ul>
<?php endif ?>
<div class="form-container">
    <div class="form-block align-center">
        <?php if (isset($this->message)): ?>
        <div class="info-message"><?= $this->message ?></div>
        <?php endif ?>
        <?php if (isset($this->continue)): ?>
        <a class="btn btn-primary btn-large" href="<?= $this->continue ?>"><?= __('continue') ?></a>
        <?php endif ?>
        <?php if (isset($this->back)): ?>
        <a class="btn btn-large" href="<?= $this->back ?>"><?= __('back') ?></a>
        <?php endif ?>
    </div>
</div>