<?php

/**
 * @package     JohnCMS
 * @link        http://johncms.com
 * @copyright   Copyright (C) 2008-2012 JohnCMS Community
 * @license     LICENSE.txt (see attached file)
 * @version     VERSION.txt (see attached file)
 * @author      http://johncms.com/about
 *
 * Шаблон сайта по-умолчанию
 */

defined('_IN_JOHNCMS') or die('Error: restricted access');
$this->httpHeaders()

?>
<!DOCTYPE html PUBLIC "-//WAPFORUM//DTD XHTML Mobile 1.0//EN" "http://www.wapforum.org/DTD/xhtml-mobile10.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="ru">
<head>
    <meta http-equiv="content-type" content="application/xhtml+xml; charset=utf-8"/>
    <meta http-equiv="Content-Style-Type" content="text/css"/>
    <meta name="Generator" content="JohnCMS, http://johncms.com"/>
    <meta name="keywords" content="<?= Vars::$SYSTEM_SET['meta_key'] ?>"/>
    <meta name="description" content="<?= Vars::$SYSTEM_SET['meta_desc'] ?>"/>
    <link rel="stylesheet" href="<?= Vars::$HOME_URL . '/theme/' . Vars::$USER_SET['skin'] ?>/style.css" type="text/css"/>
    <link rel="shortcut icon" href="<?= Vars::$HOME_URL ?>/favicon.ico"/>
    <link rel="alternate" type="application/rss+xml" title="<?= Vars::$LNG['site_news'] ?>" href="http://localhost/johncms/rss/rss.php"/>
    <title><?= isset($this->title) ? $this->title : Vars::$SYSTEM_SET['copyright'] ?></title>
</head>
<body>
<table width="100%">
    <tr>
        <td valign="bottom">
            <!-- Логотип сайта -->
            <a href="<?= Vars::$HOME_URL ?>"><?= Functions::getImage('logo.gif', Vars::$SYSTEM_SET['copyright']) ?></a>
        </td>
        <td align="right">
            <!-- Переключатель языков -->
            <?= $this->languageSwitch(1) ?>
        </td>
    </tr>
</table>
<!-- Приветствие пользователю -->
<div class="header"><?= $this->userGreeting() ?>!</div>
<!-- Пользовательское меню -->
<div class="tmn"><?= $this->userMenu() ?></div>

<div class="maintxt">
    <!-- Выводим основное содержимое -->
    <?= $this->contents ?>
</div>

<div class="fmenu">
    <!-- Ссылка на Главную -->
    <?= $this->homeLink() ?>
    <!-- Меню быстрого перехода -->
    <?= $this->quickGo() ?>
</div>
<div class="footer"><?= Counters::usersOnline() ?></div>
<div style="text-align:center"><p><b><?= Vars::$SYSTEM_SET['copyright'] ?></b></p></div>
<div style="text-align:center">
    <small>Powered by <a href="http://johncms.com">JohnCMS</a></small>
</div>
</body>
</html>