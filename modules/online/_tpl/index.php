<ul class="nav">
    <li><h1><?= lng('who_on_site') ?></h1></li>
</ul>
<?php if (isset($this->list)): ?>
<?php foreach ($this->list as $key => $val): ?>
    <div class="<?= $key % 2 ? 'block-odd' : 'block-even' ?>"><?= $val ?></div>
    <?php endforeach ?>
<?php else: ?>
<div class="form-container">
    <div class="form-block align-center"><?= lng('list_empty') ?></div>
</div>
<?php endif ?>