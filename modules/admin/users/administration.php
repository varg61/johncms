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

// Проверяем права доступа
if (Vars::$USER_RIGHTS < 1) {
    header('Location: http://johncms.com/404.php');
    exit;
}

echo '<div class="phdr"><a href="' . Vars::$MODULE_URI . '"><b>' . lng('admin_panel') . '</b></a> | ' . lng('administration') . '</div>';
$req = mysql_query("SELECT * FROM `users` WHERE `rights` > 0 ORDER BY `rights` DESC, `nickname` ASC");
$total = mysql_num_rows($req);
for ($i = 0; ($res = mysql_fetch_assoc($req)) !== false; ++$i) {
    echo ($i % 2 ? '<div class="list2">' : '<div class="list1">') .
        Functions::displayUser($res, array('header' => ('<b>ID:' . $res['id'] . '</b>'))) .
        '</div>';
}

echo'<div class="phdr">' . lng('total') . ': ' . $total . '</div>' .
    '<p><a href="' . Vars::$MODULE_URI . '/users">' . lng('users_list') . '</a><br />' .
    '<a href="' . Vars::$MODULE_URI . '">' . lng('admin_panel') . '</a></p>';