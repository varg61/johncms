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

require_once('../includes/head.php');
echo '<div class="phdr"><a href="index.php"><b>' . Vars::$LNG['forum'] . '</b></a> | ' . Vars::$LNG['moders'] . '</div>';
$req = mysql_query("SELECT * FROM `forum` WHERE `type` = 'f' ORDER BY `realid`");
while ($f1 = mysql_fetch_array($req)) {
    $mod = mysql_query("select * from `forum` where type='a' and refid='" . $f1['id'] . "'");
    $mod2 = mysql_num_rows($mod);
    if ($mod2 != 0) {
        echo $i % 2 ? '<div class="list2">' : '<div class="list1">';
        echo '<b>' . $f1['text'] . '</b><br />';
        while ($mod1 = mysql_fetch_array($mod)) {
            $uz = mysql_query("select * from `users` where name='" . $mod1['from'] . "';");
            $uz1 = mysql_fetch_array($uz);
            if ($uz1['rights'] == 3) {
                if ((!empty($_SESSION['uid'])) && (Vars::$USER_NICKNAME != $mod1['from'])) {
                    echo '<a href="../users/profile.php?user=' . $uz1['id'] . '">' . $mod1['from'] . '</a>';
                } else {
                    echo $mod1['from'];
                }
            }
        }
        echo '</div>';
        ++$i;
    }
}
echo '<div class="phdr"><a href="index.php?id=' . Vars::$ID . '">' . Vars::$LNG['back'] . '</a></div>';