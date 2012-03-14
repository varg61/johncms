<div class="phdr"><b><?= lng('photo_albums') ?></b></div>
<div class="gmenu"><p>
    <?= Functions::getImage('album_new.png') ?>&#160;<a href="album.php?act=top"><?= lng('new_photo') ?></a> (<?= $this->new ?>)<br />
    <?= Functions::getImage('comments.png') ?>&#160;<a href="album.php?act=top&amp;mod=last_comm"><?= lng('new_comments') ?></a>
    </p></div>
<div class="menu">
    <p><h3><?= Functions::getImage('users.png') ?>&#160;<?= lng('albums') ?></h3><ul>
        <li><a href="album.php?act=users"><?= lng('album_list') ?></a> (<?= $this->count ?>)</li>
        </ul></p>
    <p><h3><?= Functions::getImage('rating.png') ?>&#160;<?= lng('rating') ?></h3><ul>
        <li><a href="<?= Vars::$URI ?>/top"><?= lng('top_votes') ?></a></li>
        <li><a href="<?= Vars::$URI ?>/top?act=downloads"><?= lng('top_downloads') ?></a></li>
        <li><a href="<?= Vars::$URI ?>/top?act=views"><?= lng('top_views') ?></a></li>
        <li><a href="<?= Vars::$URI ?>/top?act=comments"><?= lng('top_comments') ?></a></li>
        <li><a href="<?= Vars::$URI ?>/top?act=trash"><?= lng('top_trash') ?></a></li>
        </ul></p>
    </div>
<div class="phdr"><a href="<?= Vars::$HOME_URL ?>/users"><?= lng('users') ?></a></div>