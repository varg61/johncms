<?php

/**
 * @package     JohnCMS
 * @link        http://johncms.com
 * @copyright   Copyright (C) 2008-2012 JohnCMS Community
 * @license     LICENSE.txt (see attached file)
 * @version     VERSION.txt (see attached file)
 * @author      http://johncms.com/about
 */

define('_IN_JOHNCMS', 1);

require_once('../includes/core.php');

// Проверяем права доступа
if (Vars::$USER_RIGHTS < 1) {
    header('Location: http://mobicms.org/404.php');
    exit;
}

echo '<div class="phdr"><a href="index.php"><b>' . Vars::$LNG['admin_panel'] . '</b></a> | ' . Vars::$LNG['administration'] . '</div>';
$req = mysql_query("SELECT * FROM `users` WHERE `rights` > '0' ORDER BY `rights` DESC, `nickname` ASC");
$total = mysql_num_rows($req);
for ($i = 0; ($res = mysql_fetch_assoc($req)) !== false; ++$i) {
    echo $i % 2 ? '<div class="list2">' : '<div class="list1">';
    echo Functions::displayUser($res, array('header' => ('<b>ID:' . $res['id'] . '</b>')));
    echo '</div>';
}

echo '<div class="phdr">' . Vars::$LNG['total'] . ': ' . $total . '</div>' .
    '<p><a href="index.php?act=usr">' . Vars::$LNG['users_list'] . '</a><br />' .
    '<a href="index.php">' . Vars::$LNG['admin_panel'] . '</a></p>';