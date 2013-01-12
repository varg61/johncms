<ul class="nav">
    <li><h1><?= __('avatars') ?> :: <?= $this->category ?></h1></li>
    <?php if ($this->total): ?>
    <?php foreach ($this->list as $val): ?>
        <li><a href="<?= $val['link'] ?>"><img src="<?= $val['image'] ?>" alt=""/>
            <?php if (Vars::$USER_ID): ?>
            <i class="icn-arrow"></i>
            <?php endif ?>
        </a></li>
        <?php endforeach ?>
    <?php else: ?>
    <li><a href="<?= Router::getUri(3) ?>"><i class="icn-info"></i><?= __('list_empty') ?><i class="icn-arrow"></i></a></li>
    <?php endif ?>
</ul>