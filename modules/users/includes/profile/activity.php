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

/*
-----------------------------------------------------------------
История активности
-----------------------------------------------------------------
*/
echo '<div class="phdr"><a href="profile.php?user=' . $user['user_id'] . '"><b>' . Vars::$LNG['profile'] . '</b></a> | ' . $lng_profile['activity'] . '</div>';
$menu = array(
    (!Vars::$MOD ? '<b>' . Vars::$LNG['messages'] . '</b>' : '<a href="profile.php?act=activity&amp;user=' . $user['user_id'] . '">' . Vars::$LNG['messages'] . '</a>'),
    (Vars::$MOD == 'topic' ? '<b>' . Vars::$LNG['themes'] . '</b>' : '<a href="profile.php?act=activity&amp;mod=topic&amp;user=' . $user['user_id'] . '">' . Vars::$LNG['themes'] . '</a>'),
    (Vars::$MOD == 'comments' ? '<b>' . Vars::$LNG['comments'] . '</b>' : '<a href="profile.php?act=activity&amp;mod=comments&amp;user=' . $user['user_id'] . '">' . Vars::$LNG['comments'] . '</a>'),
);
echo '<div class="topmenu">' . Functions::displayMenu($menu) . '</div>' .
     '<div class="user"><p>' . Functions::displayUser($user, array('iphide' => 1,)) . '</p></div>';
switch (Vars::$MOD) {
    case 'comments':
        /*
        -----------------------------------------------------------------
        Список сообщений в Гостевой
        -----------------------------------------------------------------
        */
        $total = mysql_result(mysql_query("SELECT COUNT(*) FROM `guest` WHERE `user_id` = '" . $user['user_id'] . "'" . (Vars::$USER_RIGHTS >= 1 ? '' : " AND `adm` = '0'")), 0);
        echo '<div class="phdr"><b>' . Vars::$LNG['comments'] . '</b></div>';
        if ($total > Vars::$USER_SET['page_size']) echo '<div class="topmenu">' . Functions::displayPagination('profile.php?act=activity&amp;mod=comments&amp;user=' . $user['user_id'] . '&amp;', Vars::$START, $total, Vars::$USER_SET['page_size']) . '</div>';
        $req = mysql_query("SELECT * FROM `guest` WHERE `user_id` = '" . $user['user_id'] . "'" . (Vars::$USER_RIGHTS >= 1 ? '' : " AND `adm` = '0'") . " ORDER BY `id` DESC LIMIT " . Vars::db_pagination());
        if (mysql_num_rows($req)) {
            $i = 0;
            while ($res = mysql_fetch_assoc($req)) {
                echo ($i % 2 ? '<div class="list2">' : '<div class="list1">') . Validate::filterString($res['text'], 2, 1) . '<div class="sub">' .
                     '<span class="gray">(' . Functions::displayDate($res['time']) . ')</span>' .
                     '</div></div>';
                ++$i;
            }
        } else {
            echo '<div class="menu"><p>' . $lng_profile['guest_empty'] . '</p></div>';
        }
        break;

    case 'topic':
        /*
        -----------------------------------------------------------------
        Список тем Форума
        -----------------------------------------------------------------
        */
        $total = mysql_result(mysql_query("SELECT COUNT(*) FROM `forum` WHERE `user_id` = '" . $user['user_id'] . "' AND `type` = 't'" . (Vars::$USER_RIGHTS >= 7 ? '' : " AND `close`!='1'")), 0);
        echo '<div class="phdr"><b>' . Vars::$LNG['forum'] . '</b>: ' . Vars::$LNG['themes'] . '</div>';
        if ($total > Vars::$USER_SET['page_size']) echo '<div class="topmenu">' . Functions::displayPagination('profile.php?act=activity&amp;mod=topic&amp;user=' . $user['user_id'] . '&amp;', Vars::$START, $total, Vars::$USER_SET['page_size']) . '</div>';
        $req = mysql_query("SELECT * FROM `forum` WHERE `user_id` = '" . $user['user_id'] . "' AND `type` = 't'" . (Vars::$USER_RIGHTS >= 7 ? '' : " AND `close`!='1'") . " ORDER BY `id` DESC LIMIT " . Vars::db_pagination());
        if (mysql_num_rows($req)) {
            $i = 0;
            while ($res = mysql_fetch_assoc($req)) {
                $post = mysql_fetch_assoc(mysql_query("SELECT * FROM `forum` WHERE `refid` = '" . $res['id'] . "'" . (Vars::$USER_RIGHTS >= 7 ? '' : " AND `close`!='1'") . " ORDER BY `id` ASC LIMIT 1"));
                $section = mysql_fetch_assoc(mysql_query("SELECT * FROM `forum` WHERE `id` = '" . $res['refid'] . "'"));
                $category = mysql_fetch_assoc(mysql_query("SELECT * FROM `forum` WHERE `id` = '" . $section['refid'] . "'"));
                $text = mb_substr($post['text'], 0, 300);
                $text = Validate::filterString($text, 2, 1);
                echo ($i % 2 ? '<div class="list2">' : '<div class="list1">') .
                     '<a href="' . Vars::$HOME_URL . '/forum/index.php?id=' . $res['id'] . '">' . $res['text'] . '</a>' .
                     '<br />' . $text . '...<a href="' . Vars::$HOME_URL . '/forum/index.php?id=' . $res['id'] . '"> &gt;&gt;</a>' .
                     '<div class="sub">' .
                     '<a href="' . Vars::$HOME_URL . '/forum/index.php?id=' . $category['id'] . '">' . $category['text'] . '</a> | ' .
                     '<a href="' . Vars::$HOME_URL . '/forum/index.php?id=' . $section['id'] . '">' . $section['text'] . '</a>' .
                     '<br /><span class="gray">(' . Functions::displayDate($res['time']) . ')</span>' .
                     '</div></div>';
                ++$i;
            }
        } else {
            echo '<div class="menu"><p>' . Vars::$LNG['list_empty'] . '</p></div>';
        }
        break;

    default:
        /*
        -----------------------------------------------------------------
        Список постов Форума
        -----------------------------------------------------------------
        */
        $total = mysql_result(mysql_query("SELECT COUNT(*) FROM `forum` WHERE `user_id` = '" . $user['user_id'] . "' AND `type` = 'm'" . (Vars::$USER_RIGHTS >= 7 ? '' : " AND `close`!='1'")), 0);
        echo '<div class="phdr"><b>' . Vars::$LNG['forum'] . '</b>: ' . Vars::$LNG['messages'] . '</div>';
        if ($total > Vars::$USER_SET['page_size']) echo '<div class="topmenu">' . Functions::displayPagination('profile.php?act=activity&amp;user=' . $user['user_id'] . '&amp;', Vars::$START, $total, Vars::$USER_SET['page_size']) . '</div>';
        $req = mysql_query("SELECT * FROM `forum` WHERE `user_id` = '" . $user['user_id'] . "' AND `type` = 'm' " . (Vars::$USER_RIGHTS >= 7 ? '' : " AND `close`!='1'") . " ORDER BY `id` DESC LIMIT " . Vars::db_pagination());
        if (mysql_num_rows($req)) {
            $i = 0;
            while ($res = mysql_fetch_assoc($req)) {
                $topic = mysql_fetch_assoc(mysql_query("SELECT * FROM `forum` WHERE `id` = '" . $res['refid'] . "'"));
                $section = mysql_fetch_assoc(mysql_query("SELECT * FROM `forum` WHERE `id` = '" . $topic['refid'] . "'"));
                $category = mysql_fetch_assoc(mysql_query("SELECT * FROM `forum` WHERE `id` = '" . $section['refid'] . "'"));
                $text = mb_substr($res['text'], 0, 300);
                $text = Validate::filterString($text, 2, 1);
                $text = preg_replace('#\[c\](.*?)\[/c\]#si', '<div class="quote">\1</div>', $text);
                echo ($i % 2 ? '<div class="list2">' : '<div class="list1">') .
                     '<a href="' . Vars::$HOME_URL . '/forum/index.php?id=' . $topic['id'] . '">' . $topic['text'] . '</a>' .
                     '<br />' . $text . '...<a href="' . Vars::$HOME_URL . '/forum/index.php?act=post&amp;id=' . $res['id'] . '"> &gt;&gt;</a>' .
                     '<div class="sub">' .
                     '<a href="' . Vars::$HOME_URL . '/forum/index.php?id=' . $category['id'] . '">' . $category['text'] . '</a> | ' .
                     '<a href="' . Vars::$HOME_URL . '/forum/index.php?id=' . $section['id'] . '">' . $section['text'] . '</a>' .
                     '<br /><span class="gray">(' . Functions::displayDate($res['time']) . ')</span>' .
                     '</div></div>';
                ++$i;
            }
        } else {
            echo '<div class="menu"><p>' . Vars::$LNG['list_empty'] . '</p></div>';
        }
}
echo '<div class="phdr">' . Vars::$LNG['total'] . ': ' . $total . '</div>';
if ($total > Vars::$USER_SET['page_size']) {
    echo '<div class="topmenu">' . Functions::displayPagination('profile.php?act=activity' . (Vars::$MOD ? '&amp;mod=' . Vars::$MOD : '') . '&amp;user=' . $user['user_id'] . '&amp;', Vars::$START, $total, Vars::$USER_SET['page_size']) . '</div>' .
         '<p><form action="profile.php?act=activity&amp;user=' . $user['user_id'] . (Vars::$MOD ? '&amp;mod=' . Vars::$MOD : '') . '" method="post">' .
         '<input type="text" name="page" size="2"/>' .
         '<input type="submit" value="' . Vars::$LNG['to_page'] . ' &gt;&gt;"/>' .
         '</form></p>';
}