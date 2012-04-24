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
        echo lng('mass_delete_confirm') . '<br/><a href="' . $prd . '">' . lng('back') . '</a><br/>';
    } else {
        if (empty($_POST['delch'])) {
            echo '<p>' . lng('error_mass_delete') . '<br/><a href="' . htmlspecialchars(getenv("HTTP_REFERER")) . '">' . lng('back') . '</a></p>';
            exit;
        }
        foreach ($_POST['delch'] as $v) {
            $dc[] = intval($v);
        }
        $_SESSION['dc'] = $dc;
        $_SESSION['prd'] = htmlspecialchars(getenv("HTTP_REFERER"));
        echo '<p>' . lng('delete_confirmation') . '<br/><a href="index.php?act=massdel&amp;yes">' . lng('delete') . '</a> | ' .
             '<a href="' . htmlspecialchars(getenv("HTTP_REFERER")) . '">' . lng('cancel') . '</a></p>';
    }
}