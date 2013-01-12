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
�������� ������
-----------------------------------------------------------------
*/
if (Vars::$USER_RIGHTS == 4 || Vars::$USER_RIGHTS >= 6) {
	$req_down = mysql_query("SELECT `dir`, `name`, `id` FROM `cms_download_category`");
	while ($res_down = mysql_fetch_assoc($req_down)) {
		$dir_files = mysql_result(mysql_query("SELECT COUNT(*) FROM `cms_download_files` WHERE `type` = '2' AND `dir` LIKE '" . ($res_down['dir']) . "%'"), 0);
		mysql_query("UPDATE `cms_download_category` SET `total` = '$dir_files' WHERE `id` = '" . $res_down['id'] . "'");
	}
}
header('Location: ' . Router::getUri(2) . '?id=' . Vars::$ID);