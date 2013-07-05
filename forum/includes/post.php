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

require('../incfiles/head.php');
if (empty($_GET['id'])) {
    echo functions::display_error($lng['error_wrong_data']);
    require('../incfiles/end.php');
    exit;
}



// Запрос сообщения
$req = mysql_query("SELECT `forum`.*, `users`.`sex`, `users`.`rights`, `users`.`lastdate`, `users`.`status`, `users`.`datereg`
FROM `forum` LEFT JOIN `users` ON `forum`.`user_id` = `users`.`id`
WHERE `forum`.`type` = 'm' AND `forum`.`id` = '$id'" . ($rights >= 7 ? "" : " AND `forum`.`close` != '1'") . " LIMIT 1");
$res = mysql_fetch_array($req);

// Запрос темы
$them = mysql_fetch_assoc(mysql_query("SELECT * FROM `forum` WHERE `type` = 't' AND `id` = '" . $res['refid'] . "'"));

// Запрос раздела
$section = mysql_fetch_assoc(mysql_query("SELECT * FROM `forum` WHERE `type` = 'r' AND `id` = '" . $them['refid'] . "'"));
if($section['edit'] == 3 && !$rights){
    echo functions::display_error($lng['access_forbidden'], '<a href="index.php">' . $lng['to_forum'] . '</a>');
    require('../incfiles/end.php');
    exit;
}

echo '<div class="phdr"><b>' . $lng_forum['topic'] . ':</b> ' . $them['text'] . '</div><div class="menu">';
// Значок пола
if ($res['sex'])
    echo '<img src="../theme/' . $set_user['skin'] . '/images/' . ($res['sex'] == 'm' ? 'm' : 'w') . '.png" alt=""  width="16" height="16"/>&#160;';
else
    echo '<img src="../images/del.png" width="12" height="12" />&#160;';
// Ник юзера и ссылка на его анкету
if ($user_id && $user_id != $res['user_id']) {
    echo '<a href="../users/profile.php?user=' . $res['user_id'] . '&amp;fid=' . $res['id'] . '"><b>' . $res['from'] . '</b></a> ';
    echo '<a href="index.php?act=say&amp;id=' . $res['id'] . '&amp;start=' . $start . '"> [о]</a> <a href="index.php?act=say&amp;id=' . $res['id'] . '&amp;start=' . $start . '&amp;cyt"> [ц]</a>';
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
echo (time() > $res['lastdate'] + 300 ? '<span class="red"> [Off]</span>' : '<span class="green"> [ON]</span>');
// Время поста
echo ' <span class="gray">(' . functions::display_date($res['time']) . ')</span><br/>';
// Статус юзера
if (!empty($res['status']))
    echo '<div class="status"><img src="../images/star.gif" alt=""/>&#160;' . $res['status'] . '</div>';
$text = htmlentities($res['text'], ENT_QUOTES, 'UTF-8');
$text = nl2br($text);
$text = bbcode::tags($text);
if ($set_user['smileys'])
    $text = functions::smileys($text, ($res['rights'] >= 1) ? 1 : 0);
echo $text . '';

// Если есть прикрепленный файл, выводим его описание
$freq = mysql_query("SELECT * FROM `cms_forum_files` WHERE `post` = '" . $res['id'] . "'");
if (mysql_num_rows($freq) > 0) {
    $fres = mysql_fetch_assoc($freq);
    $fls = round(@filesize('../files/forum/attach/' . $fres['filename']) / 1024, 2);
    echo '<div class="gray" style="font-size: x-small; background-color: rgba(128, 128, 128, 0.1); padding: 2px 4px; margin-top: 4px">' . $lng_forum['attached_file'] . ':';
    // Предпросмотр изображений
    $att_ext = strtolower(functions::format('./files/forum/attach/' . $fres['filename']));
    $pic_ext = array(
        'gif',
        'jpg',
        'jpeg',
        'png'
    );
    if (in_array($att_ext, $pic_ext)) {
        echo '<div><a href="index.php?act=file&amp;id=' . $fres['id'] . '">';
        echo '<img src="thumbinal.php?file=' . (urlencode($fres['filename'])) . '" alt="' . $lng_forum['click_to_view'] . '" /></a></div>';
    } else {
        echo '<br /><a href="index.php?act=file&amp;id=' . $fres['id'] . '">' . $fres['filename'] . '</a>';
    }
    echo ' (' . $fls . ' кб.)<br/>';
    echo $lng_forum['downloads'] . ': ' . $fres['dlcount'] . ' ' . $lng_forum['time'] . '</div>';
    $file_id = $fres['id'];
}
echo '</div>';

// Вычисляем, на какой странице сообщение?
$page = ceil(mysql_result(mysql_query("SELECT COUNT(*) FROM `forum` WHERE `refid` = '" . $res['refid'] . "' AND `id` " . ($set_forum['upfp'] ? ">=" : "<=") . " '$id'"), 0) / $kmess);
echo '<div class="phdr"><a href="index.php?id=' . $res['refid'] . '&amp;page=' . $page . '">' . $lng_forum['back_to_topic'] . '</a></div>';
echo '<p><a href="index.php">' . $lng['to_forum'] . '</a></p>';