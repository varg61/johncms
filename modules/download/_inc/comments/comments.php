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
/*
-----------------------------------------------------------------
Комментарии
-----------------------------------------------------------------
*/
if(!Vars::$SYSTEM_SET['mod_down_comm'] && Vars::$USER_RIGHTS < 7) {
	echo Functions::displayError(lng('comments_cloded'), '<a href="' . Vars::$URI . '">' . lng('download_title') . '</a>');
	exit;
}
$req_down = mysql_query("SELECT * FROM `cms_download_files` WHERE `id` = '" . Vars::$ID . "' AND (`type` = 2 OR `type` = 3)  LIMIT 1");
$res_down = mysql_fetch_assoc($req_down);
if (mysql_num_rows($req_down) == 0 || !is_file($res_down['dir'] . '/' . $res_down['name']) || ($res_down['type'] == 3 && Vars::$USER_RIGHTS < 6 && Vars::$USER_RIGHTS != 4)) {
    echo Functions::displayError(lng('not_found_file'), '<a href="' . Vars::$URI . '">' . lng('download_title') . '</a>');
    exit;
}
if(!Vars::$SYSTEM_SET['mod_down_comm'])
	echo '<div class="rmenu">' . lng('comments_cloded') . '</div>';
$title_pages = Validate::filterString(mb_substr($res_down['rus_name'], 0, 30));
$textl = lng('comments') . ': ' . (mb_strlen($res_down['rus_name']) > 30 ? $title_pages . '...' : $title_pages);
/*
-----------------------------------------------------------------
Параметры комментариев
-----------------------------------------------------------------
*/
$arg = array (
	'object_comm_count' => 'total',                                                             // Поле с числом комментариев
	'comments_table' => 'cms_download_comments',                              					// Таблица с комментариями
	'object_table' => 'cms_download_files',                                   					// Таблица комментируемых объектов
	'script' => Vars::$URI . '?act=comments',                                					// Имя скрипта (с параметрами вызова)
	'sub_id_name' => 'id',                                            							// Имя идентификатора комментируемого объекта
	'sub_id' => Vars::$ID,                                                  					// Идентификатор комментируемого объекта
	'owner' => false,                                                 							// Владелец объекта
	'owner_delete' => false,                                          							// Возможность владельцу удалять комментарий
	'owner_reply' => false,                                           							// Возможность владельцу отвечать на комментарий
	'owner_edit' => false,                                            							// Возможность владельцу редактировать комментарий
	'title' => lng('comments'),                                         						// Название раздела
	'context_top' => '<div class="phdr"><b>' . $textl . '</b></div>', 							// Выводится вверху списка
	'context_bottom' => '<p><a href="' . Vars::$URI . '?act=view&amp;id=' . Vars::$ID . '">' . lng('back') . '</a></p>'	// Выводится внизу списка
);
/*
-----------------------------------------------------------------
Показываем комментарии
-----------------------------------------------------------------
*/
$comm = new comments($arg);