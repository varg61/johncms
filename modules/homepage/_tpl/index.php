<!-- Заголовок раздела -->
<ul class="title">
    <li class="center"><h1><?= __('welcome') ?></h1></li>
</ul>

<!-- Меню -->
<ul class="nav">
    <li><h2><?= __('information') ?></h2></li>
    <li><a href="<?= Vars::$HOME_URL ?>news/"><i class="icn-news"></i><?= __('news_archive') ?><i class="icn-arrow"></i></a></li>
    <li><a href="<?= Vars::$HOME_URL ?>help/"><i class="icn-info"></i><?= __('information') ?>, FAQ<i class="icn-arrow"></i></a></li>
    <li><h2><?= __('dialogue') ?></h2></li>
    <li><a href="<?= Vars::$HOME_URL ?>guestbook/"><i class="icn-dialogue"></i><?= __('guestbook') ?><i class="icn-arrow"></i><span class="badge">0</span></a></li>
    <li><a href="<?= Vars::$HOME_URL ?>forum/"><i class="icn-comments"></i><?= __('forum') ?><i class="icn-arrow"></i><span class="badge">0</span></a></li>
    <li><h2><?= __('useful') ?></h2></li>
    <li><a href="<?= Vars::$HOME_URL ?>download/"><i class="icn-download"></i><?= __('downloads') ?><i class="icn-arrow"></i><span class="badge">0</span></a></li>
    <li><a href="<?= Vars::$HOME_URL ?>library/"><i class="icn-book"></i><?= __('library') ?><i class="icn-arrow"></i><span class="badge">0</span></a></li>
    <li><h2><?= __('community') ?></h2></li>
    <li><a href="<?= Vars::$HOME_URL ?>users/"><i class="icn-man-woman"></i><?= __('users') ?> //планируется<i class="icn-arrow"></i><span class="badge"><?= $this->count->users ?></span></a></li>
    <li><a href="<?= Vars::$HOME_URL ?>album/"><i class="icn-image"></i><?= __('photo_albums') ?> //планируется<i class="icn-arrow"></i><span class="badge">0</span></a></li>
</ul>