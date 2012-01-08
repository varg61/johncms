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
if (Vars::$USER_RIGHTS == 3 || Vars::$USER_RIGHTS >= 6) {
    if (!Vars::$ID) {
        require_once('../includes/head.php');
        echo Functions::displayError(Vars::$LNG['error_wrong_data']);
        require_once('../includes/end.php');
        exit;
    }
    $typ = mysql_query("SELECT * FROM `forum` WHERE `id` = " . Vars::$ID);
    $ms = mysql_fetch_assoc($typ);
    if ($ms[type] != "t") {
        require_once('../includes/head.php');
        echo Functions::displayError(Vars::$LNG['error_wrong_data']);
        require_once('../includes/end.php');
        exit;
    }
    if (isset($_POST['submit'])) {
        $nn = isset($_POST['nn']) ? Validate::filterString($_POST['nn']) : false;
        if (!$nn) {
            require_once('../includes/head.php');
            echo Functions::displayError($lng_forum['error_topic_name'], '<a href="index.php?act=ren&amp;id=' . Vars::$ID . '">' . Vars::$LNG['repeat'] . '</a>');
            require_once('../includes/end.php');
            exit;
        }
        // Проверяем, есть ли тема с таким же названием?
        $pt = mysql_query("SELECT * FROM `forum` WHERE `type` = 't' AND `refid` = '" . $ms['refid'] . "' and text='" . mysql_real_escape_string($nn) . "' LIMIT 1");
        if (mysql_num_rows($pt) != 0) {
            require_once('../includes/head.php');
            echo Functions::displayError($lng_forum['error_topic_exists'], '<a href="index.php?act=ren&amp;id=' . Vars::$ID . '">' . Vars::$LNG['repeat'] . '</a>');
            require_once('../includes/end.php');
            exit;
        }
        mysql_query("update `forum` set  `text` = '" . mysql_real_escape_string($nn) . "' where `id` = " . Vars::$ID);
        header("Location: index.php?id=" . Vars::$ID);
    } else {
        /*
        -----------------------------------------------------------------
        Переименовываем тему
        -----------------------------------------------------------------
        */
        require_once('../includes/head.php');
        echo '<div class="phdr"><a href="index.php?id=' . Vars::$ID . '"><b>' . Vars::$LNG['forum'] . '</b></a> | ' . $lng_forum['topic_rename'] . '</div>' .
             '<div class="menu"><form action="index.php?act=ren&amp;id=' . Vars::$ID . '" method="post">' .
             '<p><h3>' . $lng_forum['topic_name'] . '</h3>' .
             '<input type="text" name="nn" value="' . $ms['text'] . '"/></p>' .
             '<p><input type="submit" name="submit" value="' . Vars::$LNG['save'] . '"/></p>' .
             '</form></div>' .
             '<div class="phdr"><a href="index.php?id=' . Vars::$ID . '">' . Vars::$LNG['back'] . '</a></div>';
    }
} else {
    require_once('../includes/head.php');
    echo Functions::displayError(Vars::$LNG['access_forbidden']);
}