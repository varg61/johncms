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
        echo lng('file_not_selected') . "<br/><a href='?'>" . lng('back') . "</a><br/>";
        exit;
    }
    $file = intval(trim($_GET['file']));
    $file1 = mysql_query("select * from `download` where type = 'file' and id = '" . $file . "';");
    $file2 = mysql_num_rows($file1);
    $adrfile = mysql_fetch_array($file1);
    if (($file1 == 0) || (!is_file("$adrfile[adres]/$adrfile[name]"))) {
        echo lng('file_not_selected') . "<br/><a href='?'>" . lng('back') . "</a><br/>";
        exit;
    }
    $refd = mysql_query("select * from `download` where type = 'cat' and id = '" . $adrfile[refid] . "'");
    $refd1 = mysql_fetch_array($refd);
    if (isset($_POST['submit'])) {
        unlink("$adrfile[adres]/$adrfile[name]");
        mysql_query("delete from `download` where id='" . $adrfile[id] . "' LIMIT 1;");
        echo '<p>' . lng('file_deleted') . '</p>';
    } else {
        echo '<p>' . lng('delete_confirmation') . '</p>' .
             '<form action="' . Vars::$URI . '?act=dfile&amp;file=' . $file . '" method="post">' .
             '<input type="submit" name="submit" value="' . lng('delete') . '" />' .
             '</form><p><a href="' . Vars::$URI . '?act=view&amp;file=' . $file . '">' . lng('cancel') . '</a></p>';
    }
}
echo "<p><a href='?cat=" . $refd1[id] . "'>" . lng('back') . "</a></p>";