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
$url = Router::getUri(2);

$error = false;
if (Vars::$ID) {
    /*
    -----------------------------------------------------------------
    Скачивание прикрепленного файла Форума
    -----------------------------------------------------------------
    */
    $req = DB::PDO()->query("SELECT * FROM `cms_forum_files` WHERE `id` = " . Vars::$ID);
    if ($req->rowCount()) {
        $res = $req->fetch();
        if (file_exists(ROOTPATH . 'files' . DIRECTORY_SEPARATOR . 'forum' . DIRECTORY_SEPARATOR . $res['filename'])) {
            DB::PDO()->exec("UPDATE `cms_forum_files` SET  `dlcount` = '" . ++$res['dlcount'] . "' WHERE `id` = " . Vars::$ID);
            header('location: ' . Vars::$HOME_URL . 'files/forum/' . $res['filename']);
        } else {
            $error = true;
        }
    } else {
        $error = true;
    }
    if ($error) {
        echo Functions::displayError(__('error_file_not_exist'), '<a href="' . $url . '">' . __('to_forum') . '</a>');
        exit;
    }
} else {
    header('location: ' . $url);
}