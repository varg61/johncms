<ul class="nav">
    <li><h1><?= lng('site_news') ?></h1></li>
</ul>
<?php if (isset($this->news)): ?>
<?php foreach ($this->news as $key => $val): ?>
    <div class="<?= $key % 2 ? 'block-odd' : 'block-even' ?>">
        <div class="block-hdr"><?= $val['name'] ?></div>
        <div class="block-text"><?= $val['text'] ?></div>
    </div>
    <?php endforeach ?>
<ul class="nav">
    <li><h1><?= lng('total') ?>:&#160;<?= $this->total ?></h1></li>
</ul>
<?php if (isset($this->pagination)): ?>
    <div class="align-center"><?= $this->pagination ?></div>
    <?php endif ?>
<?php else: ?>
<div class="menu"><p><?= lng('list_empty') ?></p></div>
<?php endif ?>
<div><a href="<?= Vars::$URI ?>?act=add">Добавить новость</a></div>