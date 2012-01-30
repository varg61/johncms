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

require_once('../includes/core.php');
$tpl = Template::getInstance();

if (!Vars::$USER_ID && !Vars::$SYSTEM_SET['active']) {
    echo Functions::displayError(Vars::$LNG['access_guest_forbidden']);
    exit;
}

switch (Vars::$ACT) {
    /*
     * Списки пользователей и администрации
     */
    case 'userlist':
        $tpl->menu = array(
            (Vars::$MOD != 'adm' ? Vars::$LNG['users'] : '<a href="index.php?act=userlist">' . Vars::$LNG['users'] . '</a>'),
            (Vars::$MOD == 'adm' ? Vars::$LNG['administration'] : '<a href="index.php?act=userlist&amp;mod=adm">' . Vars::$LNG['administration'] . '</a>')
        );
        $total = mysql_result(mysql_query("SELECT COUNT(*) FROM `users` WHERE `" . (Vars::$MOD == 'adm' ? 'rights' : 'level') . "` > 0"), 0);
        $tpl->total = $total;
        Vars::fixPage($total);

//        if ($total) {
//            $req = mysql_query("SELECT * FROM `users` WHERE " . (Vars::$MOD == 'adm' ? '`rights` > 0 ORDER BY `rights`' : '`level` > 0 ORDER BY `id`') . " DESC" . Vars::db_pagination());
//            for ($i = 0; $res = mysql_fetch_assoc($req); $i++) {
//                echo $i % 2 ? '<div class="list2">' : '<div class="list1">';
//                echo Functions::displayUser($res) . '</div>';
//            }
//        } else {
//            echo '<div class="menu"><p>' . Vars::$LNG['list_empty'] . '</p></div>';
//        }
//
//        echo '<div class="phdr">' . Vars::$LNG['total'] . ': ' . $total . '</div>';
//        if ($total > Vars::$USER_SET['page_size']) {
//            echo'<div class="topmenu">' . Functions::displayPagination('userlist.php?', Vars::$START, $total, Vars::$USER_SET['page_size']) . '</div>' .
//                '<p><form action="userlist.php" method="post">' .
//                '<input type="text" name="page" size="2"/>' .
//                '<input type="submit" value="' . Vars::$LNG['to_page'] . ' &gt;&gt;"/>' .
//                '</form></p>';
//        }
//        echo '<p><a href="search.php">' . Vars::$LNG['search_user'] . '</a><br />' .
//            '<a href="index.php">' . Vars::$LNG['back'] . '</a></p>';
        break;

    /*
     * Главное меню раздела пользователей
     */
    default:
        $tpl->count    = new Counters();
        $tpl->contents = $tpl->includeTpl('users/index');
}