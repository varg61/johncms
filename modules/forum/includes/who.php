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
    header('Location: index.php');
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
        echo '<div class="phdr"><b>' . $lng_forum['who_in_topic'] . ':</b> <a href="index.php?id=' . Vars::$ID . '">' . $res['text'] . '</a></div>';
        if (Vars::$USER_RIGHTS > 0)
            echo '<div class="topmenu">' . ($do == 'guest' ? '<a href="index.php?act=who&amp;id=' . Vars::$ID . '">' . Vars::$LNG['authorized'] . '</a> | ' . Vars::$LNG['guests']
                    : Vars::$LNG['authorized'] . ' | <a href="index.php?act=who&amp;do=guest&amp;id=' . Vars::$ID . '">' . Vars::$LNG['guests'] . '</a>') . '</div>';
        $total = mysql_result(mysql_query("SELECT COUNT(*) FROM `" . ($do == 'guest' ? 'cms_sessions' : 'users') . "` WHERE `lastdate` > " . (time() - 300) . " AND `place` = 'forum," . Vars::$ID . "'"), 0);
        if ($total) {
            $req = mysql_query("SELECT * FROM `" . ($do == 'guest' ? 'cms_sessions' : 'users') . "` WHERE `lastdate` > " . (time() - 300) . " AND `place` = 'forum," . Vars::$ID . "' ORDER BY " . ($do == 'guest' ? "`movings` DESC"
                                       : "`name` ASC") . " LIMIT " . Vars::db_pagination());
            while (($res = mysql_fetch_assoc($req)) !== false) {
                echo $i % 2 ? '<div class="list2">' : '<div class="list1">';
                Vars::$USER_SET['avatar'] = 0;
                echo Functions::displayUser($res, 0, (Vars::$ACT == 'guest' || (Vars::$USER_RIGHTS >= 1 && Vars::$USER_RIGHTS >= $res['rights']) ? 1 : 0));
                echo '</div>';
                ++$i;
            }
        } else {
            echo '<div class="menu"><p>' . Vars::$LNG['list_empty'] . '</p></div>';
        }
    } else {
        header('Location: index.php');
    }
    echo '<div class="phdr">' . Vars::$LNG['total'] . ': ' . $total . '</div>' .
         '<p><a href="index.php?id=' . Vars::$ID . '">' . $lng_forum['to_topic'] . '</a></p>';
} else {
    /*
    -----------------------------------------------------------------
    Показываем общий список тех, кто в форуме
    -----------------------------------------------------------------
    */
    echo '<div class="phdr"><a href="index.php"><b>' . Vars::$LNG['forum'] . '</b></a> | ' . $lng_forum['who_in_forum'] . '</div>';
    if (Vars::$USER_RIGHTS > 0)
        echo '<div class="topmenu">' . ($do == 'guest' ? '<a href="index.php?act=who">' . Vars::$LNG['users'] . '</a> | <b>' . Vars::$LNG['guests'] . '</b>'
                : '<b>' . Vars::$LNG['users'] . '</b> | <a href="index.php?act=who&amp;do=guest">' . Vars::$LNG['guests'] . '</a>') . '</div>';
    $total = mysql_result(mysql_query("SELECT COUNT(*) FROM `" . ($do == 'guest' ? "cms_sessions" : "users") . "` WHERE `lastdate` > " . (time() - 300) . " AND `place` LIKE 'forum%'"), 0);
    if ($total > Vars::$USER_SET['page_size']) echo '<div class="topmenu">' . Functions::displayPagination('index.php?act=who&amp;' . ($do == 'guest' ? 'do=guest&amp;' : ''), Vars::$START, $total, Vars::$USER_SET['page_size']) . '</div>';
    if ($total) {
        $req = mysql_query("SELECT * FROM `" . ($do == 'guest' ? "cms_sessions" : "users") . "` WHERE `lastdate` > " . (time() - 300) . " AND `place` LIKE 'forum%' ORDER BY " . ($do == 'guest' ? "`movings` DESC"
                                   : "`name` ASC") . " LIMIT " . Vars::db_pagination());
        $i = 0;
        while (($res = mysql_fetch_assoc($req)) !== false) {
            if ($res['id'] == Vars::$USER_ID) echo '<div class="gmenu">';
            else echo $i % 2 ? '<div class="list2">' : '<div class="list1">';
            // Вычисляем местоположение
            $place = '';
            switch ($res['place']) {
                case 'forum':
                    $place = '<a href="index.php">' . $lng_forum['place_main'] . '</a>';
                    break;

                case 'forumwho':
                    $place = $lng_forum['place_list'];
                    break;

                case 'forumfiles':
                    $place = '<a href="index.php?act=files">' . $lng_forum['place_files'] . '</a>';
                    break;

                case 'forumnew':
                    $place = '<a href="index.php?act=new">' . $lng_forum['place_new'] . '</a>';
                    break;

                case 'forumsearch':
                    $place = '<a href="search.php">' . $lng_forum['place_search'] . '</a>';
                    break;

                default:
                    $where = explode(",", $res['place']);
                    if ($where[0] == 'forum' && intval($where[1])) {
                        $req_t = mysql_query("SELECT `type`, `refid`, `text` FROM `forum` WHERE `id` = '$where[1]'");
                        if (mysql_num_rows($req_t)) {
                            $res_t = mysql_fetch_assoc($req_t);
                            $link = '<a href="index.php?id=' . $where[1] . '">' . $res_t['text'] . '</a>';
                            switch ($res_t['type']) {
                                case 'f':
                                    $place = $lng_forum['place_category'] . ' &quot;' . $link . '&quot;';
                                    break;

                                case 'r':
                                    $place = $lng_forum['place_section'] . ' &quot;' . $link . '&quot;';
                                    break;

                                case 't':
                                    $place = (isset($where[2]) ? $lng_forum['place_write'] . ' &quot;' : $lng_forum['place_topic'] . ' &quot;') . $link . '&quot;';
                                    break;

                                case 'm':
                                    $req_m = mysql_query("SELECT `text` FROM `forum` WHERE `id` = '" . $res_t['refid'] . "' AND `type` = 't'");
                                    if (mysql_num_rows($req_m)) {
                                        $res_m = mysql_fetch_assoc($req_m);
                                        $place = (isset($where[2]) ? $lng_forum['place_answer'] : $lng_forum['place_topic']) . ' &quot;<a href="index.php?id=' . $res_t['refid'] . '">' . $res_m['text'] . '</a>&quot;';
                                    }
                                    break;
                            }
                        }
                    }
            }
            $arg = array(
                'stshide' => 1,
                'header' => ('<br />' . Functions::getImage('info.png', '', 'align="middle"') . '&#160;' . $place)
            );
            echo Functions::displayUser($res, $arg);
            echo '</div>';
            ++$i;
        }
    } else {
        echo '<div class="menu"><p>' . Vars::$LNG['list_empty'] . '</p></div>';
    }
    echo '<div class="phdr">' . Vars::$LNG['total'] . ': ' . $total . '</div>';
    if ($total > Vars::$USER_SET['page_size']) {
        echo '<div class="topmenu">' . Functions::displayPagination('index.php?act=who&amp;' . ($do == 'guest' ? 'do=guest&amp;' : ''), Vars::$START, $total, Vars::$USER_SET['page_size']) . '</div>' .
             '<p><form action="index.php?act=who' . ($do == 'guest' ? '&amp;do=guest' : '') . '" method="post">' .
             '<input type="text" name="page" size="2"/>' .
             '<input type="submit" value="' . Vars::$LNG['to_page'] . ' &gt;&gt;"/>' .
             '</form></p>';
    }
    echo '<p><a href="index.php">' . Vars::$LNG['to_forum'] . '</a></p>';
}