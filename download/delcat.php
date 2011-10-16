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
require_once("../incfiles/head.php");
function deletcat($catalog)
{
    $dir = opendir($catalog);

    while (($file = readdir($dir))) {
        if (is_file($catalog . "/" . $file)) {
            unlink($catalog . "/" . $file);
        } else if (is_dir($catalog . "/" . $file) && ($file != ".") && ($file != "..")) {
            deletcat($catalog . "/" . $file);
        }
    }
    closedir($dir);
    rmdir($catalog);
}

if (($rights == 4 || $rights >= 6) && (!empty($_GET['cat']))) {
    $cat = intval($_GET['cat']);
    $delcat = mysql_query("select * from `download` where type = 'cat' and refid = '$cat'");
    $delcat1 = mysql_num_rows($delcat);
    if ($delcat1 == 0) {
        if (isset($_POST['submit'])) {
            provcat($cat);
            $cat1 = mysql_query("select * from `download` where `type` = 'cat' and `id` = '$cat'");
            $adrdir = mysql_fetch_array($cat1);
            deletcat("$adrdir[adres]/$adrdir[name]");
            mysql_query("DELETE FROM `download` WHERE `id` = '$cat'");
            echo '<p>' . $lng_dl['folder_deleted'] . '<br /><a href="index.php">' . $lng['continue'] . '</a></p>';
        } else {
            echo '<p>' . $lng['delete_confirmation'] . '</p>' .
                 '<form action="index.php?act=delcat&amp;cat=' . $cat . '" method="post">' .
                 '<input type="submit" name="submit" value="' . $lng['delete'] . '" />' .
                 '</form><p><a href="index.php?cat=' . $cat . '">' . $lng['cancel'] . '</a></p>';
        }
    }
}