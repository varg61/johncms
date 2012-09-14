<ul class="nav">
    <li><h1><?= lng('avatars') ?></h1></li>
    <?php foreach ($this->list as $val): ?>
    <li><a href="<?= $val['link'] ?>"><i class="icn-image"></i><?= $val['name'] ?><i class="icn-arrow"></i><span class="badge badge-right"><?= $val['count'] ?></span></a></li>
    <?php endforeach ?>
</ul>