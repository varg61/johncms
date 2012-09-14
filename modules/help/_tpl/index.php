<ul class="nav">
    <li><h1><?= lng('information') ?></h1></li>
    <li><a href="<?= Vars::$HOME_URL ?>/forum/rules"><i class="icn-info"></i><?= lng('forum_rules') ?><i class="icn-arrow right"></i></a></li>
    <li><a href="<?= Vars::$URI ?>/tags"><i class="icn-info"></i><?= lng('tags') ?><i class="icn-arrow right"></i></a></li>
    <li><a href="<?= Vars::$HOME_URL ?>/avatars"><i class="icn-image"></i><?= lng('avatars') ?><i class="icn-arrow right"></i></a></li>
    <li><a href="<?= Vars::$HOME_URL ?>/smileys"><i class="icn-smile"></i><?= lng('smileys') ?><i class="icn-arrow right"></i></a></li>
    <li><a href="<?= (isset($_SESSION['ref']) ? $_SESSION['ref'] : Vars::$HOME_URL) ?>"><?= lng('back') ?><i class="icn-arrow right"></i></a></li>
</ul>