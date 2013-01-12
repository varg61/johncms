<ul class="nav">
    <li><h1><?= __('site_news') ?></h1></li>
</ul>

<?php if (isset($this->list)): ?>
<?php foreach ($this->list as $key => $val): ?>
    <div class="block-<?= $key % 2 ? 'odd' : 'even' ?>">
        <div class="block-hdr">
            <?= $val['name'] ?>
        </div>
        <div class="block-info">
            <?= Functions::displayDate($val['time']) ?><br/>
            <?= __('added') ?>: <a href="<?= Vars::$HOME_URL ?>users/<?= $val['author_id'] ?>/"><?= $val['author'] ?></a>
        </div>
        <div class="block-text">
            <?= $val['text'] ?>
        </div>
        <?php if (isset($this->comments) || Vars::$USER_RIGHTS >= 7): ?>
        <div class="block-tools">
            <?php if (Vars::$USER_RIGHTS >= 7): ?>
            <div style="display: inline-block">
                <a class="btn btn-mini" href="<?= $this->uri ?>?act=edit&amp;id=<?= $val['id'] ?>"><?= __('edit') ?></a>
                <a class="btn btn-mini" href="<?= $this->uri ?>?act=del&amp;id=<?= $val['id'] ?>"><?= __('delete') ?></a>
            </div>
            <?php endif ?>
            <?php if (isset($this->comments)): ?>
            <a class="btn btn-mini" href="<?= Vars::$HOME_URL ?>forum/?id=<?= $this->comments_id ?>"><?= __('discuss_on_forum') ?>: <?= $this->comments ?></a>
            <?php endif ?>
        </div>
        <?php endif ?>
    </div>
    <?php endforeach ?>

<ul class="nav">
    <li><h2><?= __('total') ?>:&#160;<?= $this->total ?></h2></li>
</ul>

<?php if ($this->total > Vars::$USER_SET['page_size']): ?>
    <div class="align-center">
        <?= Functions::displayPagination($this->uri . '?', Vars::$START, $this->total, Vars::$USER_SET['page_size']) ?>
        <form action="<?= $this->uri ?>" method="post">
            <input class="mini" type="text" name="page" size="2"/>
            <input type="submit" value="<?= __('to_page') ?> &gt;&gt;"/>
        </form>
    </div>
    <?php endif ?>
<?php else: ?>
<div class="form-container">
    <div class="form-block align-center"><?= __('list_empty') ?></div>
</div>
<?php endif ?>

<?php if (Vars::$USER_RIGHTS >= 7): ?>
<div class="btn-panel">
    <a class="btn" href="<?= $this->uri ?>add/"><i class="icn-edit"></i><?= __('add') ?></a>
    <a class="btn" href="<?= $this->uri ?>admin/"><i class="icn-settings"></i><?= __('settings') ?></a>
</div>
<?php endif ?>