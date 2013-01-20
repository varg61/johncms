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

        $STH = $STH = DB::PDO()->prepare('
            UPDATE `forum` SET
            `close` = 1,
            `close_who` = ?
            WHERE `id` = ?
        ');
        foreach ($dc as $delid) {
            $STH->execute(array(Vars::$USER_NICKNAME, intval($delid)));
        }
        $STH = NULL;

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
        echo '<p>' . __('delete_confirmation') . '<br/><a href="' . Router::getUri(2) . '?act=massdel&amp;yes">' . __('delete') . '</a> | ' .
            '<a href="' . htmlspecialchars(getenv("HTTP_REFERER")) . '">' . __('cancel') . '</a></p>';
    }
}