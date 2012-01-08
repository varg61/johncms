<?php

/**
 * @package     JohnCMS
 * @link        http://johncms.com
 * @copyright   Copyright (C) 2008-2012 JohnCMS Community
 * @license     LICENSE.txt (see attached file)
 * @version     VERSION.txt (see attached file)
 * @author      http://johncms.com/about
 */

define('_IN_JOHNCMS', 1);

require_once('includes/core.php');

$referer = isset($_SERVER['HTTP_REFERER']) ? htmlspecialchars($_SERVER['HTTP_REFERER']) : Vars::$SYSTEM_SET['homeurl'];
$url = isset($_REQUEST['url']) ? strip_tags(html_entity_decode(base64_decode(trim($_REQUEST['url'])))) : false;

if (isset($_GET['lng'])) {
    /*
    -----------------------------------------------------------------
    Переключатель языков
    -----------------------------------------------------------------
    */
    require_once('includes/head.php');
    echo'<div class="menu"><form action="' . $referer . '" method="post"><p>';
    if (count(Vars::$LNG_LIST) > 1) {
        echo'<p><h3>' . Vars::$LNG['language_select'] . '</h3>';
        foreach (Vars::$LNG_LIST as $key => $val) {
            echo'<div><input type="radio" value="' . $key . '" name="setlng" ' . ($key == Vars::$LNG_ISO ? 'checked="checked"' : '') . '/>&#160;' .
                (file_exists('images/flags/' . $key . '.gif') ? '<img src="images/flags/' . $key . '.gif" alt=""/>&#160;' : '') .
                $val .
                ($key == Vars::$SYSTEM_SET['lng'] ? ' <small class="red">[' . Vars::$LNG['default'] . ']</small>' : '') .
                '</div>';
        }
        echo'</p>';
    }
    echo'</p><p><input type="submit" name="submit" value="' . Vars::$LNG['apply'] . '" /></p>' .
        '<p><a href="' . $referer . '">' . Vars::$LNG['back'] . '</a></p></form></div>';
    require_once('includes/end.php');
} elseif ($url) {
    /*
    -----------------------------------------------------------------
    Редирект по ссылкам в текстах, обработанным функцией tags()
    -----------------------------------------------------------------
    */
    if (isset($_POST['submit'])) {
        header('Location: ' . $url);
    } else {
        require_once('includes/head.php');
        echo'<div class="phdr"><b>' . Vars::$LNG['external_link'] . '</b></div>' .
            '<div class="rmenu">' .
            '<form action="go.php?url=' . base64_encode($url) . '" method="post">' .
            '<p>' . Vars::$LNG['redirect_1'] . ':<br /><span class="red">' . Validate::filterString($url) . '</span></p>' .
            '<p>' . Vars::$LNG['redirect_2'] . '.<br />' .
            Vars::$LNG['redirect_3'] . ' <span class="green">' . Vars::$SYSTEM_SET['homeurl'] . '</span> ' . Vars::$LNG['redirect_4'] . '.</p>' .
            '<p><input type="submit" name="submit" value="' . Vars::$LNG['redirect_5'] . '" /></p>' .
            '</form></div>' .
            '<div class="phdr"><a href="' . $referer . '">' . Vars::$LNG['back'] . '</a></div>';
        require_once('includes/end.php');
    }
} elseif (Vars::$ID) {
    /*
    -----------------------------------------------------------------
    Редирект по рекламной ссылке
    -----------------------------------------------------------------
    */
    $req = mysql_query("SELECT * FROM `cms_ads` WHERE `id` = " . Vars::$ID);
    if (mysql_num_rows($req)) {
        $res = mysql_fetch_assoc($req);
        $count_link = $res['count'] + 1;
        mysql_query("UPDATE `cms_ads` SET `count` = '$count_link'  WHERE `id` = " . Vars::$ID);
        header('Location: ' . $res['link']);
    } else {
        header("Location: http://mobicms.net/404.php");
    }
} else {
    /*
    -----------------------------------------------------------------
    Редирект по "быстрому переходу"
    -----------------------------------------------------------------
    */
    $adres = trim($_POST['adres']);
    switch ($adres) {
        case 'forum':
            header('location: ' . Vars::$SYSTEM_SET['homeurl'] . '/forum/index.php');
            break;

        case 'lib':
            header('location: ' . Vars::$SYSTEM_SET['homeurl'] . '/library/index.php');
            break;

        case 'down':
            header('location: ' . Vars::$SYSTEM_SET['homeurl'] . '/download/index.php');
            break;

        case 'gallery':
            header('location: ' . Vars::$SYSTEM_SET['homeurl'] . '/gallery/index.php');
            break;

        case 'news':
            header('location: ' . Vars::$SYSTEM_SET['homeurl'] . '/news/index.php');
            break;

        case 'guest':
            header('location: ' . Vars::$SYSTEM_SET['homeurl'] . '/guestbook/index.php');
            break;

        default :
            header('location: http://mobicms.net');
            break;
    }
}