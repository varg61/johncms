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

    // Проверяем, существует ли тема
    $req = DB::PDO()->query("SELECT * FROM `forum` WHERE `id` = " . Vars::$ID . " AND `type` = 't'");
    if (!$req->rowCount()) {
        echo Functions::displayError(__('error_topic_deleted'));
        exit;
    }
    $res = $req->fetch();
    if (isset($_POST['submit'])) {
        $del = isset($_POST['del']) ? intval($_POST['del']) : NULL;
        if ($del == 2 && Vars::$USER_RIGHTS == 9) {
            /*
            -----------------------------------------------------------------
            Удаляем топик
            -----------------------------------------------------------------
            */
            $req1 = DB::PDO()->query("SELECT * FROM `cms_forum_files` WHERE `topic` = " . Vars::$ID);
            if ($req1->rowCount()) {
                while ($res1 = $req1->fetch()) {
                    unlink(ROOTPATH . 'files' . DIRECTORY_SEPARATOR . 'forum' . DIRECTORY_SEPARATOR . $res1['filename']);
                }
                DB::PDO()->exec("DELETE FROM `cms_forum_files` WHERE `topic` = " . Vars::$ID);
                DB::PDO()->query("OPTIMIZE TABLE `cms_forum_files`");
            }
            DB::PDO()->exec("DELETE FROM `forum` WHERE `refid` = " . Vars::$ID);
            DB::PDO()->exec("DELETE FROM `forum` WHERE `id` = " . Vars::$ID);
        } elseif ($del = 1) {
            /*
            -----------------------------------------------------------------
            Скрываем топик
            -----------------------------------------------------------------
            */
            $nick = DB::PDO()->quote(Vars::$USER_NICKNAME);
            DB::PDO()->exec("UPDATE `forum` SET `close` = '1', `close_who` = '" . $nick . "' WHERE `id` = " . Vars::$ID);
            DB::PDO()->exec("UPDATE `cms_forum_files` SET `del` = '1' WHERE `topic` = " . Vars::$ID);
        }
        header('Location: ' . $url . '?id=' . $res['refid']);
    } else {
        /*
        -----------------------------------------------------------------
        Меню выбора режима удаления темы
        -----------------------------------------------------------------
        */
        echo '<div class="phdr"><a href="' . $url . '?id=' . Vars::$ID . '"><b>' . __('forum') . '</b></a> | ' . __('topic_delete') . '</div>' .
             '<div class="rmenu"><form method="post" action="' . $url . '?act=deltema&amp;id=' . Vars::$ID . '">' .
             '<p><h3>' . __('delete_confirmation') . '</h3>' .
             '<input type="radio" value="1" name="del" checked="checked"/>&#160;' . __('hide') . '<br />' .
             (Vars::$USER_RIGHTS == 9 ? '<input type="radio" value="2" name="del" />&#160;' . __('delete') : '') .
             '</p><p><input type="submit" name="submit" value="' . __('do') . '" /></p>' .
             '<p><a href="' . $url . '?id=' . Vars::$ID . '">' . __('cancel') . '</a>' .
             '</p></form></div>' .
             '<div class="phdr">&#160;</div>';
    }
} else {
    echo Functions::displayError(__('access_forbidden'));
}