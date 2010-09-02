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

/*
-----------------------------------------------------------------
Показываем выбранный альбом с фотографиями
-----------------------------------------------------------------
*/
if (!$al) {
    echo display_error($lng['error_wrong_data']);
    require('../../incfiles/end.php');
    exit;
}
$req = mysql_query("SELECT * FROM `cms_album_cat` WHERE `id` = '$al' LIMIT 1");
if (!mysql_num_rows($req)) {
    echo display_error($lng['error_wrong_data']);
    require('../../incfiles/end.php');
    exit;
}
$album = mysql_fetch_assoc($req);
echo '<div class="phdr">' .
    '<a href="../profile/index.php?id=' . $user['id'] . '"><b>' . ($id && $id != $user_id ? $lng_profile['user_profile'] : $lng_profile['my_profile']) . '</b></a> | ' .
    '<a href="index.php?id=' . $user['id'] . '">' . $lng['photo_album'] . '</a></div>';
if ($user['id'] == $user_id || $rights >= 7)
    echo '<div class="topmenu"><a href="index.php?act=image_upload&amp;al=' . $al . '&amp;id=' . $user['id'] . '">' . $lng_profile['image_add'] . '</a></div>';
echo '<div class="user"><p>' . display_user($user, array ('iphide' => 1,)) . '</p></div>' .
    '<div class="phdr">' . $lng_profile['album'] . ': <b>' . checkout($album['name']) . '</b><br />' . checkout($album['description'], 1) . '</div>';

/*
-----------------------------------------------------------------
Проверяем права доступа к альбому
-----------------------------------------------------------------
*/
if ($album['access'] != 2)
    unset($_SESSION['ap']);
if ($album['access'] == 1 && $user['id'] != $user_id) {
    // Если доступ закрыт
    echo display_error($lng['access_forbidden']) .
        '<div class="phdr"><a href="index.php?id=' . $user['id'] . '">' . $lng_profile['album_list'] . '</a></div>';
    require('../../incfiles/end.php');
    exit;
} elseif ($album['access'] == 2 && $user['id'] != $user_id && $rights < 6) {
    // Если доступ через пароль
    if (isset($_POST['password'])) {
        if ($album['password'] == trim($_POST['password']))
            $_SESSION['ap'] = $album['password'];
        else
            echo display_error($lng['error_wrong_password']);
    }
    if (!isset($_SESSION['ap']) || $_SESSION['ap'] != $album['password']) {
        echo '<form action="index.php?act=album&amp;al=' . $al . '&amp;id=' . $user['id'] . '" method="post"><div class="menu"><p>';
        echo $lng_profile['album_password'] . '<br />';
        echo '<input type="text" name="password"/></p>';
        echo '<p><input type="submit" name="submit" value="' . $lng['login'] . '"/></p>';
        echo '</div></form>';
        echo '<div class="phdr"><a href="index.php?id=' . $user['id'] . '">' . $lng_profile['album_list'] . '</a></div>';
        require('../../incfiles/end.php');
        exit;
    }
}
if ($img) {
    /*
    -----------------------------------------------------------------
    Предпросмотр фотографии
    -----------------------------------------------------------------
    */
    $req = mysql_query("SELECT * FROM `cms_album_files` WHERE `id` = '" . $img . "' AND `user_id` = '" . $user['id'] . "' AND `album_id` = '$al' LIMIT 1");
    if (mysql_num_rows($req)) {
        $res = mysql_fetch_assoc($req);
        echo '<div class="menu">' .
            '<a href="' . htmlspecialchars($_SERVER['HTTP_REFERER']) . '"><img src="image.php?u=' . $user['id'] . '&amp;f=' . $res['img_name'] . '" /></a>';
        if (!empty($res['description']))
            echo '<br />' . smileys(checkout($res['description'], 1, 1)) . '';
        echo '<div class="sub"><ul>' .
            '<li><a href="">' . $lng['comments'] . '</a></li>' .
            '<li><a href="../../files/users/album/' . $user['id'] . '/' . $res['img_name'] . '">' . $lng['download'] . '</a></li>' .
            '</ul></div></div>' .
            '<div class="phdr"><a href="index.php?act=album&amp;al=' . $al . '&amp;id=' . $user['id'] . '">' . $lng['back'] . '</a></div>';
    } else {
        echo display_error($lng['error_wrong_data']);
    }
} else {
    /*
    -----------------------------------------------------------------
    Просмотр альбома
    -----------------------------------------------------------------
    */
    $total = mysql_result(mysql_query("SELECT COUNT(*) FROM `cms_album_files` WHERE `user_id` = '" . $user['id'] . "' AND `album_id` = '$al'"), 0);
    if ($total > $kmess)
        echo '<div class="topmenu">' . display_pagination('index.php?act=album&amp;al=' . $al . '&amp;id=' . $user['id'] . '&amp;', $start, $total, $kmess) . '</div>';
    if ($total) {
        $req = mysql_query("SELECT * FROM `cms_album_files` WHERE `user_id` = '" . $user['id'] . "' AND `album_id` = '$al' ORDER BY `time` DESC LIMIT $start, $kmess");
        while ($res = mysql_fetch_assoc($req)) {
            echo ($i % 2 ? '<div class="list2">' : '<div class="list1">') .
                '<a href="index.php?act=album&amp;al=' . $al . '&amp;img=' . $res['id'] . '&amp;id=' . $user['id'] . '"><img src="../../files/users/album/' . $user['id'] . '/' . $res['tmb_name'] . '" /></a>' .
                '<div class="sub">' . checkout($res['description'], 1);
            if ($user['id'] == $user_id || $rights >= 6) {
                echo '<p><a href="index.php?act=image_edit&amp;img=' . $res['id'] . '&amp;id=' . $user['id'] . '">' . $lng['edit'] . '</a> | ' .
                    '<a href="index.php?act=image_move&amp;img=' . $res['id'] . '&amp;id=' . $user['id'] . '">' . $lng['move'] . '</a> | ' .
                    '<a href="index.php?act=image_delete&amp;img=' . $res['id'] . '&amp;id=' . $user['id'] . '">' . $lng['delete'] . '</a></p>';
            }
            echo '<ul>' .
                '<li><a href="">' . $lng['comments'] . '</a></li>' .
                '<li><a href="../../files/users/album/' . $user['id'] . '/' . $res['img_name'] . '">' . $lng['download'] . '</a></li>' .
                '</ul>' .
                '</div></div>';
            ++$i;
        }
    } else {
        echo '<div class="menu"><p>' . $lng['list_empty'] . '</p></div>';
    }
    echo '<div class="phdr">' . $lng['total'] . ': ' . $total . '</div>';
    if ($total > $kmess) {
        echo '<div class="topmenu">' . display_pagination('index.php?act=album&amp;al=' . $al . '&amp;id=' . $user['id'] . '&amp;', $start, $total, $kmess) . '</div>' .
            '<p><form action="index.php?act=album&amp;al=' . $al . '&amp;id=' . $user['id'] . '" method="post">' .
            '<input type="text" name="page" size="2"/>' .
            '<input type="submit" value="' . $lng['to_page'] . ' &gt;&gt;"/>' .
            '</form></p>';
    }
    echo '<p><a href="index.php?id=' . $user['id'] . '">' . $lng_profile['album_list'] . '</a></p>';
}
?>