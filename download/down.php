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

$fil = mysql_query("select * from `download` where `id` = " . Vars::$ID);
$mas = mysql_fetch_array($fil);
if (!empty ($mas[name])) {
    if (file_exists("$mas[adres]/$mas[name]")) {
        $sc = $mas[ip] + 1;
        mysql_query("update `download` set `ip` = '" . $sc . "' where `id` = " . Vars::$ID);
        $_SESSION['upl'] = "";
        header("location: $mas[adres]/$mas[name]");
    }
}
else {
    require_once ("../includes/head.php");
    echo "ERROR<br/>&#187;<a href='?'>Back</a><br/>";
}