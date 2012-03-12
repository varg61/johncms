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

if (stristr(Vars::$USER_AGENT, "msie") && stristr(Vars::$USER_AGENT, "windows")) {
    header("Cache-Control: no-store, no-cache, must-revalidate");
    header('Content-type: text/html; charset=UTF-8');
} else {
    header("Cache-Control: public");
    header('Content-type: application/xhtml+xml; charset=UTF-8');
    echo '<?xml version="1.0" encoding="utf-8"?>' . "\n";
}

?>
<!DOCTYPE html PUBLIC "-//WAPFORUM//DTD XHTML Mobile 1.0//EN" "http://www.wapforum.org/DTD/xhtml-mobile10.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="ru">
<head>
    <meta http-equiv="content-type" content="application/xhtml+xml; charset=utf-8"/>
    <meta http-equiv="Content-Style-Type" content="text/css"/>
    <meta name="Generator" content="JohnCMS, http://johncms.com"/>
    <meta name="keywords" content="<?= Vars::$SYSTEM_SET['meta_key'] ?>"/>
    <meta name="description" content="<?= Vars::$SYSTEM_SET['meta_desc'] ?>"/>
    <link rel="stylesheet" href="<?= Vars::$HOME_URL . '/templates/' . Vars::$USER_SET['skin'] ?>/style.css" type="text/css"/>
    <link rel="shortcut icon" href="<?= Vars::$HOME_URL ?>/favicon.ico"/>
    <link rel="alternate" type="application/rss+xml" title="<?= lng('site_news', 1) ?>" href="http://localhost/johncms/rss"/>
    <title><?= isset($this->title) ? $this->title : Vars::$SYSTEM_SET['copyright'] ?></title>
</head>
<body>
<table width="100%">
    <tr>
        <td valign="bottom">
            <a href="<?= Vars::$HOME_URL ?>"><?= Functions::getImage('logo.gif', Vars::$SYSTEM_SET['copyright']) ?></a>
        </td>
        <td align="right">
            <?php if (Vars::$PLACE == '' && count(Vars::$LNG_LIST) > 1) : ?>
            <a href="<?= Vars::$HOME_URL ?>/language"><b><?= strtoupper(Vars::$LNG_ISO) ?></b></a>&#160;<img src="<?= Vars::$HOME_URL ?>/images/flags/<?= Vars::$LNG_ISO ?>.gif" alt=""/>
            <?php endif ?>
        </td>
    </tr>
</table>
<div class="header"><?= lng('hi', 1) . ', ' . (Vars::$USER_ID ? '<b>' . Vars::$USER_DATA['nickname'] . '</b>' : lng('guest', 1)) ?>!</div>
<div class="tmn">
    <?php if (!empty(Vars::$PLACE) || Vars::$ACT) : ?>
    <a href="<?= Vars::$HOME_URL ?>"><?= lng('homepage', 1) ?></a> |
    <?php endif ?>
    <?php if (Vars::$USER_ID) : ?>
    <a href="<?= Vars::$HOME_URL ?>/cabinet"><?= lng('personal', 1) ?></a> |
    <a href="<?= Vars::$HOME_URL ?>/exit"><?= lng('exit', 1) ?></a>
    <?php else : ?>
    <a href="<?= Vars::$HOME_URL ?>/login"><?= lng('login', 1) ?></a> |
    <a href="<?= Vars::$HOME_URL ?>/registration"><?= lng('registration', 1) ?></a>
    <?php endif ?>
</div>

<div class="maintxt">
    <!-- Выводим основное содержимое -->
    <?= $this->contents ?>
</div>

<div class="fmenu">
    <?php if (!empty(Vars::$PLACE) || Vars::$ACT) : ?>
    <div><a href="<?= Vars::$HOME_URL ?>"><?= lng('homepage', 1) ?></a></div>
    <?php endif ?>
    <?php if (Vars::$USER_SET['quick_go']) : ?>
    <form action="<?= Vars::$HOME_URL ?>/redirect" method="post">
        <div>
            <select name="adres" style="font-size:x-small">
                <option selected="selected"><?= lng('quick_jump', 1) ?></option>
                <option value="guest"><?= lng('guestbook', 1) ?></option>
                <option value="forum"><?= lng('forum', 1) ?></option>
                <option value="news"><?= lng('news', 1) ?></option>
                <option value="gallery"><?= lng('gallery', 1) ?></option>
                <option value="down"><?= lng('downloads', 1) ?></option>
                <option value="lib"><?= lng('library', 1) ?></option>
                <option value="gazen">Gazenwagen :)</option>
            </select>
            <input type="submit" value="Go!" style="font-size:x-small"/>
        </div>
    </form>
    <?php endif ?>
</div>
<div class="footer"><?= Counters::usersOnline() ?></div>
<div style="text-align:center"><p><b><?= Vars::$SYSTEM_SET['copyright'] ?></b></p></div>
<div style="text-align:center">
    <small>Powered by <a href="http://johncms.com">JohnCMS</a></small>
</div>
</body>
</html>