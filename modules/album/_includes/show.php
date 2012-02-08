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

if (!$al) {
    echo Functions::displayError(Vars::$LNG['error_wrong_data']);
    exit;
}
$req = mysql_query("SELECT * FROM `cms_album_cat` WHERE `id` = '$al'");
if (!mysql_num_rows($req)) {
    echo Functions::displayError(Vars::$LNG['error_wrong_data']);
    exit;
}
$album = mysql_fetch_assoc($req);
$view = isset($_GET['view']);

/*
-----------------------------------------------------------------
Показываем выбранный альбом с фотографиями
-----------------------------------------------------------------
*/
echo '<div class="phdr"><a href="album.php"><b>' . Vars::$LNG['photo_albums'] . '</b></a> | <a href="album.php?act=list&amp;user=' . $user['user_id'] . '">' . Vars::$LNG['personal_2'] . '</a></div>';
if ($user['user_id'] == Vars::$USER_ID || Vars::$USER_RIGHTS >= 7)
    echo '<div class="topmenu"><a href="album.php?act=image_upload&amp;al=' . $al . '&amp;user=' . $user['user_id'] . '">' . $lng['image_add'] . '</a></div>';
echo'<div class="user"><p>' . Functions::displayUser($user, array('iphide' => 1,)) . '</p></div>' .
    '<div class="phdr">' . $lng['album'] . ': ';
echo $view ? '<a href="album.php?act=show&amp;al=' . $al . '&amp;user=' . $user['user_id'] . '"><b>' . Validate::filterString($album['name']) . '</b></a>' : '<b>' . Validate::filterString($album['name']) . '</b>';
if (!empty($album['description'])) {
    echo '<small><br />' . Validate::filterString($album['description'], 1) . '</small>';
}
echo'</div>';

/*
-----------------------------------------------------------------
Проверяем права доступа к альбому
-----------------------------------------------------------------
*/
if ($album['access'] != 2)
    unset($_SESSION['ap']);
if ($album['access'] == 1 && $user['user_id'] != Vars::$USER_ID && Vars::$USER_RIGHTS < 6) {
    // Если доступ закрыт
    echo Functions::displayError(Vars::$LNG['access_forbidden']) .
        '<div class="phdr"><a href="album.php?act=list&amp;user=' . $user['user_id'] . '">' . $lng['album_list'] . '</a></div>';
    exit;
} elseif ($album['access'] == 2 && $user['user_id'] != Vars::$USER_ID && Vars::$USER_RIGHTS < 6) {
    // Если доступ через пароль
    if (isset($_POST['password'])) {
        if ($album['password'] == trim($_POST['password']))
            $_SESSION['ap'] = $album['password'];
        else
            echo Functions::displayError(Vars::$LNG['error_wrong_password']);
    }
    if (!isset($_SESSION['ap']) || $_SESSION['ap'] != $album['password']) {
        echo '<form action="album.php?act=show&amp;al=' . $al . '&amp;user=' . $user['user_id'] . '" method="post"><div class="menu"><p>';
        echo $lng['album_password'] . '<br />';
        echo '<input type="text" name="password"/></p>';
        echo '<p><input type="submit" name="submit" value="' . Vars::$LNG['login'] . '"/></p>';
        echo '</div></form>';
        echo '<div class="phdr"><a href="album.php?act=list&amp;user=' . $user['user_id'] . '">' . $lng['album_list'] . '</a></div>';
        exit;
    }
}

/*
-----------------------------------------------------------------
Просмотр альбома и фотографий
-----------------------------------------------------------------
*/
if ($view) {
    Vars::$USER_SET['page_size'] = 1;
    Vars::$START = isset($_REQUEST['page']) ? Vars::$PAGE - 1 : (mysql_result(mysql_query("SELECT COUNT(*) FROM `cms_album_files` WHERE `album_id` = '$al' AND `id` > '$img'"), 0));
    // Обрабатываем ссылку для возврата
    if (empty($_SESSION['ref']))
        $_SESSION['ref'] = htmlspecialchars($_SERVER['HTTP_REFERER']);
} else {
    unset($_SESSION['ref']);
}
$total = mysql_result(mysql_query("SELECT COUNT(*) FROM `cms_album_files` WHERE `album_id` = '$al'"), 0);
if ($total > Vars::$USER_SET['page_size'])
    echo '<div class="topmenu">' . Functions::displayPagination('album.php?act=show&amp;al=' . $al . '&amp;user=' . $user['user_id'] . '&amp;' . ($view ? 'view&amp;' : ''), Vars::$START, $total, Vars::$USER_SET['page_size']) . '</div>';
if ($total) {
    $req = mysql_query("SELECT * FROM `cms_album_files` WHERE `user_id` = '" . $user['user_id'] . "' AND `album_id` = '$al' ORDER BY `id` DESC LIMIT " . Vars::db_pagination());
    $i = 0;
    while (($res = mysql_fetch_assoc($req)) !== false) {
        echo ($i % 2 ? '<div class="list2">' : '<div class="list1">');
        if ($view) {
            /*
            -----------------------------------------------------------------
            Предпросмотр отдельного изображения
            -----------------------------------------------------------------
            */
            if ($user['user_id'] == Vars::$USER_ID && isset($_GET['profile'])) {
                copy(
                    '../files/users/album/' . $user['user_id'] . '/' . $res['tmb_name'],
                    '../files/users/photo/' . Vars::$USER_ID . '_small.jpg'
                );
                copy(
                    '../files/users/album/' . $user['user_id'] . '/' . $res['img_name'],
                    '../files/users/photo/' . Vars::$USER_ID . '.jpg'
                );
                echo '<span class="green"><b>' . $lng['photo_profile_ok'] . '</b></span><br />';
            }
            echo '<a href="' . $_SESSION['ref'] . '"><img src="image.php?u=' . $user['user_id'] . '&amp;f=' . $res['img_name'] . '" /></a>';
            // Счетчик просмотров
            if (!mysql_result(mysql_query("SELECT COUNT(*) FROM `cms_album_views` WHERE `user_id` = '" . Vars::$USER_ID . "' AND `file_id` = '" . $res['id'] . "'"), 0)) {
                mysql_query("INSERT INTO `cms_album_views` SET `user_id` = '" . Vars::$USER_ID . "', `file_id` = '" . $res['id'] . "', `time` = '" . time() . "'");
                $views = mysql_result(mysql_query("SELECT COUNT(*) FROM `cms_album_views` WHERE `file_id` = '" . $res['id'] . "'"), 0);
                mysql_query("UPDATE `cms_album_files` SET `views` = '$views' WHERE `id` = '" . $res['id'] . "'");
            }
        } else {
            /*
            -----------------------------------------------------------------
            Предпросмотр изображения в списке
            -----------------------------------------------------------------
            */
            echo '<a href="album.php?act=show&amp;al=' . $al . '&amp;img=' . $res['id'] . '&amp;user=' . $user['user_id'] . '&amp;view"><img src="../files/users/album/' . $user['user_id'] . '/' . $res['tmb_name'] . '" /></a>';
        }
        if (!empty($res['description']))
            echo '<div class="gray">' . Functions::smileys(Validate::filterString($res['description'], 1)) . '</div>';
        echo '<div class="sub">';
        if ($user['user_id'] == Vars::$USER_ID || Vars::$USER_RIGHTS >= 6) {
            echo Functions::displayMenu(array(
                '<a href="album.php?act=image_edit&amp;img=' . $res['id'] . '&amp;user=' . $user['user_id'] . '">' . Vars::$LNG['edit'] . '</a>',
                '<a href="album.php?act=image_move&amp;img=' . $res['id'] . '&amp;user=' . $user['user_id'] . '">' . Vars::$LNG['move'] . '</a>',
                '<a href="album.php?act=image_delete&amp;img=' . $res['id'] . '&amp;user=' . $user['user_id'] . '">' . Vars::$LNG['delete'] . '</a>'
            ));
            if ($user['user_id'] == Vars::$USER_ID && $view)
                echo ' | <a href="album.php?act=show&amp;al=' . $al . '&amp;user=' . $user['user_id'] . '&amp;view&amp;img=' . $res['id'] . '&amp;profile">' . $lng['photo_profile'] . '</a>';
        }
        echo vote_photo($res) .
            '<div class="gray">' . Vars::$LNG['count_views'] . ': ' . $res['views'] . ', ' . Vars::$LNG['count_downloads'] . ': ' . $res['downloads'] . '</div>' .
            '<div class="gray">' . Vars::$LNG['date'] . ': ' . Functions::displayDate($res['time']) . '</div>' .
            '<a href="album.php?act=comments&amp;img=' . $res['id'] . '">' . Vars::$LNG['comments'] . '</a> (' . $res['comm_count'] . ')<br />' .
            '<a href="album.php?act=image_download&amp;img=' . $res['id'] . '">' . Vars::$LNG['download'] . '</a>' .
            '</div></div>';
        ++$i;
    }
} else {
    echo '<div class="menu"><p>' . Vars::$LNG['list_empty'] . '</p></div>';
}
echo '<div class="phdr">' . Vars::$LNG['total'] . ': ' . $total . '</div>';
if ($total > Vars::$USER_SET['page_size']) {
    echo '<div class="topmenu">' . Functions::displayPagination('album.php?act=show&amp;al=' . $al . '&amp;user=' . $user['user_id'] . '&amp;' . ($view ? 'view&amp;' : ''), Vars::$START, $total, Vars::$USER_SET['page_size']) . '</div>' .
        '<p><form action="album.php?act=show&amp;al=' . $al . '&amp;user=' . $user['user_id'] . ($view ? '&amp;view' : '') . '" method="post">' .
        '<input type="text" name="page" size="2"/>' .
        '<input type="submit" value="' . Vars::$LNG['to_page'] . ' &gt;&gt;"/>' .
        '</form></p>';
}
echo '<p><a href="album.php?act=list&amp;user=' . $user['user_id'] . '">' . $lng['album_list'] . '</a></p>';