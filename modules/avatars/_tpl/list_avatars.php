<ul class="nav">
    <li><h1><?= lng('avatars') ?> :: <?= $this->category ?></h1></li>
    <?php if ($this->total): ?>
    <?php foreach ($this->list as $val): ?>
        <li><a href="<?= $val['link'] ?>"><img src="<?= $val['image'] ?>" alt=""/>
            <?php if (Vars::$USER_ID): ?>
            <i class="icn-arrow"></i>
            <?php endif ?>
        </a></li>
        <?php endforeach ?>
    <?php else: ?>
    <li><a href="<?= Vars::$URI ?>"><i class="icn-info"></i><?= lng('list_empty') ?><i class="icn-arrow"></i></a></li>
    <?php endif ?>
</ul>