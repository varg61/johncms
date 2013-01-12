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
Редактирование описания файла
-----------------------------------------------------------------
*/
$req_down = mysql_query("SELECT * FROM `cms_download_files` WHERE `id` = '" . VARS::$ID . "' AND (`type` = 2 OR `type` = 3)  LIMIT 1");
$res_down = mysql_fetch_assoc($req_down);
if (mysql_num_rows($req_down) == 0 || !is_file($res_down['dir'] . '/' . $res_down['name']) || (Vars::$USER_RIGHTS < 6 && Vars::$USER_RIGHTS != 4)) {
    echo Functions::displayError('<a href="' . $url . '">' . __('download_title') . '</a>');
    exit;
}
if (isset($_POST['submit'])) {
    $text = isset($_POST['opis']) ? mysql_real_escape_string(trim($_POST['opis'])) : '';
    mysql_query("UPDATE `cms_download_files` SET `about` = '$text' WHERE `id` = '" . Vars::$ID . "' LIMIT 1");
    header('Location: ' . $url . '?act=view&id=' . Vars::$ID);
} else {
    echo '<div class="phdr"><b>' . __('dir_desc') . ':</b> ' . Validate::checkout($res_down['rus_name']) . '</div>' .
        '<div class="list1"><form action="' . $url . '?act=edit_about&amp;id=' . Vars::$ID . '" method="post">' .
        '<small>' . __('desc_file_faq') . '</small><br />' .
        '<textarea name="opis">' . htmlentities($res_down['about'], ENT_QUOTES, 'UTF-8') . '</textarea><br />' .
        '<input type="submit" name="submit" value="' . __('sent') . '"/></form></div>' .
        '<div class="phdr"><a href="' . $url . '?act=view&amp;id=' . Vars::$ID . '">' . __('back') . '</a></div>';
}