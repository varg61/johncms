<?php if (isset($this->total) && isset($this->list)): ?>
    <?php foreach ($this->list as $key => $val): ?>
        <div class="block-<?= $key % 2 ? 'odd' : 'even' ?>">
            <?= $val['text'] ?>
        </div>
    <?php endforeach ?>

    <?php if ($this->total > Vars::$USER_SET['page_size']): ?>
    <div class="align-center">
        <?= Functions::displayPagination(Router::getUrl(3) . '?', Vars::$START, $this->total, Vars::$USER_SET['page_size']) ?>
        <form action="<?= Router::getUrl(3) ?>" method="post">
            <input class="mini" type="text" name="page" size="2"/>
            <input type="submit" value="<?= __('to_page') ?> &gt;&gt;"/>
        </form>
    </div>
    <?php endif ?>
<?php endif ?>