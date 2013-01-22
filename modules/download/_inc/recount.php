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

if (Vars::$USER_RIGHTS == 4 || Vars::$USER_RIGHTS >= 6) {
    $req_down = DB::PDO()->query("SELECT `dir`, `name`, `id` FROM `cms_download_category`");
    while ($res_down = $req_down->fetch()) {
        $dir_files = DB::PDO()->query("SELECT COUNT(*) FROM `cms_download_files` WHERE `type` = '2' AND `dir` LIKE '" . ($res_down['dir']) . "%'")->fetchColumn();
        DB::PDO()->exec("UPDATE `cms_download_category` SET `total` = '$dir_files' WHERE `id` = '" . $res_down['id'] . "'");
    }
}
header('Location: ' . Router::getUri(2) . '?id=' . Vars::$ID);