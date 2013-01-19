<ul class="nav">
    <li><h1><?= __('smilies') ?></h1></li>
    <?php foreach ($this->list as $val): ?>
    <li><a href="<?= $val['link'] ?>"><i class="icn-smile"></i><?= $val['name'] ?><i class="icn-arrow"></i><span class="badge badge-right"><?= $val['count'] ?></span></a></li>
    <?php endforeach ?>
</ul>