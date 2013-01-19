<ul class="nav">
    <li><h1><?= __('information') ?></h1></li>
    <li><a href="<?= Vars::$HOME_URL ?>forum/rules/"><i class="icn-info"></i><?= __('forum_rules') ?><i class="icn-arrow right"></i></a></li>
    <li><a href="<?= Router::getUri(2) ?>tags/"><i class="icn-info"></i><?= __('tags') ?><i class="icn-arrow right"></i></a></li>
    <li><a href="<?= Vars::$HOME_URL ?>avatars/"><i class="icn-image"></i><?= __('avatars') ?><i class="icn-arrow right"></i></a></li>
    <li><a href="<?= Vars::$HOME_URL ?>smilies/"><i class="icn-smile"></i><?= __('smilies') ?><i class="icn-arrow right"></i></a></li>
    <li><a href="<?= (isset($_SESSION['ref']) ? $_SESSION['ref'] : Vars::$HOME_URL) ?>"><?= __('back') ?><i class="icn-arrow right"></i></a></li>
</ul>