<ul class="nav">
    <li><h1><?= lng('photo_albums') ?></h1></li>
    <li><h2><?= lng('new') ?></h2></li>
    <li><a href="<?= Vars::$URI ?>?act=new"><i class="icn-image"></i><?= lng('photos') ?><i class="icn-arrow right"></i><span class="badge badge-right"><?= $this->new ?></span></a></li>
    <li><a href="<?= Vars::$URI ?>/top?act=last_comm"><i class="icn-dialogue"></i><?= lng('comments') ?><i class="icn-arrow right"></i></a></li>
    <li><h2><?= lng('albums') ?></h2></li>
    <li><a href="<?= Vars::$URI ?>?act=users&amp;mod=boys"><i class="icn-man"></i><?= lng('mans') ?><i class="icn-arrow right"></i><span class="badge badge-right"><?= $this->count_m ?></span></a></li>
    <li><a href="<?= Vars::$URI ?>?act=users&amp;mod=girls"><i class="icn-woman"></i><?= lng('womans') ?><i class="icn-arrow right"></i><span class="badge badge-right"><?= $this->count_w ?></span></a></li>
    <?php if (Vars::$USER_ID): ?>
    <li><a href="<?= Vars::$URI ?>?act=list"><i class="icn-camera"></i><?= lng('my_album') ?><i class="icn-arrow right"></i><span class="badge badge-right"><?= $this->count_my ?></span></a></li>
    <?php endif ?>
    <li><h2><?= lng('rating') ?></h2></li>
    <li><a href="<?= Vars::$URI ?>/top"><i class="icn-chart"></i><?= lng('top_votes') ?><i class="icn-arrow right"></i></a></li>
    <li><a href="<?= Vars::$URI ?>/top?act=downloads"><i class="icn-chart"></i><?= lng('top_downloads') ?><i class="icn-arrow right"></i></a></li>
    <li><a href="<?= Vars::$URI ?>/top?act=views"><i class="icn-chart"></i><?= lng('top_views') ?><i class="icn-arrow right"></i></a></li>
    <li><a href="<?= Vars::$URI ?>/top?act=comments"><i class="icn-chart"></i><?= lng('top_comments') ?><i class="icn-arrow right"></i></a></li>
    <li><a href="<?= Vars::$URI ?>/top?act=trash"><i class="icn-chart"></i><?= lng('top_trash') ?><i class="icn-arrow right"></i></a></li>
</ul>