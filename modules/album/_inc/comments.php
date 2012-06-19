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

// Проверяем наличие комментируемого объекта
$req_obj = mysql_query("SELECT * FROM `cms_album_files` WHERE `id` = '$img'");
if (mysql_num_rows($req_obj)) {
    $res_obj = mysql_fetch_assoc($req_obj);

    /*
    -----------------------------------------------------------------
    Получаем данные владельца Альбома
    -----------------------------------------------------------------
    */
    $owner = Functions::getUser($res_obj['user_id']);
    if (!$owner) {
        echo Functions::displayError(lng('user_does_not_exist'));
        exit;
    }

    /*
    -----------------------------------------------------------------
    Показываем выбранную картинку
    -----------------------------------------------------------------
    */
    unset($_SESSION['ref']);
    $req_a = mysql_query("SELECT * FROM `cms_album_cat` WHERE `id` = '" . $res_obj['album_id'] . "'");
    $res_a = mysql_fetch_assoc($req_a);
    if ($res_a['access'] == 1 && $owner['id'] != Vars::$USER_ID && Vars::$USER_RIGHTS < 6) {
        // Если доступ закрыт
        echo Functions::displayError(lng('access_forbidden')) .
            '<div class="phdr"><a href="' . Vars::$URI . '?act=list&amp;user=' . $owner['id'] . '">' . lng('album_list') . '</a></div>';
        exit;
    }
    $context_top = '<div class="phdr"><a href="' . Vars::$URI . '"><b>' . lng('photo_albums') . '</b></a> | ' .
        '<a href="' . Vars::$URI . '?act=list&amp;user=' . $owner['id'] . '">' . lng('personal_2') . '</a></div>' .
        '<div class="menu"><a href="' . Vars::$URI . '?act=show&amp;al=' . $res_obj['album_id'] . '&amp;img=' . $img . '&amp;user=' . $owner['id'] . '&amp;view"><img src="' . Vars::$HOME_URL . '/files/users/album/' . $owner['id'] . '/' . $res_obj['tmb_name'] . '" /></a>';
    if (!empty($res_obj['description']))
        $context_top .= '<div class="gray">' . Functions::smileys(Validate::filterString($res_obj['description'], 1)) . '</div>';
    $context_top .= '<div class="sub">' .
        '<a href="profile.php?user=' . $owner['id'] . '"><b>' . $owner['nickname'] . '</b></a> | ' .
        '<a href="' . Vars::$URI . '?act=show&amp;al=' . $res_a['id'] . '&amp;user=' . $owner['id'] . '">' . Validate::filterString($res_a['name']) . '</a>';
    if ($res_obj['access'] == 4 || Vars::$USER_RIGHTS >= 7) {
        $context_top .= Album::vote($res_obj) .
            '<div class="gray">' . lng('count_views') . ': ' . $res_obj['views'] . ', ' . lng('count_downloads') . ': ' . $res_obj['downloads'] . '</div>' .
            '<a href="' . Vars::$URI . '?act=image_download&amp;img=' . $res_obj['id'] . '">' . lng('download') . '</a>';
    }
    $context_top .= '</div></div>';

    /*
    -----------------------------------------------------------------
    Параметры комментариев
    -----------------------------------------------------------------
    */
    $arg = array(
        'comments_table' => 'cms_album_comments',          // Таблица с комментариями
        'object_table'   => 'cms_album_files',             // Таблица комментируемых объектов
        'script'         => Vars::$URI . '?act=comments',  // Имя скрипта (с параметрами вызова)
        'sub_id_name'    => 'img',                         // Имя идентификатора комментируемого объекта
        'sub_id'         => $img,                          // Идентификатор комментируемого объекта
        'owner'          => $owner['id'],                  // Владелец объекта
        'owner_delete'   => TRUE,                          // Возможность владельцу удалять комментарий
        'owner_reply'    => TRUE,                          // Возможность владельцу отвечать на комментарий
        'owner_edit'     => FALSE,                         // Возможность владельцу редактировать комментарий
        'title'          => lng('comments'),               // Название раздела
        'context_top'    => $context_top,                  // Выводится вверху списка
        'context_bottom' => ''                             // Выводится внизу списка
    );

    /*
    -----------------------------------------------------------------
    Ставим метку прочтения
    -----------------------------------------------------------------
    */
    if (Vars::$USER_ID == $user['id'] && $res_obj['unread_comments'])
        mysql_query("UPDATE `cms_album_files` SET `unread_comments` = '0' WHERE `id` = '$img' LIMIT 1");

    /*
    -----------------------------------------------------------------
    Показываем комментарии
    -----------------------------------------------------------------
    */
    $comm = new Comments($arg);

    /*
    -----------------------------------------------------------------
    Обрабатываем метки непрочитанных комментариев
    -----------------------------------------------------------------
    */
    if ($comm->added)
        mysql_query("UPDATE `cms_album_files` SET `unread_comments` = '1' WHERE `id` = '$img' LIMIT 1");
} else {
    echo Functions::displayError(lng('error_wrong_data'));
}