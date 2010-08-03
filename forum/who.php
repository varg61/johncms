<?php

/*
////////////////////////////////////////////////////////////////////////////////
// JohnCMS                Mobile Content Management System                    //
// Project site:          http://johncms.com                                  //
// Support site:          http://gazenwagen.com                               //
////////////////////////////////////////////////////////////////////////////////
// Lead Developer:        Oleg Kasyanov   (AlkatraZ)  alkatraz@gazenwagen.com //
// Development Team:      Eugene Ryabinin (john77)    john77@gazenwagen.com   //
//                        Dmitry Liseenko (FlySelf)   flyself@johncms.com     //
////////////////////////////////////////////////////////////////////////////////
*/

defined('_IN_JOHNCMS') or die('Error: restricted access');
$textl = $lng_forum['who_in_forum'];
$headmod = $id ? 'forum,' . $id : 'forumwho';
require_once('../incfiles/head.php');
$onltime = $realtime - 300;
if (!$user_id) {
    header('Location: index.php');
    exit;
}

// Ссылка на Новые темы
forum_new(1);
if ($id) {
    /*
    -----------------------------------------------------------------
    Показываем общий список тех, кто в выбранной теме
    -----------------------------------------------------------------
    */
    $req = mysql_query("SELECT `text` FROM `forum` WHERE `id` = '$id' AND `type` = 't' LIMIT 1");
    if (mysql_num_rows($req)) {
        $res = mysql_fetch_assoc($req);
        echo '<div class="phdr"><b>' . $lng_forum['who_in_topic'] . ':</b> ' . $res['text'] . '</div>';
        if ($rights > 0)
            echo '<div class="topmenu">' . ($do == 'guest' ? '<a href="index.php?act=who&amp;id=' . $id . '">' . $lng['authorized'] . '</a> | ' . $lng['guests'] : $lng['authorized'] . ' | <a href="index.php?act=who&amp;do=guest&amp;id=' . $id . '">' . $lng['guests'] . '</a>') . '</div>';
        $total = mysql_result(mysql_query("SELECT COUNT(*) FROM `" . ($do == 'guest' ? 'cms_guests' : 'users') . "` WHERE `lastdate` > $onltime AND `place` = 'forum,$id'"), 0);
        if ($total) {
            $req = mysql_query("SELECT * FROM `" . ($do == 'guest' ? 'cms_guests' : 'users') . "` WHERE `lastdate` > $onltime AND `place` = 'forum,$id' ORDER BY " . ($do == 'guest' ? "`movings` DESC" : "`name` ASC") . " LIMIT $start, $kmess");
            while ($res = mysql_fetch_assoc($req)) {
                echo $i % 2 ? '<div class="list2">' : '<div class="list1">';
                $set_user['avatar'] = 0;
                echo display_user($res, 0, ($act == 'guest' || ($rights >= 1 && $rights >= $res['rights']) ? 1 : 0));
                echo '</div>';
                ++$i;
            }
        } else {
            echo '<div class="menu"><p>' . $lng['list_empty'] . '</p></div>';
        }
    } else {
        header('Location: index.php');
    }
    echo '<div class="phdr">' . $lng['total'] . ': ' . $total . '</div>' .
        '<p><a href="index.php?id=' . $id . '">' . $lng_forum['to_topic'] . '</a></p>';
} else {
    /*
    -----------------------------------------------------------------
    Показываем общий список тех, кто в форуме
    -----------------------------------------------------------------
    */
    echo '<div class="phdr"><b>' . $lng_forum['who_in_forum'] . '</b></div>';
    if ($rights > 0)
        echo '<div class="topmenu">' . ($do == 'guest' ? '<a href="index.php?act=who">' . $lng['authorized'] . '</a> | ' . $lng['guests'] : $lng['authorized'] . ' | <a href="index.php?act=who&amp;do=guest">' . $lng['guests'] . '</a>') . '</div>';
    $total = mysql_result(mysql_query("SELECT COUNT(*) FROM `" . ($do == 'guest' ? "cms_guests" : "users") . "` WHERE `lastdate` > $onltime AND `place` LIKE 'forum%'"), 0);
    if ($total) {
        $req = mysql_query("SELECT * FROM `" . ($do == 'guest' ? "cms_guests" : "users") . "` WHERE `lastdate` > $onltime AND `place` LIKE 'forum%' ORDER BY " . ($do == 'guest' ? "`movings` DESC" : "`name` ASC") . " LIMIT $start, $kmess");
        while ($res = mysql_fetch_assoc($req)) {
            echo $i % 2 ? '<div class="list2">' : '<div class="list1">';
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
                        $req_t = mysql_query("SELECT `type`, `refid`, `text` FROM `forum` WHERE `id` = '$where[1]' LIMIT 1");
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
                                    $req_m = mysql_query("SELECT `text` FROM `forum` WHERE `id` = '" . $res_t['refid'] . "' AND `type` = 't' LIMIT 1");
                                    if (mysql_num_rows($req_m)) {
                                        $res_m = mysql_fetch_assoc($req_m);
                                        $place = (isset($where[2]) ? $lng_forum['place_answer'] : $lng_forum['place_topic']) . ' &quot;<a href="index.php?id=' . $res_t['refid'] . '">' . $res_m['text'] . '</a>&quot;';
                                    }
                                    break;
                            }
                        }
                    }
            }
            $arg = array (
                'stshide' => 1,
                'header' => ('<br /><img src="../images/info.png" width="16" height="16" align="middle" />&#160;' . $place)
            );
            echo display_user($res, $arg);
            echo '</div>';
            ++$i;
        }
    } else {
        echo '<div class="menu"><p>' . $lng['list_empty'] . '</p></div>';
    }
    echo '<div class="phdr">' . $lng['total'] . ': ' . $total . '</div>';
    if ($total > 10) {
        echo '<p>' . display_pagination('index.php?act=who&amp;' . ($do == 'guest' ? 'do=guest&amp;' : ''), $start, $total, $kmess) . '</p>' .
            '<p><form action="index.php?act=who' . ($do == 'guest' ? '&amp;do=guest' : '') . '" method="post">' .
            '<input type="text" name="page" size="2"/>' .
            '<input type="submit" value="' . $lng['to_page'] . ' &gt;&gt;"/>' .
            '</form></p>';
    }
    echo '<p><a href="index.php">' . $lng['to_forum'] . '</a></p>';
}
?>