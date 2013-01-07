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

$backLink = Router::getUrl(2);

switch (Vars::$ACT) {
    case 'last_comm':
        /*
        -----------------------------------------------------------------
        Последние комментарии по всем альбомам
        -----------------------------------------------------------------
        */
        $total = mysql_result(mysql_query("SELECT COUNT(DISTINCT `sub_id`) FROM `cms_album_comments` WHERE `time` >" . (time() - 86400)), 0);
        $title = __('new_comments');
        $select = "";
        $join = "INNER JOIN `cms_album_comments` ON `cms_album_files`.`id` = `cms_album_comments`.`sub_id`";
        $where = "`cms_album_comments`.`time` > " . (time() - 86400) . " GROUP BY `cms_album_files`.`id`";
        $order = "`cms_album_comments`.`time` DESC";
        $link = '&amp;mod=last_comm';
        break;

    case 'views':
        /*
        -----------------------------------------------------------------
        ТОП просмотров
        -----------------------------------------------------------------
        */
        $title = __('top_views');
        $select = "";
        $join = "";
        $where = "`cms_album_files`.`views` > '0'" . (Vars::$USER_RIGHTS >= 6 ? "" : " AND `cms_album_files`.`access` = '4'");
        $order = "`views` DESC";
        $link = '&amp;act=views';
        break;

    case 'downloads':
        /*
        -----------------------------------------------------------------
        ТОП скачиваний
        -----------------------------------------------------------------
        */
        $title = __('top_downloads');
        $select = "";
        $join = "";
        $where = "`cms_album_files`.`downloads` > 0" . (Vars::$USER_RIGHTS >= 6 ? "" : " AND `cms_album_files`.`access` = '4'");
        $order = "`downloads` DESC";
        $link = 'act=downloads';
        break;

    case 'comments':
        /*
        -----------------------------------------------------------------
        ТОП комментариев
        -----------------------------------------------------------------
        */
        $title = __('top_comments');
        $select = "";
        $join = "";
        $where = "`cms_album_files`.`comm_count` > '0'" . (Vars::$USER_RIGHTS >= 6 ? "" : " AND `cms_album_files`.`access` = '4'");
        $order = "`comm_count` DESC";
        $link = 'act=comments';
        break;

    case 'trash':
        /*
        -----------------------------------------------------------------
        ТОП отрицательных голосов
        -----------------------------------------------------------------
        */
        $title = __('top_trash');
        $select = ", (`vote_plus` - `vote_minus`) AS `rating`";
        $join = "";
        $where = "(`vote_plus` - `vote_minus`) < -2" . (Vars::$USER_RIGHTS >= 6 ? "" : " AND `cms_album_files`.`access` = '4'");
        $order = "`rating` ASC";
        $link = 'act=trash';
        break;

    default:
        /*
        -----------------------------------------------------------------
        ТОП положительных голосов
        -----------------------------------------------------------------
        */
        $title = __('top_votes');
        $select = ", (`vote_plus` - `vote_minus`) AS `rating`";
        $join = "";
        $where = "(`vote_plus` - `vote_minus`) > 2" . (Vars::$USER_RIGHTS >= 6 ? "" : " AND `cms_album_files`.`access` = '4'");
        $order = "`rating` DESC";
        $link = 'act=votes';
}

/*
-----------------------------------------------------------------
Показываем список фотографий, отсортированных по рейтингу
-----------------------------------------------------------------
*/
unset($_SESSION['ref']);
echo '<div class="phdr"><a href="' . $backLink . '"><b>' . __('photo_albums') . '</b></a> | ' . $title . '</div>';

if (!isset($total)) {
    $total = mysql_result(mysql_query("SELECT COUNT(*) FROM `cms_album_files` WHERE $where"), 0);
}

if ($total) {
    if ($total > Vars::$USER_SET['page_size'])
        echo '<div class="topmenu">' . Functions::displayPagination(Vars::$URI . '?' . $link . '&amp;', Vars::$START, $total, Vars::$USER_SET['page_size']) . '</div>';
    $req = mysql_query("
        SELECT `cms_album_files`.*, `users`.`nickname` AS `user_name`, `cms_album_cat`.`name` AS `album_name` $select
        FROM `cms_album_files`
        INNER JOIN `users` ON `cms_album_files`.`user_id` = `users`.`id`
        INNER JOIN `cms_album_cat` ON `cms_album_files`.`album_id` = `cms_album_cat`.`id`
        $join
        WHERE $where
        ORDER BY $order
        " . Vars::db_pagination()
    ) or die(mysql_error());
    for ($i = 0; ($res = mysql_fetch_assoc($req)) !== FALSE; ++$i) {
        echo $i % 2 ? '<div class="list2">' : '<div class="list1">';
        if ($res['access'] == 4 || Vars::$USER_RIGHTS >= 7) {
            // Если доступ открыт всем, или смотрит Администратор
            echo '<a href="' . $backLink . '?act=show&amp;al=' . $res['album_id'] . '&amp;img=' . $res['id'] . '&amp;user=' . $res['user_id'] . '&amp;view"><img src="../files/users/album/' . $res['user_id'] . '/' . $res['tmb_name'] . '" /></a>';
            if (!empty($res['description'])) {
                echo '<div class="gray">' . Functions::smilies(Validate::checkout($res['description'], 1)) . '</div>';
            }
        } elseif ($res['access'] == 3) {
            // Если доступ открыт друзьям
            echo 'Только для друзей';
        } elseif ($res['access'] == 2) {
            // Если доступ по паролю
            echo '<a href="' . $backLink . '?act=show&amp;al=' . $res['album_id'] . '&amp;img=' . $res['id'] . '&amp;user=' . $res['user_id'] . '">' . Functions::getImage('password.gif') . '</a>';
        }
        echo'<div class="sub">' .
            '<a href="' . $backLink . '?act=list&amp;user=' . $res['user_id'] . '"><b>' . $res['user_name'] . '</b></a> | ' .
            '<a href="' . $backLink . '?act=show&amp;al=' . $res['album_id'] . '&amp;user=' . $res['user_id'] . '">' . Validate::checkout($res['album_name']) . '</a>';
        if ($res['access'] == 4 || Vars::$USER_RIGHTS >= 6) {
            echo Album::vote($res) .
                '<div class="gray">' . __('count_views') . ': ' . $res['views'] . ', ' . __('count_downloads') . ': ' . $res['downloads'] . '</div>' .
                '<div class="gray">' . __('date') . ': ' . Functions::displayDate($res['time']) . '</div>' .
                '<a href="' . $backLink . '?act=comments&amp;img=' . $res['id'] . '">' . __('comments') . '</a> (' . $res['comm_count'] . ')' .
                '<br /><a href="' . $backLink . '?act=image_download&amp;img=' . $res['id'] . '">' . __('download') . '</a>';
        }
        echo '</div></div>';
    }
} else {
    echo '<div class="menu"><p>' . __('list_empty') . '</p></div>';
}
echo '<div class="phdr">' . __('total') . ': ' . $total . '</div>';
if ($total > Vars::$USER_SET['page_size']) {
    echo '<div class="topmenu">' . Functions::displayPagination(Vars::$URI . '?' . $link . '&amp;', Vars::$START, $total, Vars::$USER_SET['page_size']) . '</div>' .
        '<p><form action="' . Vars::$URI . '?' . $link . '" method="post">' .
        '<input type="text" name="page" size="2"/>' .
        '<input type="submit" value="' . __('to_page') . ' &gt;&gt;"/>' .
        '</form></p>';
}
echo '<p><a href="' . $backLink . '">' . __('photo_albums') . '</a></p>';