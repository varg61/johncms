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

if (empty($_GET['id'])) {
    echo Functions::displayError(lng('error_wrong_data'));
    exit;
}
$s = isset($_GET['s']) ? intval($_GET['s']) : false;
// Запрос сообщения
$req = mysql_query("SELECT `forum`.*, `users`.`sex`, `users`.`rights`, `users`.`last_visit`, `users`.`status`, `users`.`datereg`
FROM `forum` LEFT JOIN `users` ON `forum`.`user_id` = `users`.`id`
WHERE `forum`.`type` = 'm' AND `forum`.`id` = " . Vars::$ID . (Vars::$USER_RIGHTS >= 7 ? "" : " AND `forum`.`close` != '1'") . " LIMIT 1");
$res = mysql_fetch_array($req);

// Запрос темы
$them = mysql_fetch_array(mysql_query("SELECT * FROM `forum` WHERE `type` = 't' AND `id` = '" . $res['refid'] . "'"));
echo '<div class="phdr"><b>' . lng('topic') . ':</b> ' . $them['text'] . '</div><div class="menu">';
// Значок пола
if ($res['sex'])
    echo Functions::getImage('usr_' . ($res['sex'] == 'm' ? 'm' : 'w') . ($res['datereg'] > time() - 86400 ? '_new' : '') . '.png', '', 'align="middle"') . '&#160;';
else
    echo Functions::getImage('delete.png', '', 'align="middle"') . '&#160;';
// Ник юзера и ссылка на его анкету
if (Vars::$USER_ID && Vars::$USER_ID != $res['user_id']) {
    echo '<a href="../users/profile.php?user=' . $res['user_id'] . '&amp;fid=' . $res['id'] . '"><b>' . $res['from'] . '</b></a> ';
    echo '<a href="index.php?act=say&amp;id=' . $res['id'] . '&amp;start=' . Vars::$START . '"> [о]</a> <a href="index.php?act=say&amp;id=' . $res['id'] . '&amp;start=' . Vars::$START . '&amp;cyt"> [ц]</a>';
} else {
    echo '<b>' . $res['from'] . '</b>';
}
// Метка должности
switch ($res['rights']) {
    case 7:
        echo " Adm ";
        break;

    case 6:
        echo " Smd ";
        break;

    case 3:
        echo " Mod ";
        break;

    case 1:
        echo " Kil ";
        break;
}
// Метка Онлайн / Офлайн
echo (time() > $res['last_visit'] + 300 ? '<span class="red"> [Off]</span>' : '<span class="green"> [ON]</span>');
// Время поста
echo ' <span class="gray">(' . Functions::displayDate($res['time']) . ')</span><br/>';
// Статус юзера
if (!empty($res['status']))
    echo '<div class="status">' . Functions::getImage('label.png') . '&#160;' . $res['status'] . '</div>';
$text = htmlentities($res['text'], ENT_QUOTES, 'UTF-8');
$text = nl2br($text);
$text = TextParser::tags($text);
if (Vars::$USER_SET['smileys'])
    $text = Functions::smileys($text, ($res['rights'] >= 1) ? 1 : 0);
echo $text . '</div>';
// Вычисляем, на какой странице сообщение?
$page = ceil(mysql_result(mysql_query("SELECT COUNT(*) FROM `forum` WHERE `refid` = '" . $res['refid'] . "' AND `id` " . ($set_forum['upfp'] ? ">= " : "<= ") . Vars::$ID), 0) / Vars::$USER_SET['page_size']);
echo '<div class="phdr"><a href="index.php?id=' . $res['refid'] . '&amp;page=' . $page . '">' . lng('back_to_topic') . '</a></div>';
echo '<p><a href="index.php">' . lng('to_forum') . '</a></p>';