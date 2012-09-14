<ul class="nav">
    <li><h1><?= lng('users') ?></h1></li>
    <li style="height: 68px"><a href="#">[Форма поиска юзера]</a></li>
    <li><h2><?= lng('community') ?></h2></li>
    <li><a href="<?= Vars::$URI ?>/search"><i class="icn-man-woman"></i><?= lng('users') ?><i class="icn-arrow right"></i><span class="badge badge-right"><?= $this->count->users ?></span></a></li>
    <li><a href="<?= Vars::$URI ?>/search?act=adm"><i class="icn-man"></i><?= lng('administration') ?><i class="icn-arrow right"></i></a></li>
    <li><h2><?= lng('rating') ?></h2></li>
    <li><a href="<?= Vars::$URI ?>/top"><i class="icn-chart"></i><?= lng('forum') ?><i class="icn-arrow right"></i></a></li>
    <li><a href="<?= Vars::$URI ?>/top?act=comm"><i class="icn-chart"></i><?= lng('comments') ?><i class="icn-arrow right"></i></a></li>
    <li><a href="<?= Vars::$URI ?>/top?act=karma"><i class="icn-chart"></i><?= lng('karma') ?><i class="icn-arrow right"></i></a></li>
</ul>