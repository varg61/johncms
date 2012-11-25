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

if (!Vars::$USER_ID) {
    header('Location: ' . Vars::$URI);
    exit;
}

//TODO: Переделать с $do на $mod

if (Vars::$ID) {
    /*
    -----------------------------------------------------------------
    Показываем общий список тех, кто в выбранной теме
    -----------------------------------------------------------------
    */
    $req = mysql_query("SELECT `text` FROM `forum` WHERE `id` = " . Vars::$ID . " AND `type` = 't'");
    if (mysql_num_rows($req)) {
        $res = mysql_fetch_assoc($req);
        echo '<div class="phdr"><b>' . __('who_in_topic') . ':</b> <a href="' . Vars::$URI . '?id=' . Vars::$ID . '">' . $res['text'] . '</a></div>';
        if (Vars::$USER_RIGHTS > 0)
            echo '<div class="topmenu">' . ($do == 'guest' ? '<a href="' . Vars::$URI . '?act=who&amp;id=' . Vars::$ID . '">' . __('authorized') . '</a> | ' . __('guests')
                    : __('authorized') . ' | <a href="' . Vars::$URI . '?act=who&amp;do=guest&amp;id=' . Vars::$ID . '">' . __('guests') . '</a>') . '</div>';
        $total = mysql_result(mysql_query("SELECT COUNT(*) FROM `" . ($do == 'guest' ? 'cms_sessions' : 'users') . "` WHERE `last_visit` > " . (time() - 300) . " AND `place` = 'forum," . Vars::$ID . "'"), 0);
        if ($total) {
            $req = mysql_query("SELECT * FROM `" . ($do == 'guest' ? 'cms_sessions' : 'users') . "` WHERE `last_visit` > " . (time() - 300) . " AND `place` = 'forum," . Vars::$ID . "' ORDER BY " . ($do == 'guest' ? "`movings` DESC"
                                       : "`name` ASC") . " " . Vars::db_pagination());
            while (($res = mysql_fetch_assoc($req)) !== FALSE) {
                echo $i % 2 ? '<div class="list2">' : '<div class="list1">';
                Vars::$USER_SET['avatar'] = 0;
                echo Functions::displayUser($res, 0, (Vars::$ACT == 'guest' || (Vars::$USER_RIGHTS >= 1 && Vars::$USER_RIGHTS >= $res['rights']) ? 1 : 0));
                echo '</div>';
                ++$i;
            }
        } else {
            echo '<div class="menu"><p>' . __('list_empty') . '</p></div>';
        }
    } else {
        header('Location: ' . Vars::$URI);
    }
    echo '<div class="phdr">' . __('total') . ': ' . $total . '</div>' .
         '<p><a href="' . Vars::$URI . '?id=' . Vars::$ID . '">' . __('to_topic') . '</a></p>';
} else {
    /*
    -----------------------------------------------------------------
    Показываем общий список тех, кто в форуме
    -----------------------------------------------------------------
    */
    echo '<div class="phdr"><a href="' . Vars::$URI . '"><b>' . __('forum') . '</b></a> | ' . __('who_in_forum') . '</div>';
    if (Vars::$USER_RIGHTS > 0)
        echo '<div class="topmenu">' . ($do == 'guest' ? '<a href="' . Vars::$URI . '?act=who">' . __('users') . '</a> | <b>' . __('guests') . '</b>'
                : '<b>' . __('users') . '</b> | <a href="' . Vars::$URI . '?act=who&amp;do=guest">' . __('guests') . '</a>') . '</div>';
    $total = mysql_result(mysql_query("SELECT COUNT(*) FROM `" . ($do == 'guest' ? "cms_sessions" : "users") . "` WHERE `last_visit` > " . (time() - 300) . " AND `place` LIKE 'forum%'"), 0);
    if ($total > Vars::$USER_SET['page_size']) echo '<div class="topmenu">' . Functions::displayPagination(Vars::$URI . '?act=who&amp;' . ($do == 'guest' ? 'do=guest&amp;' : ''), Vars::$START, $total, Vars::$USER_SET['page_size']) . '</div>';
    if ($total) {
        $req = mysql_query("SELECT * FROM `" . ($do == 'guest' ? "cms_sessions" : "users") . "` WHERE `last_visit` > " . (time() - 300) . " AND `place` LIKE 'forum%' ORDER BY " . ($do == 'guest' ? "`movings` DESC"
                                   : "`name` ASC") . " " . Vars::db_pagination());
        $i = 0;
        while (($res = mysql_fetch_assoc($req)) !== FALSE) {
            if ($res['id'] == Vars::$USER_ID) echo '<div class="gmenu">';
            else echo $i % 2 ? '<div class="list2">' : '<div class="list1">';
            // Вычисляем местоположение
            $place = '';
            switch ($res['place']) {
                case 'forum':
                    $place = '<a href="' . Vars::$URI . '">' . __('place_main') . '</a>';
                    break;

                case 'forumwho':
                    $place = __('place_list');
                    break;

                case 'forumfiles':
                    $place = '<a href="' . Vars::$URI . '?act=files">' . __('place_files') . '</a>';
                    break;

                case 'forumnew':
                    $place = '<a href="' . Vars::$URI . '?act=new">' . __('place_new') . '</a>';
                    break;

                case 'forumsearch':
                    $place = '<a href="' . Vars::$URI . '/search">' . __('place_search') . '</a>';
                    break;

                default:
                    $where = explode(",", $res['place']);
                    if ($where[0] == 'forum' && intval($where[1])) {
                        $req_t = mysql_query("SELECT `type`, `refid`, `text` FROM `forum` WHERE `id` = '$where[1]'");
                        if (mysql_num_rows($req_t)) {
                            $res_t = mysql_fetch_assoc($req_t);
                            $link = '<a href="' . Vars::$URI . '?id=' . $where[1] . '">' . $res_t['text'] . '</a>';
                            switch ($res_t['type']) {
                                case 'f':
                                    $place = __('place_category') . ' &quot;' . $link . '&quot;';
                                    break;

                                case 'r':
                                    $place = __('place_section') . ' &quot;' . $link . '&quot;';
                                    break;

                                case 't':
                                    $place = (isset($where[2]) ? __('place_write') . ' &quot;' : __('place_topic') . ' &quot;') . $link . '&quot;';
                                    break;

                                case 'm':
                                    $req_m = mysql_query("SELECT `text` FROM `forum` WHERE `id` = '" . $res_t['refid'] . "' AND `type` = 't'");
                                    if (mysql_num_rows($req_m)) {
                                        $res_m = mysql_fetch_assoc($req_m);
                                        $place = (isset($where[2]) ? __('place_answer') : __('place_topic')) . ' &quot;<a href="' . Vars::$URI . '?id=' . $res_t['refid'] . '">' . $res_m['text'] . '</a>&quot;';
                                    }
                                    break;
                            }
                        }
                    }
            }
            $arg = array(
                'stshide' => 1,
                'header' => ('<br />' . Functions::getIcon('info.png', '', '', 'align="middle"') . '&#160;' . $place)
            );
            echo Functions::displayUser($res, $arg);
            echo '</div>';
            ++$i;
        }
    } else {
        echo '<div class="menu"><p>' . __('list_empty') . '</p></div>';
    }
    echo '<div class="phdr">' . __('total') . ': ' . $total . '</div>';
    if ($total > Vars::$USER_SET['page_size']) {
        echo '<div class="topmenu">' . Functions::displayPagination(Vars::$URI . '?act=who&amp;' . ($do == 'guest' ? 'do=guest&amp;' : ''), Vars::$START, $total, Vars::$USER_SET['page_size']) . '</div>' .
             '<p><form action="' . Vars::$URI . '?act=who' . ($do == 'guest' ? '&amp;do=guest' : '') . '" method="post">' .
             '<input type="text" name="page" size="2"/>' .
             '<input type="submit" value="' . __('to_page') . ' &gt;&gt;"/>' .
             '</form></p>';
    }
    echo '<p><a href="' . Vars::$URI . '">' . __('to_forum') . '</a></p>';
}