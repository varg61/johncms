<ul class="nav">
    <li><h1><?= lng('site_news') ?></h1></li>
</ul>
<?php if (isset($this->news)): ?>
    <?php foreach ($this->news as $key => $val): ?>
    <div class="<?= $key % 2 ? 'block-odd' : 'block-even' ?>">
        <div class="block-hdr"><?= $val['name'] ?></div>
        <div class="block-text"><?= $val['text'] ?></div>
        <div class="list-tools">
            <a class="btn btn-mini" href="<?= Vars::$URI ?>"><?= lng('edit') ?></a>
            <a class="btn btn-mini" href="<?= Vars::$URI ?>"><?= lng('delete') ?></a>
        </div>
    </div>
    <?php endforeach ?>
    <ul class="nav">
        <li><h1><?= lng('total') ?>:&#160;<?= $this->total ?></h1></li>
    </ul>
    <?php if (isset($this->pagination)): ?>
        <div class="align-center"><?= $this->pagination ?></div>
        <?php endif ?>
<?php else: ?>
    <div class="form-container">
        <div class="form-block align-center">
            <?= lng('list_empty') ?>
        </div>
    </div>
<?php endif ?>
<?php if(Vars::$USER_RIGHTS >= 7): ?>
    <div class="btn-panel"><a class="btn" href="<?= Vars::$URI ?>?act=add">Добавить новость</a></div>
<?php endif ?>