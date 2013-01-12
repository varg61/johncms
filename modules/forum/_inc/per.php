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
    $typ = mysql_query("SELECT * FROM `forum` WHERE `id` = " . Vars::$ID . " AND `type` = 't'");
    if (!mysql_num_rows($typ)) {
        echo Functions::displayError(__('error_wrong_data'));
        exit;
    }
    if (isset($_POST['submit'])) {
        $razd = isset($_POST['razd']) ? abs(intval($_POST['razd'])) : FALSE;
        if (!$razd) {
            echo Functions::displayError(__('error_wrong_data'));
            exit;
        }
        $typ1 = mysql_query("SELECT * FROM `forum` WHERE `id` = '$razd' AND `type` = 'r'");
        if (!mysql_num_rows($typ1)) {
            echo Functions::displayError(__('error_wrong_data'));
            exit;
        }
        mysql_query("UPDATE `forum` SET `refid` = '$razd' WHERE `id` = " . Vars::$ID);
        header("Location: " . $url . "?id=" . Vars::$ID);
    } else {
        /*
        -----------------------------------------------------------------
        Перенос темы
        -----------------------------------------------------------------
        */
        $ms = mysql_fetch_assoc($typ);
        if (empty($_GET['other'])) {
            $rz = mysql_query("select * from `forum` where id='" . $ms['refid'] . "';");
            $rz1 = mysql_fetch_assoc($rz);
            $other = $rz1['refid'];
        } else {
            $other = intval($_GET['other']);
        }
        $fr = mysql_query("select * from `forum` where id='" . $other . "';");
        $fr1 = mysql_fetch_assoc($fr);
        echo'<div class="phdr"><a href="' . $url . '?id=' . Vars::$ID . '"><b>' . __('forum') . '</b></a> | ' . __('topic_move') . '</div>' .
            '<form action="' . $url . '?act=per&amp;id=' . Vars::$ID . '" method="post">' .
            '<div class="gmenu"><p>' .
            '<h3>' . __('category') . '</h3>' . $fr1['text'] . '</p>';

        $raz = mysql_query("SELECT * FROM `forum` WHERE `refid` = '$other' AND `type` = 'r' AND `id` != '" . $ms['refid'] . "' ORDER BY `realid` ASC");
        if (mysql_num_rows($raz)) {
            echo'<p><h3>' . __('section') . '</h3>' .
                '<select name="razd">';
            while ($raz1 = mysql_fetch_assoc($raz)) {
                echo '<option value="' . $raz1['id'] . '">' . $raz1['text'] . '</option>';
            }
            echo'</select></p>';
        }
        echo'<p><input type="submit" name="submit" value="' . __('move') . '"/></p>' .
            '</div></form>' .
            '<div class="phdr">' . __('other_categories') . '</div>';
        $frm = mysql_query("SELECT * FROM `forum` WHERE `type` = 'f' AND `id` != '$other' ORDER BY `realid` ASC");
        for ($i = 0; $frm1 = mysql_fetch_assoc($frm); ++$i) {
            echo $i % 2 ? '<div class="list2">' : '<div class="list1">';
            echo '<a href="' . $url . '?act=per&amp;id=' . Vars::$ID . '&amp;other=' . $frm1['id'] . '">' . $frm1['text'] . '</a></div>';
        }
        echo '<div class="phdr"><a href="' . $url . '">' . __('back') . '</a></div>';
    }
}