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

if (!empty($_GET['cat'])) {
    $cat = $_GET['cat'];
    provcat($cat);
    if (Vars::$USER_RIGHTS == 4 || Vars::$USER_RIGHTS >= 6) {
        echo "<form action='?act=upl' method='post' enctype='multipart/form-data'>
         <p>" . lng('select') . " (max " . Vars::$SYSTEM_SET['flsz'] . " кб.):<br/>
         <input type='file' name='fail'/></p>
         <p>" . lng('screenshot') . ":<br/>
         <input type='file' name='screens'/></p>
         <p>" . lng('description') . ":<br/>
         <textarea name='opis'></textarea></p>
         <p>" . lng('save_as') . ":<br/>
         <input type='text' name='newname'/></p>
         <input type='hidden' name='cat' value='" . $cat . "'/>
         <p><input type='submit' value='" . lng('upload') . "'/></p>
         </form>";
    } else {
        echo "Нет доступа!<br/>";
    }
    echo "<a href='?cat=" . $cat . "'>" . lng('back') . "</a><br/>";
} else {
    echo 'ERROR';
}