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
    <link rel="alternate" type="application/rss+xml" title="<?= Vars::$LNG['site_news'] ?>" href="http://localhost/johncms/rss"/>
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
<div class="header"><?= Vars::$LNG['hi'] . ', ' . (Vars::$USER_ID ? '<b>' . Vars::$USER_DATA['nickname'] . '</b>' : Vars::$LNG['guest']) ?>!</div>
<div class="tmn">
    <?php if (!empty(Vars::$PLACE) || Vars::$ACT) : ?>
    <a href="<?= Vars::$HOME_URL ?>"><?= Vars::$LNG['homepage'] ?></a> |
    <?php endif ?>
    <?php if (Vars::$USER_ID) : ?>
    <a href="<?= Vars::$HOME_URL ?>/users/cabinet"><?= Vars::$LNG['personal'] ?></a> |
    <a href="<?= Vars::$HOME_URL ?>/exit"><?= Vars::$LNG['exit'] ?></a>
    <?php else : ?>
    <a href="<?= Vars::$HOME_URL ?>/login"><?= Vars::$LNG['login'] ?></a> |
    <a href="<?= Vars::$HOME_URL ?>/registration"><?= Vars::$LNG['registration'] ?></a>
    <?php endif ?>
</div>

<div class="maintxt">
    <!-- Выводим основное содержимое -->
    <?= $this->contents ?>
</div>

<div class="fmenu">
    <?php if (!empty(Vars::$PLACE) || Vars::$ACT) : ?>
    <div><a href="<?= Vars::$HOME_URL ?>"><?= Vars::$LNG['homepage'] ?></a></div>
    <?php endif ?>
    <?php if (Vars::$USER_SET['quick_go']) : ?>
    <form action="<?= Vars::$HOME_URL ?>/go.php" method="post">
        <div>
            <select name="adres" style="font-size:x-small">
                <option selected="selected"><?= Vars::$LNG['quick_jump'] ?></option>
                <option value="guest"><?= Vars::$LNG['guestbook'] ?></option>
                <option value="forum"><?= Vars::$LNG['forum'] ?></option>
                <option value="news"><?= Vars::$LNG['news'] ?></option>
                <option value="gallery"><?= Vars::$LNG['gallery'] ?></option>
                <option value="down"><?= Vars::$LNG['downloads'] ?></option>
                <option value="lib"><?= Vars::$LNG['library'] ?></option>
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