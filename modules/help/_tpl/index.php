<ul class="nav">
    <li><h1><?= __('information') ?></h1></li>
    <li><a href="<?= Vars::$HOME_URL ?>/forum/rules"><i class="icn-info"></i><?= __('forum_rules') ?><i class="icn-arrow right"></i></a></li>
    <li><a href="<?= Vars::$URI ?>/tags"><i class="icn-info"></i><?= __('tags') ?><i class="icn-arrow right"></i></a></li>
    <li><a href="<?= Vars::$HOME_URL ?>/avatars"><i class="icn-image"></i><?= __('avatars') ?><i class="icn-arrow right"></i></a></li>
    <li><a href="<?= Vars::$HOME_URL ?>/smileys"><i class="icn-smile"></i><?= __('smileys') ?><i class="icn-arrow right"></i></a></li>
    <li><a href="<?= (isset($_SESSION['ref']) ? $_SESSION['ref'] : Vars::$HOME_URL) ?>"><?= __('back') ?><i class="icn-arrow right"></i></a></li>
</ul>