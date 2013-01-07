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
$url = Router::getUrl(2);

/*
-----------------------------------------------------------------
Топ файлов
-----------------------------------------------------------------
*/
if (Vars::$ID == 2) {
    $textl = __('top_files_comments');
} elseif (Vars::$ID == 1) {
    $textl = __('top_files_download');
} else {
    $textl = __('top_files_popular');
}

$linkTopComments = Vars::$SYSTEM_SET['mod_down_comm'] || Vars::$USER_RIGHTS >= 7 ? '<br /><a href="' . $url . '?act=top_files&amp;id=2">' . __('top_files_comments') . '</a>' : '';
echo '<div class="phdr"><a href="' . Router::getUrl(2) . '"><b>' . __('downloads') . '</b></a> | ' . $textl . ' (' . $set_down['top'] . ')</div>';
if (Vars::$ID == 2 && (Vars::$SYSTEM_SET['mod_down_comm'] || Vars::$USER_RIGHTS >= 7)) {
    echo '<div class="gmenu"><a href="' . $url . '?act=top_files&amp;id=0">' . __('top_files_popular') . '</a><br />' .
        '<a href="' . $url . '?act=top_files&amp;id=1">' . __('top_files_download') . '</a></div>';
    $sql = '`total`';
} elseif (Vars::$ID == 1) {
    echo '<div class="gmenu"><a href="' . $url . '?act=top_files&amp;id=0">' . __('top_files_popular') . '</a>' . $linkTopComments . '</div>';
    $sql = '`field`';
} else {
    echo '<div class="gmenu"><a href="' . $url . '?act=top_files&amp;id=1">' . __('top_files_download') . '</a>' . $linkTopComments . '</div>';
    $sql = '`rate`';
}
/*
-----------------------------------------------------------------
Выводим список
-----------------------------------------------------------------
*/
$req_down = mysql_query("SELECT * FROM `cms_download_files` WHERE `type` = 2 ORDER BY $sql DESC LIMIT " . $set_down['top']);
$i = 0;
while ($res_down = mysql_fetch_assoc($req_down)) {
    echo (($i++ % 2) ? '<div class="list2">' : '<div class="list1">') . Download::displayFile($res_down, 1) . '</div>';
}
echo '<div class="phdr"><a href="' . $url . '">' . __('download_title') . '</a></div>';