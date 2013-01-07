<ul class="nav">
    <li><h1><?= __('photo_albums') ?></h1></li>
    <li><h2><?= __('new') ?></h2></li>
    <li><a href="<?= $this->link ?>?act=new"><i class="icn-image"></i><?= __('photos') ?><i class="icn-arrow right"></i><span class="badge badge-right"><?= $this->new ?></span></a></li>
    <li><a href="<?= $this->link ?>/top?act=last_comm"><i class="icn-dialogue"></i><?= __('comments') ?><i class="icn-arrow right"></i></a></li>
    <li><h2><?= __('albums') ?></h2></li>
    <li><a href="<?= $this->link ?>?act=users&amp;mod=boys"><i class="icn-man"></i><?= __('mans') ?><i class="icn-arrow right"></i><span class="badge badge-right"><?= $this->count_m ?></span></a></li>
    <li><a href="<?= $this->link ?>?act=users&amp;mod=girls"><i class="icn-woman"></i><?= __('womans') ?><i class="icn-arrow right"></i><span class="badge badge-right"><?= $this->count_w ?></span></a></li>
    <?php if (Vars::$USER_ID): ?>
    <li><a href="<?= $this->link ?>?act=list"><i class="icn-camera"></i><?= __('my_album') ?><i class="icn-arrow right"></i><span class="badge badge-right"><?= $this->count_my ?></span></a></li>
    <?php endif ?>
    <li><h2><?= __('rating') ?></h2></li>
    <li><a href="<?= $this->link ?>/top"><i class="icn-chart"></i><?= __('top_votes') ?><i class="icn-arrow right"></i></a></li>
    <li><a href="<?= $this->link ?>/top?act=downloads"><i class="icn-chart"></i><?= __('top_downloads') ?><i class="icn-arrow right"></i></a></li>
    <li><a href="<?= $this->link ?>/top?act=views"><i class="icn-chart"></i><?= __('top_views') ?><i class="icn-arrow right"></i></a></li>
    <li><a href="<?= $this->link ?>/top?act=comments"><i class="icn-chart"></i><?= __('top_comments') ?><i class="icn-arrow right"></i></a></li>
    <li><a href="<?= $this->link ?>/top?act=trash"><i class="icn-chart"></i><?= __('top_trash') ?><i class="icn-arrow right"></i></a></li>
</ul>