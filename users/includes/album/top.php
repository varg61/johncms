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

switch (Vars::$MOD) {
    case 'my_new_comm':
        /*
        -----------------------------------------------------------------
        Непрочитанные комментарии в личных альбомах
        -----------------------------------------------------------------
        */
        if(!Vars::$USER_ID || Vars::$USER_ID != $user['user_id']){
            echo Functions::displayError($lng['wrong_data']);
            require_once('../includes/end.php');
            exit;
        }
        $title = $lng_profile['unread_comments'];
        $select = "";
        $join = "INNER JOIN `cms_album_comments` ON `cms_album_files`.`id` = `cms_album_comments`.`sub_id`";
        $where = "`cms_album_files`.`user_id` = '" . Vars::$USER_ID . "' AND `cms_album_files`.`unread_comments` = 1 GROUP BY `cms_album_files`.`id`";
        $order = "`cms_album_comments`.`time` DESC";
        $link = '&amp;mod=my_new_comm';
        break;

    case 'last_comm':
        /*
        -----------------------------------------------------------------
        Последние комментарии по всем альбомам
        -----------------------------------------------------------------
        */
        $total = mysql_result(mysql_query("SELECT COUNT(DISTINCT `sub_id`) FROM `cms_album_comments` WHERE `time` >" . (time() - 86400)), 0);
        $title = $lng_profile['new_comments'];
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
        $title = $lng_profile['top_views'];
        $select = "";
        $join = "";
        $where = "`cms_album_files`.`views` > '0'" . (Vars::$USER_RIGHTS >= 6 ? "" : " AND `cms_album_files`.`access` = '4'");
        $order = "`views` DESC";
        $link = '&amp;mod=views';
        break;

    case 'downloads':
        /*
        -----------------------------------------------------------------
        ТОП скачиваний
        -----------------------------------------------------------------
        */
        $title = $lng_profile['top_downloads'];
        $select = "";
        $join = "";
        $where = "`cms_album_files`.`downloads` > 0" . (Vars::$USER_RIGHTS >= 6 ? "" : " AND `cms_album_files`.`access` = '4'");
        $order = "`downloads` DESC";
        $link = '&amp;mod=downloads';
        break;

    case 'comments':
        /*
        -----------------------------------------------------------------
        ТОП комментариев
        -----------------------------------------------------------------
        */
        $title = $lng_profile['top_comments'];
        $select = "";
        $join = "";
        $where = "`cms_album_files`.`comm_count` > '0'" . (Vars::$USER_RIGHTS >= 6 ? "" : " AND `cms_album_files`.`access` = '4'");
        $order = "`comm_count` DESC";
        $link = '&amp;mod=comments';
        break;

    case 'votes':
        /*
        -----------------------------------------------------------------
        ТОП положительных голосов
        -----------------------------------------------------------------
        */
        $title = $lng_profile['top_votes'];
        $select = ", (`vote_plus` - `vote_minus`) AS `rating`";
        $join = "";
        $where = "(`vote_plus` - `vote_minus`) > 2" . (Vars::$USER_RIGHTS >= 6 ? "" : " AND `cms_album_files`.`access` = '4'");
        $order = "`rating` DESC";
        $link = '&amp;mod=votes';
        break;

    case 'trash':
        /*
        -----------------------------------------------------------------
        ТОП отрицательных голосов
        -----------------------------------------------------------------
        */
        $title = $lng_profile['top_trash'];
        $select = ", (`vote_plus` - `vote_minus`) AS `rating`";
        $join = "";
        $where = "(`vote_plus` - `vote_minus`) < -2" . (Vars::$USER_RIGHTS >= 6 ? "" : " AND `cms_album_files`.`access` = '4'");
        $order = "`rating` ASC";
        $link = '&amp;mod=trash';
        break;

    default:
        /*
        -----------------------------------------------------------------
        Новые изображения
        -----------------------------------------------------------------
        */
        $title = $lng_profile['new_photo'];
        $select = "";
        $join = "";
        $where = "`cms_album_files`.`time` > '" . (time() - 259200) . "'" . (Vars::$USER_RIGHTS >= 6 ? "" : " AND `cms_album_files`.`access` > '1'");
        $order = "`cms_album_files`.`time` DESC";
        $link = '';
}

/*
-----------------------------------------------------------------
Показываем список фотографий, отсортированных по рейтингу
-----------------------------------------------------------------
*/
unset($_SESSION['ref']);
require_once('../includes/head.php');
echo '<div class="phdr"><a href="album.php"><b>' . Vars::$LNG['photo_albums'] . '</b></a> | ' . $title . '</div>';
if(Vars::$MOD == 'my_new_comm') $total = $new_album_comm;
elseif(!isset($total)) $total = mysql_result(mysql_query("SELECT COUNT(*) FROM `cms_album_files` WHERE $where"), 0);
if ($total) {
    if ($total > Vars::$USER_SET['page_size'])
        echo '<div class="topmenu">' . Functions::displayPagination('album.php?act=top' . $link . '&amp;', Vars::$START, $total, Vars::$USER_SET['page_size']) . '</div>';
    $req = mysql_query("
        SELECT `cms_album_files`.*, `users`.`name` AS `user_name`, `cms_album_cat`.`name` AS `album_name` $select
        FROM `cms_album_files`
        INNER JOIN `users` ON `cms_album_files`.`user_id` = `users`.`id`
        INNER JOIN `cms_album_cat` ON `cms_album_files`.`album_id` = `cms_album_cat`.`id`
        $join
        WHERE $where
        ORDER BY $order
        LIMIT " . Vars::db_pagination()
    );
    $i = 0;
    for ($i = 0; ($res = mysql_fetch_assoc($req)) !== false; ++$i) {
        echo $i % 2 ? '<div class="list2">' : '<div class="list1">';
        if ($res['access'] == 4 || Vars::$USER_RIGHTS >= 7) {
            // Если доступ открыт всем, или смотрит Администратор
            echo '<a href="album.php?act=show&amp;al=' . $res['album_id'] . '&amp;img=' . $res['id'] . '&amp;user=' . $res['user_id'] . '&amp;view"><img src="../files/users/album/' . $res['user_id'] . '/' . $res['tmb_name'] . '" /></a>';
            if (!empty($res['description']))
                echo '<div class="gray">' . Functions::smileys(Validate::filterString($res['description'], 1)) . '</div>';
        } elseif ($res['access'] == 3) {
            // Если доступ открыт друзьям
            echo 'Только для друзей';
        } elseif ($res['access'] == 2) {
            // Если доступ по паролю
            echo '<a href="album.php?act=show&amp;al=' . $res['album_id'] . '&amp;img=' . $res['id'] . '&amp;user=' . $res['user_id'] . '">' . Functions::getImage('password.gif') . '</a>';
        }
        echo '<div class="sub">' .
             '<a href="album.php?act=list&amp;user=' . $res['user_id'] . '"><b>' . $res['user_name'] . '</b></a> | <a href="album.php?act=show&amp;al=' . $res['album_id'] . '&amp;user=' . $res['user_id'] . '">' . Validate::filterString($res['album_name']) . '</a>';
        if ($res['access'] == 4 || Vars::$USER_RIGHTS >= 6) {
            echo vote_photo($res) .
                '<div class="gray">' . Vars::$LNG['count_views'] . ': ' . $res['views'] . ', ' . Vars::$LNG['count_downloads'] . ': ' . $res['downloads'] . '</div>' .
                '<div class="gray">' . Vars::$LNG['date'] . ': ' . Functions::displayDate($res['time']) . '</div>' .
                '<a href="album.php?act=comments&amp;img=' . $res['id'] . '">' . Vars::$LNG['comments'] . '</a> (' . $res['comm_count'] . ')' .
                '<br /><a href="album.php?act=image_download&amp;img=' . $res['id'] . '">' . Vars::$LNG['download'] . '</a>';
        }
        echo '</div></div>';
    }
} else {
    echo '<div class="menu"><p>' . Vars::$LNG['list_empty'] . '</p></div>';
}
echo '<div class="phdr">' . Vars::$LNG['total'] . ': ' . $total . '</div>';
if ($total > Vars::$USER_SET['page_size']) {
    echo '<div class="topmenu">' . Functions::displayPagination('album.php?act=top' . $link . '&amp;', Vars::$START, $total, Vars::$USER_SET['page_size']) . '</div>' .
        '<p><form action="album.php?act=top' . $link . '" method="post">' .
        '<input type="text" name="page" size="2"/>' .
        '<input type="submit" value="' . Vars::$LNG['to_page'] . ' &gt;&gt;"/>' .
        '</form></p>';
}