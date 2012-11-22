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
        echo Functions::displayError(lng('error_wrong_data'));
        exit;
    }
    $typ = mysql_query("SELECT * FROM `forum` WHERE `id` = " . Vars::$ID);
    $ms = mysql_fetch_assoc($typ);
    if ($ms['type'] != "t") {
        echo Functions::displayError(lng('error_wrong_data'));
        exit;
    }
    if (isset($_POST['submit'])) {
        $nn = isset($_POST['nn']) ? Validate::checkout($_POST['nn']) : false;
        if (!$nn) {
            echo Functions::displayError(lng('error_topic_name'), '<a href="' . Vars::$URI . '?act=ren&amp;id=' . Vars::$ID . '">' . lng('repeat') . '</a>');
            exit;
        }
        // Проверяем, есть ли тема с таким же названием?
        $pt = mysql_query("SELECT * FROM `forum` WHERE `type` = 't' AND `refid` = '" . $ms['refid'] . "' and text='" . mysql_real_escape_string($nn) . "' LIMIT 1");
        if (mysql_num_rows($pt) != 0) {
            echo Functions::displayError(lng('error_topic_exists'), '<a href="' . Vars::$URI . '?act=ren&amp;id=' . Vars::$ID . '">' . lng('repeat') . '</a>');
            exit;
        }
        mysql_query("update `forum` set  `text` = '" . mysql_real_escape_string($nn) . "' where `id` = " . Vars::$ID);
        header("Location: " . Vars::$URI . "?id=" . Vars::$ID);
    } else {
        /*
        -----------------------------------------------------------------
        Переименовываем тему
        -----------------------------------------------------------------
        */
        echo '<div class="phdr"><a href="' . Vars::$URI . '?id=' . Vars::$ID . '"><b>' . lng('forum') . '</b></a> | ' . lng('topic_rename') . '</div>' .
             '<div class="menu"><form action="' . Vars::$URI . '?act=ren&amp;id=' . Vars::$ID . '" method="post">' .
             '<p><h3>' . lng('topic_name') . '</h3>' .
             '<input type="text" name="nn" value="' . $ms['text'] . '"/></p>' .
             '<p><input type="submit" name="submit" value="' . lng('save') . '"/></p>' .
             '</form></div>' .
             '<div class="phdr"><a href="' . Vars::$URI . '?id=' . Vars::$ID . '">' . lng('back') . '</a></div>';
    }
} else {
    echo Functions::displayError(lng('access_forbidden'));
}