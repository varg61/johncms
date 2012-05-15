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

if (Vars::$USER_RIGHTS == 4 || Vars::$USER_RIGHTS >= 6) {
    if ($_GET['file'] == "") {
        echo Functions::displayError(lng('file_not_selected'), '<a href="' . Vars::$URI . '">' . lng('back') . '</a>');
        exit;
    }
    $file = intval(trim($_GET['file']));
    $file1 = mysql_query("select * from `download` where type = 'file' and id = '" . $file . "';");
    $file2 = mysql_num_rows($file1);
    $adrfile = mysql_fetch_array($file1);
    if (($file1 == 0) || (!is_file("$adrfile[adres]/$adrfile[name]"))) {
        echo Functions::displayError(lng('file_select_error'), '<a href="' . Vars::$URI . '">' . lng('back') . '</a>');
        exit;
    }
    if (isset($_POST['submit'])) {
        $scrname = $_FILES['screens']['name'];
        $scrsize = $_FILES['screens']['size'];
        $scsize = GetImageSize($_FILES['screens']['tmp_name']);
        $scwidth = $scsize[0];
        $scheight = $scsize[1];
        $ffot = strtolower($scrname);
        $dopras = array(
            "gif",
            "jpg",
            "png"
        );
        if ($scrname != "") {
            $formfot = Functions::format($ffot);
            if (!in_array($formfot, $dopras)) {
                echo lng('screenshot_upload_error') . '<br/><a href="' . Vars::$URI . '?act=screen&amp;file=' . $file . '">' . lng('repeat') . '</a><br/>';
                exit;
            }
            if ($scwidth > 320 || $scheight > 320) {
                echo lng('screenshot_size_error') . '<br/><a href="' . Vars::$URI . '?act=screen&amp;file=' . $file . '">' . lng('repeat') . '</a><br/>';
                exit;
            }
            if (preg_match("/[^\da-z_\-.]+/", $scrname)) {
                echo lng('screenshot_name_error') . "<br/><a href='?act=screen&amp;file=" . $file . "'>" . lng('repeat') . "</a><br/>";
                exit;
            }
            $filnam = "$adrfile[name]";
            unlink("$screenroot/$adrfile[screen]");
            if ((move_uploaded_file($_FILES["screens"]["tmp_name"], "$screenroot/$filnam.$formfot")) == true) {
                $ch1 = "$filnam.$formfot";
                @chmod("$ch1", 0777);
                @chmod("$screenroot/$ch1", 0777);
                echo lng('screenshot_uploaded') . '<br/>';
                mysql_query("update `download` set screen='" . $ch1 . "' where id='" . $file . "';");
            }
        }
    } else {
        echo lng('upload_screenshot') . '<br/>';
        echo '<form action="' . Vars::$URI . '?act=screen&amp;file=' . $file . '" method="post" enctype="multipart/form-data"><p>' . lng('select') . ' (max. 320*320):<br/>' .
             '<input type="file" name="screens"/>' .
             '</p><p><input type="submit" name="submit" value="' . lng('upload') . '"/></p>' .
             '</form>';
    }
}
echo '<p><a href="' . Vars::$URI . '?act=view&amp;file=' . $file . '">' . lng('back') . '</a></p>';