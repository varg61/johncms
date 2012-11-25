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
    /*
    -----------------------------------------------------------------
    Массовое удаление выбранных постов форума
    -----------------------------------------------------------------
    */
    if (isset($_GET['yes'])) {
        $dc = $_SESSION['dc'];
        $prd = $_SESSION['prd'];
        foreach ($dc as $delid) {
            mysql_query("UPDATE `forum` SET
                `close` = '1',
                `close_who` = '" . mysql_real_escape_string(Vars::$USER_NICKNAME) . "'
                WHERE `id` = '" . intval($delid) . "'
            ");
        }
        echo __('mass_delete_confirm') . '<br/><a href="' . $prd . '">' . __('back') . '</a><br/>';
    } else {
        if (empty($_POST['delch'])) {
            echo '<p>' . __('error_mass_delete') . '<br/><a href="' . htmlspecialchars(getenv("HTTP_REFERER")) . '">' . __('back') . '</a></p>';
            exit;
        }
        foreach ($_POST['delch'] as $v) {
            $dc[] = intval($v);
        }
        $_SESSION['dc'] = $dc;
        $_SESSION['prd'] = htmlspecialchars(getenv("HTTP_REFERER"));
        echo '<p>' . __('delete_confirmation') . '<br/><a href="' . Vars::$URI . '?act=massdel&amp;yes">' . __('delete') . '</a> | ' .
             '<a href="' . htmlspecialchars(getenv("HTTP_REFERER")) . '">' . __('cancel') . '</a></p>';
    }
}