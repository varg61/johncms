<!-- Заголовок раздела -->
<script type="text/javascript">
    $(document).ready(function () {
        $(".slider-button").click(function () {
            $(".slider").slideToggle();
            $(this).toggleClass("close");
        });
    });
</script>
<ul class="title">
    <li class="left"></li>
    <li class="center"><h1><?= __('site_news') ?></h1></li>
    <?php if (Vars::$USER_RIGHTS >= 7): ?>
    <li class="separator"></li>
    <li class="right"><a href="#" class="slider-button"><span class="icn icn-gear"></span></a></li>
    <?php else: ?>
    <li class="right"></li>
    <?php endif ?>
</ul>

<div class="content">
    <div class="hdr-submenu slider">
        <a href="<?= $this->uri ?>add/" class="btn btn-primary"><?= __('add') ?></a>
        <a href="#" class="btn"><?= __('clear') ?></a>
        <a href="<?= $this->uri ?>admin/" class="btn"><?= __('settings') ?></a>
    </div>

    <div class="list-header"><?= __('news') ?></div>
    <div class="list striped">
        <?php if (isset($this->list)): ?>
        <?php foreach ($this->list as $key => $val): ?>
            <div>
                <div class="news-title"><?= $val['name'] ?></div>
                <div class="news-info">
                    <?= Functions::displayDate($val['time']) ?><br/>
                    <?= __('added') ?>: <a href="<?= Vars::$HOME_URL ?>users/<?= $val['author_id'] ?>/"><?= $val['author'] ?></a>
                </div>
                <div class="news-text"><?= $val['text'] ?></div>
                <div class="slider">
                    <a href="#" class="btn btn-small"><?= __('edit') ?></a>
                    <a href="#" class="btn btn-small"><?= __('delete') ?></a>
                </div>
            </div>
            <?php endforeach ?>
        <?php else: ?>
        <div style="text-align: center; padding: 27px"><?= __('list_empty') ?></div>
        <?php endif ?>
    </div>
    <div class="list-footer"><?= __('total') ?>:&#160;<?= $this->total ?></div>

    <?php if ($this->total > Vars::$USER_SET['page_size']): ?>
    <?= Functions::displayPagination($this->uri . '?', Vars::$START, $this->total, Vars::$USER_SET['page_size']) ?>
    <?php endif ?>
</div>