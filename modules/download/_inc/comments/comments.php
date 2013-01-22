<?php

/**
 * @package     JohnCMS
 * @link        http://johncms.com
 * @copyright   Copyright (C) 2008-2011 JohnCMS Community
 * @license     LICENSE.txt (see attached file)
 * @version     VERSION.txt (see attached file)
 * @author      http://johncms.com/about
 */

defined('_IN_JOHNCMS') or die('Error: restricted access');
$url = Router::getUri(2);

/*
-----------------------------------------------------------------
Комментарии
-----------------------------------------------------------------
*/
if (!Vars::$SYSTEM_SET['mod_down_comm'] && Vars::$USER_RIGHTS < 7) {
    echo Functions::displayError(__('comments_cloded'), '<a href="' . $url . '">' . __('download_title') . '</a>');
    exit;
}
$req_down = DB::PDO()->query("SELECT * FROM `cms_download_files` WHERE `id` = '" . Vars::$ID . "' AND (`type` = 2 OR `type` = 3)  LIMIT 1");
$res_down = $req_down->fetch();
if (!$req_down->rowCount() || !is_file($res_down['dir'] . '/' . $res_down['name']) || ($res_down['type'] == 3 && Vars::$USER_RIGHTS < 6 && Vars::$USER_RIGHTS != 4)) {
    echo Functions::displayError(__('not_found_file'), '<a href="' . $url . '">' . __('download_title') . '</a>');
    exit;
}
if (!Vars::$SYSTEM_SET['mod_down_comm'])
    echo '<div class="rmenu">' . __('comments_cloded') . '</div>';
$title_pages = Validate::checkout(mb_substr($res_down['rus_name'], 0, 30));
$textl = __('comments') . ': ' . (mb_strlen($res_down['rus_name']) > 30 ? $title_pages . '...' : $title_pages);
/*
-----------------------------------------------------------------
Параметры комментариев
-----------------------------------------------------------------
*/
$arg = array(
    'object_comm_count' => 'total', // Поле с числом комментариев
    'comments_table'    => 'cms_download_comments', // Таблица с комментариями
    'object_table'      => 'cms_download_files', // Таблица комментируемых объектов
    'script'            => $url . '?act=comments', // Имя скрипта (с параметрами вызова)
    'sub_id_name'       => 'id', // Имя идентификатора комментируемого объекта
    'sub_id'            => Vars::$ID, // Идентификатор комментируемого объекта
    'owner'             => FALSE, // Владелец объекта
    'owner_delete'      => FALSE, // Возможность владельцу удалять комментарий
    'owner_reply'       => FALSE, // Возможность владельцу отвечать на комментарий
    'owner_edit'        => FALSE, // Возможность владельцу редактировать комментарий
    'title'             => __('comments'), // Название раздела
    'context_top'       => '<div class="phdr"><b>' . $textl . '</b></div>', // Выводится вверху списка
    'context_bottom'    => '<p><a href="' . $url . '?act=view&amp;id=' . Vars::$ID . '">' . __('back') . '</a></p>' // Выводится внизу списка
);
/*
-----------------------------------------------------------------
Показываем комментарии
-----------------------------------------------------------------
*/
$comm = new comments($arg);