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

require_once('includes/core.php');


include(Vars::$MODULE_INCLUDE);












//$tpl = Template::getInstance();
//
//if (isset($_SESSION['ref']))
//    unset($_SESSION['ref']);
//
//switch (Vars::$ACT) {
//    case 'lng':
//        break;
//
//
//    case 'digest':
//        /*
//        -----------------------------------------------------------------
//        Дайджест
//        -----------------------------------------------------------------
//        */
//        //TODO: Добавить поздравление с днем рожденья и информацию по Карме
//        if (!Vars::$USER_ID) {
//            echo Functions::displayError(Vars::$LNG['access_guest_forbidden']);
//            exit;
//        }
//        // Дайджест Администратора
//        if (Vars::$USER_RIGHTS) {
//            $tpl->reg_total = mysql_result(mysql_query("SELECT COUNT(*) FROM `users` WHERE `level` = 0"), 0);
//            $tpl->ban_total = mysql_result(mysql_query("SELECT COUNT(*) FROM `cms_ban_users` WHERE `ban_time` > '" . time() . "'"), 0);
//            //$total_admin = Counters::guestbookCount(2);
//        }
//        // Дайджест юзеров
//        $total_news = mysql_result(mysql_query("SELECT COUNT(*) FROM `news` WHERE `time` > " . (time() - 86400)), 0);
//        $total_forum = Counters::forumMessagesNew();
//        //$total_guest = Counters::guestbookCount(1);
//        //$total_gal = Counters::galleryCount(1);
//        //$total_lib = mysql_result(mysql_query("SELECT COUNT(*) FROM `lib` WHERE `type` = 'bk' AND `moder` = 1 AND `time` > " . (time() - 259200)), 0);
//        //$total_album = mysql_result(mysql_query("SELECT COUNT(*) FROM `cms_album_files` WHERE `time` > '" . (time() - 259200) . "' AND `access` > '1'"), 0);
//        $tpl->last = isset($_GET['last']) ? intval($_GET['last']) : Vars::$USER_DATA['lastdate'];
//        $tpl->count = new Counters();
//        $tpl->contents = $tpl->includeTpl('digest');
//        break;
//
//    default:
//        /*
//        -----------------------------------------------------------------
//        Главное меню сайта
//        -----------------------------------------------------------------
//        */
//        if (isset($_SESSION['ref']))
//            unset($_SESSION['ref']);
//
//        // Загружаем шаблон вывода
//        $tpl->mp = new HomePage();
//        $tpl->count = new Counters();
//        $tpl->contents = $tpl->includeTpl('mainmenu');
//
//        /*
//        -----------------------------------------------------------------
//        Карта сайта
//        -----------------------------------------------------------------
//        */
//        if (isset(Vars::$SYSTEM_SET['sitemap'])) {
//            $set_map = unserialize(Vars::$SYSTEM_SET['sitemap']);
//            if (($set_map['forum'] || $set_map['lib']) && ($set_map['users'] || !Vars::$USER_ID) && ($set_map['browsers'] || !Vars::$IS_MOBILE)) {
//                $map = new SiteMap();
//                echo '<div class="sitemap">' . $map->mapGeneral() . '</div>';
//            }
//        }
//}