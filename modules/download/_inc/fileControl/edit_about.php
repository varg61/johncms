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
Редактирование описания файла
-----------------------------------------------------------------
*/
$req_down = mysql_query("SELECT * FROM `cms_download_files` WHERE `id` = '" . VARS::$ID . "' AND (`type` = 2 OR `type` = 3)  LIMIT 1");
$res_down = mysql_fetch_assoc($req_down);
if (mysql_num_rows($req_down) == 0 || !is_file($res_down['dir'] . '/' . $res_down['name']) || (Vars::$USER_RIGHTS < 6 && Vars::$USER_RIGHTS != 4)) {
    echo Functions::displayError('<a href="' . Vars::$URI . '">' . lng('download_title') . '</a>');
    exit;
}
if (isset($_POST['submit'])) {
    $text = isset($_POST['opis']) ? mysql_real_escape_string(trim($_POST['opis'])) : '';
    mysql_query("UPDATE `cms_download_files` SET `about` = '$text' WHERE `id` = '" . Vars::$ID ."' LIMIT 1");
    header('Location: ' . Vars::$URI . '?act=view&id=' . Vars::$ID);
} else {
    echo '<div class="phdr"><b>' . lng('dir_desc') . ':</b> ' . Validate::filterString($res_down['rus_name']) . '</div>' .
    '<div class="list1"><form action="' . Vars::$URI . '?act=edit_about&amp;id=' . Vars::$ID . '" method="post">' .
    '<small>' . lng('desc_file_faq') . '</small><br />' .
    '<textarea name="opis">' . htmlentities($res_down['about'], ENT_QUOTES, 'UTF-8') . '</textarea><br />' .
    '<input type="submit" name="submit" value="' . lng('sent') . '"/></form></div>' .
    '<div class="phdr"><a href="' . Vars::$URI . '?act=view&amp;id=' . Vars::$ID . '">' . lng('back') . '</a></div>';
}