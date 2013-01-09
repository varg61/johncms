<?php

/**
 * @package     JohnCMS
 * @link        http://johncms.com
 * @copyright   Copyright (C) 2008-2012 JohnCMS Community
 * @license     LICENSE.txt (see attached file)
 * @version     VERSION.txt (see attached file)
 * @author      http://johncms.com/about
 */

defined('_IN_JOHNCMS') or die('Error: restricted access');

$referer = isset($_SERVER['HTTP_REFERER']) ? htmlspecialchars($_SERVER['HTTP_REFERER']) : Vars::$HOME_URL;
$url = isset($_REQUEST['url']) ? strip_tags(rawurldecode(trim($_REQUEST['url']))) : false;

if ($url) {
    /*
    -----------------------------------------------------------------
    Редирект по ссылкам в текстах, обработанным функцией tags()
    -----------------------------------------------------------------
    */
    if (isset($_POST['submit'])) {
        header('Location: ' . $url);
    } else {
        echo '<div class="phdr"><b>' . __('external_link') . '</b></div>' .
            '<div class="rmenu">' .
            '<form action="go.php?url=' . rawurlencode($url) . '" method="post">' .
            '<p>' . __('redirect_1') . ':<br /><span class="red">' . htmlspecialchars($url) . '</span></p>' .
            '<p>' . __('redirect_2') . '.<br />' .
            __('redirect_3') . ' <span class="green">' . Vars::$HOME_URL . '</span> ' . __('redirect_4') . '.</p>' .
            '<p><input type="submit" name="submit" value="' . __('redirect_5') . '" /></p>' .
            '</form></div>' .
            '<div class="phdr"><a href="' . $referer . '">' . __('back') . '</a></div>';
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
            header('location: ' . Vars::$HOME_URL . 'forum/');
            break;

        case 'lib':
            header('location: ' . Vars::$HOME_URL . 'library/');
            break;

        case 'down':
            header('location: ' . Vars::$HOME_URL . 'download/');
            break;

        case 'news':
            header('location: ' . Vars::$HOME_URL . 'news/');
            break;

        case 'guest':
            header('location: ' . Vars::$HOME_URL . 'guestbook/');
            break;

        default :
            header('location: http://gazenwagen.com');
            break;
    }
}