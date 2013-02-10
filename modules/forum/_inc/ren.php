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
        echo Functions::displayError(__('error_wrong_data'));
        exit;
    }
    $url = Router::getUri(2);
    $typ = DB::PDO()->query("SELECT * FROM `forum` WHERE `id` = " . Vars::$ID);
    $ms = $typ->fetch();
    if ($ms['type'] != "t") {
        echo Functions::displayError(__('error_wrong_data'));
        exit;
    }
    if (isset($_POST['submit'])) {
        $nn = isset($_POST['nn']) ? Functions::checkout($_POST['nn']) : FALSE;
        if (!$nn) {
            echo Functions::displayError(__('error_topic_name'), '<a href="' . $url . '?act=ren&amp;id=' . Vars::$ID . '">' . __('repeat') . '</a>');
            exit;
        }

        // Проверяем, есть ли тема с таким же названием?
        $STH = $STH = DB::PDO()->prepare('
            SELECT * FROM `forum`
            WHERE `type` = ?
            AND `refid` = ?
            AND `text` = ?
        ');
        $STH->execute(array('t', $ms['refid'], $nn));
        if ($STH->rowCount()) {
            echo Functions::displayError(__('error_topic_exists'), '<a href="' . $url . '?act=ren&amp;id=' . Vars::$ID . '">' . __('repeat') . '</a>');
            exit;
        }
        $STH = NULL;

        $STH = $STH = DB::PDO()->prepare('
            UPDATE `forum` SET
            `text` = ?
            WHERE `id` = ?
        ');
        $STH->execute(array($nn, Vars::$ID));
        $STH = null;

        header("Location: " . $url . "?id=" . Vars::$ID);
    } else {
        /*
        -----------------------------------------------------------------
        Переименовываем тему
        -----------------------------------------------------------------
        */
        echo '<div class="phdr"><a href="' . $url . '?id=' . Vars::$ID . '"><b>' . __('forum') . '</b></a> | ' . __('topic_rename') . '</div>' .
            '<div class="menu"><form action="' . $url . '?act=ren&amp;id=' . Vars::$ID . '" method="post">' .
            '<p><h3>' . __('topic_name') . '</h3>' .
            '<input type="text" name="nn" value="' . $ms['text'] . '"/></p>' .
            '<p><input type="submit" name="submit" value="' . __('save') . '"/></p>' .
            '</form></div>' .
            '<div class="phdr"><a href="' . $url . '?id=' . Vars::$ID . '">' . __('back') . '</a></div>';
    }
} else {
    echo Functions::displayError(__('access_forbidden'));
}