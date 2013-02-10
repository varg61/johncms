<?php
defined('_IN_JOHNCMS') or die('Error: restricted access');
echo '<?xml version="1.0" encoding="utf-8"?>' . "\n";
?>
<!DOCTYPE html PUBLIC "-//WAPFORUM//DTD XHTML Mobile 1.2//EN" "http://www.openmobilealliance.org/tech/DTD/xhtml-mobile12.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <meta name="HandheldFriendly" content="true"/>
    <meta name="MobileOptimized" content="width"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0"/>
    <meta name="keywords" content="<?= Functions::checkout(Vars::$SYSTEM_SET['meta_key']) ?>"/>
    <meta name="description" content="<?= Functions::checkout(Vars::$SYSTEM_SET['meta_desc']) ?>"/>
    <?= $this->loadCSS() ?>
    <link rel="shortcut icon" href="<?= Vars::$HOME_URL ?>favicon.ico"/>
    <link rel="alternate" type="application/rss+xml" title="<?= __('site_news', 1) ?>" href="http://localhost/johncms/rss"/>
    <title><?= isset($this->title) ? $this->title : Vars::$SYSTEM_SET['hometitle'] ?></title>
    <?php if (!Vars::$IS_MOBILE): ?>
    <script type="text/javascript" src="//ajax.googleapis.com/ajax/libs/jquery/1.9.0/jquery.min.js"></script>
    <?php endif ?>
</head>
<body>
<div id="container">
    <a name="top"></a>

    <!-- Верхний блок с логотипом -->
    <ul class="attic">
        <li class="logo"><a href="<?= Vars::$HOME_URL ?>"><?= Functions::loadImage('logo.png', 24, '', 'JohnCMS') ?></a></li>
        <li class="tools"></li>
    </ul>

    <!-- Верхняя панель инструментов -->
    <ul class="top">
        <li<?= (empty(Vars::$PLACE) && !Vars::$ACT ? ' class="select"' : '') ?>><a href="<?= Vars::$HOME_URL ?>" title="<?= __('homepage') ?>"><span class="icn icn-home"></span></a></li>
        <?php if (Vars::$USER_ID): ?>
        <li<?= (Vars::$PLACE == 'mail' ? ' class="select"' : '') ?>><a href="<?= Vars::$HOME_URL ?>mail/" title="<?= __('mail') ?>"><span class="icn icn-mail"></span></a></li>
        <li<?= (Vars::$PLACE == 'users/' . Vars::$USER_ID . '/menu' ? ' class="select"' : '') ?>><a href="<?= Vars::$HOME_URL ?>users/<?= Vars::$USER_ID ?>/menu/" title="<?= __('personal') ?>"><span class="icn icn-user"></span></a></li>
        <?php else: ?>
        <li<?= (Vars::$PLACE == 'users/login' ? ' class="select"' : '') ?>><a href="<?= Vars::$HOME_URL ?>users/login/" title="<?= __('login') ?>"><span class="icn icn-user"></span></a></li>
        <?php endif ?>
    </ul>

    <!-- Содержимое -->
    <?= $this->contents ?>

    <!-- Нижняя панель инструментов -->
    <ul class="bottom">
        <?php if (Vars::$USER_ID || Vars::$USER_SYS['view_online']): ?>
        <li><a href="<?= Vars::$HOME_URL ?>online/"><i class="icn-w-man-woman"></i><?= Counters::usersOnline() ?> :: <?= Counters::guestaOnline() ?></a></li>
        <?php else: ?>
        <li><i class="icn-w-man-woman"></i><?= Counters::usersOnline() ?> :: <?= Counters::guestaOnline() ?></li>
        <?php endif ?>
        <li></li>
        <li><a href="#top"><i class="icn-w-top"></i><?= __('up') ?></a></li>
    </ul>

    <!-- Информация внизу страницы -->
    <ul class="basement">
        <li><?= Functions::checkout(Vars::$SYSTEM_SET['copyright'], 1, 1, 1) ?></li>
        <li class="profiler">
            <?php if (Vars::$SYSTEM_SET['generation']): ?>
            <div>Generation: <?= round((microtime(TRUE) - START_TIME), 4) ?> sec</div>
            <?php endif ?>
            <?php if (Vars::$SYSTEM_SET['memory']): ?>
            <div>Memory: <?= round((memory_get_usage() - START_MEMORY) / 1024, 2) ?> kb</div>
            <?php endif ?>
        </li>
        <li class="counters"><?= Functions::displayCounters() ?></li>
        <li><a href="http://johncms.com">JohnCMS</a></li>
    </ul>
</div>
</body>
</html>