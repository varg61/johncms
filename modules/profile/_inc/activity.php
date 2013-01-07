<?php

/**
 * @package     JohnCMS
 * @link        http://johncms.com
 * @copyright   Copyright (C) 2008-2012 JohnCMS Community
 * @license     LICENSE.txt (see attached file)
 * @version     VERSION.txt (see attached file)
 * @author      http://johncms.com/about
 */

defined('_IN_PROFILE') or die('Error: restricted access');
$url = Router::getUrl(2);

/*
-----------------------------------------------------------------
История активности
-----------------------------------------------------------------
*/
echo '<div class="phdr"><a href="' . $url . '?user=' . $user['id'] . '"><b>' . ($user['id'] != Vars::$USER_ID ? __('user_profile') : __('my_profile')) . '</b></a> | ' . __('activity') . '</div>';
$menu = array(
    (!Vars::$MOD ? '<b>' . __('messages') . '</b>' : '<a href="' . $url . '?act=activity&amp;user=' . $user['id'] . '">' . __('messages') . '</a>'),
    (Vars::$MOD == 'topic' ? '<b>' . __('themes') . '</b>' : '<a href="' . $url . '?act=activity&amp;mod=topic&amp;user=' . $user['id'] . '">' . __('themes') . '</a>'),
    (Vars::$MOD == 'comments' ? '<b>' . __('comments') . '</b>' : '<a href="' . $url . '?act=activity&amp;mod=comments&amp;user=' . $user['id'] . '">' . __('comments') . '</a>'),
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
        $total = mysql_result(mysql_query("SELECT COUNT(*) FROM `guest` WHERE `user_id` = '" . $user['id'] . "'" . (Vars::$USER_RIGHTS >= 1 ? '' : " AND `adm` = '0'")), 0);
        echo '<div class="phdr"><b>' . __('comments') . '</b></div>';
        if ($total > Vars::$USER_SET['page_size']) echo '<div class="topmenu">' . Functions::displayPagination('profile.php?act=activity&amp;mod=comments&amp;user=' . $user['id'] . '&amp;', Vars::$START, $total, Vars::$USER_SET['page_size']) . '</div>';
        $req = mysql_query("SELECT * FROM `guest` WHERE `user_id` = '" . $user['id'] . "'" . (Vars::$USER_RIGHTS >= 1 ? '' : " AND `adm` = '0'") . " ORDER BY `id` DESC" . Vars::db_pagination());
        if (mysql_num_rows($req)) {
            $i = 0;
            while ($res = mysql_fetch_assoc($req)) {
                echo ($i % 2 ? '<div class="list2">' : '<div class="list1">') . Validate::checkout($res['text'], 2, 1) . '<div class="sub">' .
                     '<span class="gray">(' . Functions::displayDate($res['time']) . ')</span>' .
                     '</div></div>';
                ++$i;
            }
        } else {
            echo '<div class="menu"><p>' . __('list_empty') . '</p></div>';
        }
        break;

    case 'topic':
        /*
        -----------------------------------------------------------------
        Список тем Форума
        -----------------------------------------------------------------
        */
        $total = mysql_result(mysql_query("SELECT COUNT(*) FROM `forum` WHERE `user_id` = '" . $user['id'] . "' AND `type` = 't'" . (Vars::$USER_RIGHTS >= 7 ? '' : " AND `close`!='1'")), 0);
        echo '<div class="phdr"><b>' . __('forum') . '</b>: ' . __('themes') . '</div>';
        if ($total > Vars::$USER_SET['page_size']) echo '<div class="topmenu">' . Functions::displayPagination('profile.php?act=activity&amp;mod=topic&amp;user=' . $user['id'] . '&amp;', Vars::$START, $total, Vars::$USER_SET['page_size']) . '</div>';
        $req = mysql_query("SELECT * FROM `forum` WHERE `user_id` = '" . $user['id'] . "' AND `type` = 't'" . (Vars::$USER_RIGHTS >= 7 ? '' : " AND `close`!='1'") . " ORDER BY `id` DESC" . Vars::db_pagination());
        if (mysql_num_rows($req)) {
            $i = 0;
            while ($res = mysql_fetch_assoc($req)) {
                $post = mysql_fetch_assoc(mysql_query("SELECT * FROM `forum` WHERE `refid` = '" . $res['id'] . "'" . (Vars::$USER_RIGHTS >= 7 ? '' : " AND `close`!='1'") . " ORDER BY `id` ASC LIMIT 1"));
                $section = mysql_fetch_assoc(mysql_query("SELECT * FROM `forum` WHERE `id` = '" . $res['refid'] . "'"));
                $category = mysql_fetch_assoc(mysql_query("SELECT * FROM `forum` WHERE `id` = '" . $section['refid'] . "'"));
                $text = mb_substr($post['text'], 0, 300);
                $text = Validate::checkout($text, 2, 1);
                echo ($i % 2 ? '<div class="list2">' : '<div class="list1">') .
                     '<a href="' . Vars::$HOME_URL . '/forum?id=' . $res['id'] . '">' . $res['text'] . '</a>' .
                     '<br />' . $text . '...<a href="' . Vars::$HOME_URL . '/forum?id=' . $res['id'] . '"> &gt;&gt;</a>' .
                     '<div class="sub">' .
                     '<a href="' . Vars::$HOME_URL . '/forum?id=' . $category['id'] . '">' . $category['text'] . '</a> | ' .
                     '<a href="' . Vars::$HOME_URL . '/forum?id=' . $section['id'] . '">' . $section['text'] . '</a>' .
                     '<br /><span class="gray">(' . Functions::displayDate($res['time']) . ')</span>' .
                     '</div></div>';
                ++$i;
            }
        } else {
            echo '<div class="menu"><p>' . __('list_empty') . '</p></div>';
        }
        break;

    default:
        /*
        -----------------------------------------------------------------
        Список постов Форума
        -----------------------------------------------------------------
        */
        $total = mysql_result(mysql_query("SELECT COUNT(*) FROM `forum` WHERE `user_id` = '" . $user['id'] . "' AND `type` = 'm'" . (Vars::$USER_RIGHTS >= 7 ? '' : " AND `close`!='1'")), 0);
        echo '<div class="phdr"><b>' . __('forum') . '</b>: ' . __('messages') . '</div>';
        if ($total > Vars::$USER_SET['page_size']) echo '<div class="topmenu">' . Functions::displayPagination('profile.php?act=activity&amp;user=' . $user['id'] . '&amp;', Vars::$START, $total, Vars::$USER_SET['page_size']) . '</div>';
        $req = mysql_query("SELECT * FROM `forum` WHERE `user_id` = '" . $user['id'] . "' AND `type` = 'm' " . (Vars::$USER_RIGHTS >= 7 ? '' : " AND `close`!='1'") . " ORDER BY `id` DESC" . Vars::db_pagination());
        if (mysql_num_rows($req)) {
            $i = 0;
            while ($res = mysql_fetch_assoc($req)) {
                $topic = mysql_fetch_assoc(mysql_query("SELECT * FROM `forum` WHERE `id` = '" . $res['refid'] . "'"));
                $section = mysql_fetch_assoc(mysql_query("SELECT * FROM `forum` WHERE `id` = '" . $topic['refid'] . "'"));
                $category = mysql_fetch_assoc(mysql_query("SELECT * FROM `forum` WHERE `id` = '" . $section['refid'] . "'"));
                $text = mb_substr($res['text'], 0, 300);
                $text = Validate::checkout($text, 2, 1);
                $text = preg_replace('#\[c\](.*?)\[/c\]#si', '<div class="quote">\1</div>', $text);
                echo ($i % 2 ? '<div class="list2">' : '<div class="list1">') .
                     '<a href="' . Vars::$HOME_URL . '/forum?id=' . $topic['id'] . '">' . $topic['text'] . '</a>' .
                     '<br />' . $text . '...<a href="' . Vars::$HOME_URL . '/forum?act=post&amp;id=' . $res['id'] . '"> &gt;&gt;</a>' .
                     '<div class="sub">' .
                     '<a href="' . Vars::$HOME_URL . '/forum?id=' . $category['id'] . '">' . $category['text'] . '</a> | ' .
                     '<a href="' . Vars::$HOME_URL . '/forum?id=' . $section['id'] . '">' . $section['text'] . '</a>' .
                     '<br /><span class="gray">(' . Functions::displayDate($res['time']) . ')</span>' .
                     '</div></div>';
                ++$i;
            }
        } else {
            echo '<div class="menu"><p>' . __('list_empty') . '</p></div>';
        }
}
echo '<div class="phdr">' . __('total') . ': ' . $total . '</div>';
if ($total > Vars::$USER_SET['page_size']) {
    echo '<div class="topmenu">' . Functions::displayPagination('profile.php?act=activity' . (Vars::$MOD ? '&amp;mod=' . Vars::$MOD : '') . '&amp;user=' . $user['id'] . '&amp;', Vars::$START, $total, Vars::$USER_SET['page_size']) . '</div>' .
         '<p><form action="profile.php?act=activity&amp;user=' . $user['id'] . (Vars::$MOD ? '&amp;mod=' . Vars::$MOD : '') . '" method="post">' .
         '<input type="text" name="page" size="2"/>' .
         '<input type="submit" value="' . __('to_page') . ' &gt;&gt;"/>' .
         '</form></p>';
}