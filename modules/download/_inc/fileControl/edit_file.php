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
Редактирование файла
-----------------------------------------------------------------
*/
$req_down = mysql_query("SELECT * FROM `cms_download_files` WHERE `id` = '" . VARS::$ID . "' AND (`type` = 2 OR `type` = 3)  LIMIT 1");
$res_down = mysql_fetch_assoc($req_down);
if (mysql_num_rows($req_down) == 0 || !is_file($res_down['dir'] . '/' . $res_down['name']) || (Vars::$USER_RIGHTS < 6 && Vars::$USER_RIGHTS != 4)) {
    echo Functions::displayError('<a href="' . Vars::$URI . '">' . __('download_title') . '</a>');
    exit;
}
if (isset($_POST['submit'])) {
    $name = isset($_POST['text']) ? trim($_POST['text']) : NULL;
    $name_link = isset($_POST['name_link']) ? mysql_real_escape_string(Validate::checkout(mb_substr($_POST['name_link'], 0, 200))) : NULL;
    if ($name_link && $name) {
        $name = mysql_real_escape_string($name);
        mysql_query("UPDATE `cms_download_files` SET `rus_name`='$name', `text` = '$name_link' WHERE `id` = '" . Vars::$ID ."' LIMIT 1");
        header('Location: ' . Vars::$URI . '?act=view&id=' . Vars::$ID);
    } else
        echo functions::displayError(__('error_empty_fields'), '<a href="' . Vars::$URI . '?act=edit_file&amp;id=' . Vars::$ID . '">' . __('repeat') . '</a>');
} else {
    $file_name = Validate::checkout($res_down['rus_name']);
    echo '<div class="phdr"><b>' . $file_name . '</b></div>' .
    '<div class="list1"><form action="' . Vars::$URI . '?act=edit_file&amp;id=' . Vars::$ID . '" method="post">' .
    __('name_file') . '(мах. 200):<br /><input type="text" name="text" value="' . $file_name . '"/><br />' .
    __('link_file') . ' (мах. 200):<br /><input type="text" name="name_link" value="' . $res_down['text'] . '"/><br />' .
    '<input type="submit" name="submit" value="' . __('sent') . '"/></form></div>' .
    '<div class="phdr"><a href="' . Vars::$URI . '?act=view&amp;id=' . Vars::$ID . '">' . __('back') . '</a></div>';
}