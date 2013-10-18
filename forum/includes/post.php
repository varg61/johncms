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
echo '<div class="phdr"><b>' . $lng_forum['topic'] . ':</b> ' . $them['text'] . '</div><div class="menu">';

// Данные пользователя
if ($set_user['avatar']) {
    echo '<table cellpadding="0" cellspacing="0"><tr><td>';
    if (file_exists(('../files/users/avatar/' . $res['user_id'] . '.png')))
        echo '<img src="../files/users/avatar/' . $res['user_id'] . '.png" width="32" height="32" alt="' . $res['from'] . '" />&#160;';
    else
        echo '<img src="../images/empty.png" width="32" height="32" alt="' . $res['from'] . '" />&#160;';
    echo '</td><td>';
}
if ($res['sex'])
    echo functions::image(($res['sex'] == 'm' ? 'm' : 'w') . ($res['datereg'] > time() - 86400 ? '_new' : '') . '.png', array('class' => 'icon-inline'));
else
    echo functions::image('del.png');
// Ник юзера и ссылка на его анкету
if ($user_id && $user_id != $res['user_id']) {
    echo '<a href="../users/profile.php?user=' . $res['user_id'] . '"><b>' . $res['from'] . '</b></a> ';
} else {
    echo '<b>' . $res['from'] . '</b> ';
}
// Метка должности
$user_rights = array(
    3 => '(FMod)',
    6 => '(Smd)',
    7 => '(Adm)',
    9 => '(SV!)'
);
echo @$user_rights[$res['rights']];
// Метка Онлайн / Офлайн
echo(time() > $res['lastdate'] + 300 ? '<span class="red"> [Off]</span> ' : '<span class="green"> [ON]</span> ');
echo '<a href="index.php?act=post&amp;id=' . $res['id'] . '" title="Link to post">[#]</a>';
// Ссылки на ответ и цитирование
if ($user_id && $user_id != $res['user_id']) {
    echo '&#160;<a href="index.php?act=say&amp;id=' . $res['id'] . '&amp;start=' . $start . '">' . $lng_forum['reply_btn'] . '</a>&#160;' .
        '<a href="index.php?act=say&amp;id=' . $res['id'] . '&amp;start=' . $start . '&amp;cyt">' . $lng_forum['cytate_btn'] . '</a> ';
}
// Время поста
echo ' <span class="gray">(' . functions::display_date($res['time']) . ')</span><br />';
// Статус юзера
if (!empty($res['status']))
    echo '<div class="status">' . functions::image('label.png', array('class' => 'icon-inline')) . $res['status'] . '</div>';
if ($set_user['avatar'])
    echo '</td></tr></table>';

// Вывод текста поста
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

if (
    (($rights == 3 || $rights >= 6 || $curator) && $rights >= $res['rights'])
    || ($res['user_id'] == $user_id && !$set_forum['upfp'] && ($start + $i) == $colmes && $res['time'] > time() - 300)
    || ($res['user_id'] == $user_id && $set_forum['upfp'] && $start == 0 && $i == 1 && $res['time'] > time() - 300)
    || ($i == 1 && $allow == 2 && $res['user_id'] == $user_id)
) {
    // Ссылки на редактирование / удаление постов
    $menu = array(
        '<a href="index.php?act=editpost&amp;id=' . $res['id'] . '">' . $lng['edit'] . '</a>',
        ($rights >= 7 && $res['close'] == 1 ? '<a href="index.php?act=editpost&amp;do=restore&amp;id=' . $res['id'] . '">' . $lng_forum['restore'] . '</a>' : ''),
        ($res['close'] == 1 ? '' : '<a href="index.php?act=editpost&amp;do=del&amp;id=' . $res['id'] . '">' . $lng['delete'] . '</a>')
    );
    echo '<div class="sub">';
    echo functions::display_menu($menu);
    if ($res['close']) {
        echo '<div class="red">' . $lng_forum['who_delete_post'] . ': <b>' . $res['close_who'] . '</b></div>';
    } elseif (!empty($res['close_who'])) {
        echo '<div class="green">' . $lng_forum['who_restore_post'] . ': <b>' . $res['close_who'] . '</b></div>';
    }
    if ($rights == 3 || $rights >= 6) {
        if ($res['ip_via_proxy']) {
            echo '<div class="gray"><b class="red"><a href="' . $set['homeurl'] . '/' . $set['admp'] . '/index.php?act=search_ip&amp;ip=' . long2ip($res['ip']) . '">' . long2ip($res['ip']) . '</a></b> - ' .
                '<a href="' . $set['homeurl'] . '/' . $set['admp'] . '/index.php?act=search_ip&amp;ip=' . long2ip($res['ip_via_proxy']) . '">' . long2ip($res['ip_via_proxy']) . '</a>' .
                ' - ' . $res['soft'] . '</div>';
        } else {
            echo '<div class="gray"><a href="' . $set['homeurl'] . '/' . $set['admp'] . '/index.php?act=search_ip&amp;ip=' . long2ip($res['ip']) . '">' . long2ip($res['ip']) . '</a> - ' . $res['soft'] . '</div>';
        }
    }
    echo '</div>';
}

echo '</div>';

// Вычисляем, на какой странице сообщение?
$page = ceil(mysql_result(mysql_query("SELECT COUNT(*) FROM `forum` WHERE `refid` = '" . $res['refid'] . "' AND `id` " . ($set_forum['upfp'] ? ">=" : "<=") . " '$id'"), 0) / $kmess);
echo '<div class="phdr"><a href="index.php?id=' . $res['refid'] . '&amp;page=' . $page . '">' . $lng_forum['back_to_topic'] . '</a></div>';
echo '<p><a href="index.php">' . $lng['to_forum'] . '</a></p>';