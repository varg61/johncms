<ul class="nav">
    <li><h1><?= __('users') ?></h1></li>
    <li style="height: 68px"><a href="#">[Форма поиска юзера]</a></li>
    <li><h2><?= __('community') ?></h2></li>
    <li><a href="<?= $this->link ?>/search"><i class="icn-man-woman"></i><?= __('users') ?><i class="icn-arrow right"></i><span class="badge badge-right"><?= $this->count->users ?></span></a></li>
    <li><a href="<?= $this->link ?>/search?act=adm"><i class="icn-man"></i><?= __('administration') ?><i class="icn-arrow right"></i></a></li>
    <li><h2><?= __('rating') ?></h2></li>
    <li><a href="<?= $this->link ?>/top"><i class="icn-chart"></i><?= __('forum') ?><i class="icn-arrow right"></i></a></li>
    <li><a href="<?= $this->link ?>/top?act=comm"><i class="icn-chart"></i><?= __('comments') ?><i class="icn-arrow right"></i></a></li>
    <li><a href="<?= $this->link ?>/top?act=karma"><i class="icn-chart"></i><?= __('karma') ?><i class="icn-arrow right"></i></a></li>
</ul>