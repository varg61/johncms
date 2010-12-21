<?php

/*
////////////////////////////////////////////////////////////////////////////////
// JohnCMS                Mobile Content Management System                    //
// Project site:          http://johncms.com                                  //
// Support site:          http://gazenwagen.com                               //
////////////////////////////////////////////////////////////////////////////////
// Lead Developer:        Oleg Kasyanov   (AlkatraZ)  alkatraz@gazenwagen.com //
// Development Team:      Eugene Ryabinin (john77)    john77@gazenwagen.com   //
//                        Dmitry Liseenko (FlySelf)   flyself@johncms.com     //
////////////////////////////////////////////////////////////////////////////////
*/

define('_IN_JOHNCMS', 1);
require('../incfiles/core.php');
$lng_forum = $core->load_lng('forum');
if (isset($_SESSION['ref']))
    unset($_SESSION['ref']);

/*
-----------------------------------------------------------------
Настройки форума
-----------------------------------------------------------------
*/
if ($user_id)
    $set_forum = unserialize($datauser['set_forum']);
// Настроки по-умолчанию
if (!isset($set_forum) || empty($set_forum))
    $set_forum = array (
        'farea' => 0,
        'upfp' => 0,
        'postclip' => 1,
        'postcut' => 2
    );

/*
-----------------------------------------------------------------
Список расширений файлов, разрешенных к выгрузке
-----------------------------------------------------------------
*/
// Файлы архивов
$ext_arch = array (
    'zip',
    'rar',
    '7z',
    'tar',
    'gz'
);
// Звуковые файлы
$ext_audio = array (
    'mp3',
    'amr'
);
// Файлы документов и тексты
$ext_doc = array (
    'txt',
    'pdf',
    'doc',
    'rtf',
    'djvu',
    'xls'
);
// Файлы Java
$ext_java = array (
    'jar',
    'jad'
);
// Файлы картинок
$ext_pic = array (
    'jpg',
    'jpeg',
    'gif',
    'png',
    'bmp'
);
// Файлы SIS
$ext_sis = array (
    'sis',
    'sisx'
);
// Файлы видео
$ext_video = array (
    '3gp',
    'avi',
    'flv',
    'mpeg',
    'mp4'
);
// Файлы Windows
$ext_win = array (
    'exe',
    'msi'
);
// Другие типы файлов (что не перечислены выше)
$ext_other = array ('wmf');

/*
-----------------------------------------------------------------
Ограничиваем доступ к Форуму
-----------------------------------------------------------------
*/
$error = '';
if (!$set['mod_forum'] && $rights < 7)
    $error = $lng_forum['forum_closed'];
elseif ($set['mod_forum'] == 1 && !$user_id)
    $error = $lng['access_guest_forbidden'];
if ($error) {
    require('../incfiles/head.php');
    echo '<div class="rmenu"><p>' . $error . '</p></div>';
    require('../incfiles/end.php');
    exit;
}

$headmod = $id ? 'forum,' . $id : 'forum';

/*
-----------------------------------------------------------------
Заголовки страниц форума
-----------------------------------------------------------------
*/
if (empty($id)) {
    $textl = '' . $lng['forum'] . '';
} else {
    $req = mysql_query("SELECT `text` FROM `forum` WHERE `id`= '" . $id . "'");
    $res = mysql_fetch_assoc($req);
    $hdr = strtr($res['text'], array (
        '&quot;' => '',
        '&amp;' => '',
        '&lt;' => '',
        '&gt;' => '',
        '&#039;' => ''
    ));
    $hdr = mb_substr($hdr, 0, 30);
    $hdr = functions::checkout($hdr);
    $textl = mb_strlen($res['text']) > 30 ? $hdr . '...' : $hdr;
}

/*
-----------------------------------------------------------------
Переключаем режимы работы
-----------------------------------------------------------------
*/
$array = array (
    'addfile' => 'includes',
    'addvote' => 'includes',
    'close' => 'includes',
    'deltema' => 'includes',
    'delvote' => 'includes',
    'editpost' => 'includes',
    'editvote' => 'includes',
    'file' => 'includes',
    'files' => 'includes',
    'filter' => 'includes',
    'loadtem' => 'includes',
    'massdel' => 'includes',
    'moders' => 'includes',
    'new' => 'includes',
    'nt' => 'includes',
    'per' => 'includes',
    'post' => 'includes',
    'ren' => 'includes',
    'restore' => 'includes',
    'say' => 'includes',
    'tema' => 'includes',
    'users' => 'includes',
    'vip' => 'includes',
    'vote' => 'includes',
    'who' => 'includes'
);
$path = !empty($array[$act]) ? $array[$act] . '/' : '';
if (array_key_exists($act, $array) && file_exists($path . $act . '.php')) {
    require_once($path . $act . '.php');
} else {
    require('../incfiles/head.php');

    /*
    -----------------------------------------------------------------
    Если форум закрыт, то для Админов выводим напоминание
    -----------------------------------------------------------------
    */
    if (!$set['mod_forum'])
        echo '<div class="alarm">' . $lng_forum['forum_closed'] . '</div>';
    if (!$user_id) {
        if (isset($_GET['newup']))
            $_SESSION['uppost'] = 1;
        if (isset($_GET['newdown']))
            $_SESSION['uppost'] = 0;
    }
    if ($id) {
        /*
        -----------------------------------------------------------------
        Определяем тип запроса (каталог, или тема)
        -----------------------------------------------------------------
        */
        $type = mysql_query("SELECT * FROM `forum` WHERE `id`= '$id'");
        if (!mysql_num_rows($type)) {
            // Если темы не существует, показываем ошибку
            echo functions::display_error($lng_forum['error_topic_deleted'], '<a href="index.php">' . $lng['to_forum'] . '</a>');
            require('../incfiles/end.php');
            exit;
        }
        $type1 = mysql_fetch_assoc($type);

        /*
        -----------------------------------------------------------------
        Фиксация факта прочтения Топика
        -----------------------------------------------------------------
        */
        if ($user_id && $type1['type'] == 't') {
            $req_r = mysql_query("SELECT * FROM `cms_forum_rdm` WHERE `topic_id` = '$id' AND `user_id` = '$user_id' LIMIT 1");
            if (mysql_num_rows($req_r)) {
                $res_r = mysql_fetch_assoc($req_r);
                if ($type1['time'] > $res_r['time'])
                    mysql_query("UPDATE `cms_forum_rdm` SET `time` = '$realtime' WHERE `topic_id` = '$id' AND `user_id` = '$user_id' LIMIT 1");
            } else {
                mysql_query("INSERT INTO `cms_forum_rdm` SET `topic_id` = '$id', `user_id` = '$user_id', `time` = '$realtime'");
            }
        }

        /*
        -----------------------------------------------------------------
        Получаем структуру форума
        -----------------------------------------------------------------
        */
        $res = true;
        $parent = $type1['refid'];
        while ($parent != '0' && $res != false) {
            $req = mysql_query("SELECT * FROM `forum` WHERE `id` = '$parent' LIMIT 1");
            $res = mysql_fetch_assoc($req);
            if ($res['type'] == 'f' || $res['type'] == 'r')
                $tree[] = '<a href="index.php?id=' . $parent . '">' . $res['text'] . '</a>';
            $parent = $res['refid'];
        }
        $tree[] = '<a href="index.php">' . $lng['forum'] . '</a>';
        krsort($tree);
        if ($type1['type'] != 't')
            $tree[] = '<b>' . $type1['text'] . '</b>';

        /*
        -----------------------------------------------------------------
        Счетчик файлов и ссылка на них
        -----------------------------------------------------------------
        */
        $sql = ($rights == 9) ? "" : " AND `del` != '1'";
        if ($type1['type'] == 'f') {
            $count = mysql_result(mysql_query("SELECT COUNT(*) FROM `cms_forum_files` WHERE `cat` = '$id'" . $sql), 0);
            if ($count > 0)
                $filelink = '<a href="index.php?act=files&amp;c=' . $id . '">' . $lng_forum['files_category'] . '</a>';
        } elseif ($type1['type'] == 'r') {
            $count = mysql_result(mysql_query("SELECT COUNT(*) FROM `cms_forum_files` WHERE `subcat` = '$id'" . $sql), 0);
            if ($count > 0)
                $filelink = '<a href="index.php?act=files&amp;s=' . $id . '">' . $lng_forum['files_section'] . '</a>';
        } elseif ($type1['type'] == 't') {
            $count = mysql_result(mysql_query("SELECT COUNT(*) FROM `cms_forum_files` WHERE `topic` = '$id'" . $sql), 0);
            if ($count > 0)
                $filelink = '<a href="index.php?act=files&amp;t=' . $id . '">' . $lng_forum['files_topic'] . '</a>';
        }
        $filelink = isset($filelink) ? $filelink . '&#160;<span class="red">(' . $count . ')</span>' : false;

        /*
        -----------------------------------------------------------------
        Счетчик "Кто в теме?"
        -----------------------------------------------------------------
        */
        $wholink = false;
        if ($user_id && $type1['type'] == 't') {
            $onltime = $realtime - 300;
            $online_u = mysql_result(mysql_query("SELECT COUNT(*) FROM `users` WHERE `lastdate` > $onltime AND `place` = 'forum,$id'"), 0);
            $online_g = mysql_result(mysql_query("SELECT COUNT(*) FROM `cms_guests` WHERE `lastdate` > $onltime AND `place` = 'forum,$id'"), 0);
            $wholink = '<a href="index.php?act=who&amp;id=' . $id . '">' . $lng_forum['who_here'] . '?</a>&#160;<span class="red">(' . $online_u . '&#160;/&#160;' . $online_g . ')</span><br/>';
        }

        /*
        -----------------------------------------------------------------
        Выводим верхнюю панель навигации
        -----------------------------------------------------------------
        */
        echo '<p>' . functions::forum_new(1) . '</p>' .
            '<div class="phdr">' . functions::display_menu($tree) . '</div>' .
            '<div class="topmenu"><a href="search.php?id=' . $id . '">' . $lng['search'] . '</a>' . ($filelink ? ' | ' . $filelink : '') . ($wholink ? ' | ' . $wholink : '') . '</div>';

        /*
        -----------------------------------------------------------------
        Отрбражаем содержимое форума
        -----------------------------------------------------------------
        */
        switch ($type1['type']) {
            case 'f':
                /*
                -----------------------------------------------------------------
                Список разделов форума
                -----------------------------------------------------------------
                */
                $req = mysql_query("SELECT `id`, `text`, `soft` FROM `forum` WHERE `type`='r' AND `refid`='$id' ORDER BY `realid`");
                $total = mysql_num_rows($req);
                if ($total) {
                    while ($res = mysql_fetch_assoc($req)) {
                        echo $i % 2 ? '<div class="list2">' : '<div class="list1">';
                        $coltem = mysql_result(mysql_query("SELECT COUNT(*) FROM `forum` WHERE `type` = 't' AND `refid` = '" . $res['id'] . "'"), 0);
                        echo '<a href="?id=' . $res['id'] . '">' . $res['text'] . '</a>';
                        if ($coltem)
                            echo " [$coltem]";
                        if (!empty($res['soft']))
                            echo '<div class="sub"><span class="gray">' . $res['soft'] . '</span></div>';
                        echo '</div>';
                        ++$i;
                    }
                    unset($_SESSION['fsort_id']);
                    unset($_SESSION['fsort_users']);
                } else {
                    echo '<div class="menu"><p>' . $lng_forum['section_list_empty'] . '</p></div>';
                }
                echo '<div class="phdr">' . $lng['total'] . ': ' . $total . '</div>';
                break;

            case 'r':
                /*
                -----------------------------------------------------------------
                Список топиков
                -----------------------------------------------------------------
                */
                $total = mysql_result(mysql_query("SELECT COUNT(*) FROM `forum` WHERE `type`='t' AND `refid`='$id'" . ($rights >= 7 ? '' : " AND `close`!='1'")), 0);
                if ($user_id && !$ban['1'] && !$ban['11']) {
                    // Кнопка создания новой темы
                    echo '<div class="gmenu"><form action="index.php?act=nt&amp;id=' . $id . '" method="post"><input type="submit" value="' . $lng_forum['new_topic'] . '" /></form></div>';
                }
                if ($total) {
                    $req = mysql_query("SELECT * FROM `forum` WHERE `type`='t'" . ($rights >= 7 ? '' : " AND `close`!='1'") . " AND `refid`='$id' ORDER BY `vip` DESC, `time` DESC LIMIT $start, $kmess");
                    while ($res = mysql_fetch_assoc($req)) {
                        if($res['close'])
                            echo '<div class="rmenu">';
                        else
                            echo $i % 2 ? '<div class="list2">' : '<div class="list1">';
                        $nikuser = mysql_query("SELECT `from` FROM `forum` WHERE `type` = 'm' AND `close` != '1' AND `refid` = '" . $res['id'] . "' ORDER BY `time` DESC LIMIT 1");
                        $nam = mysql_fetch_assoc($nikuser);
                        $colmes = mysql_query("SELECT COUNT(*) FROM `forum` WHERE `type`='m' AND `refid`='" . $res['id'] . "'" . ($rights >= 7 ? '' : " AND `close` != '1'"));
                        $colmes1 = mysql_result($colmes, 0);
                        $cpg = ceil($colmes1 / $kmess);
                        $np = mysql_result(mysql_query("SELECT COUNT(*) FROM `cms_forum_rdm` WHERE `time` >= '" . $res['time'] . "' AND `topic_id` = '" . $res['id'] . "' AND `user_id`='$user_id'"), 0);
                        // Значки
                        $icons = array(
                            ($np ? (!$res['vip'] ? '<img src="../theme/' . $set_user['skin'] . '/images/op.gif" alt=""/>' : '') : '<img src="../theme/' . $set_user['skin'] . '/images/np.gif" alt=""/>'),
                            ($res['vip'] ? '<img src="../theme/' . $set_user['skin'] . '/images/pt.gif" alt=""/>' : ''),
                            ($res['realid'] ? '<img src="../theme/' . $set_user['skin'] . '/images/rate.gif" alt=""/>' : ''),
                            ($res['edit'] ? '<img src="../theme/' . $set_user['skin'] . '/images/tz.gif" alt=""/>' : '')
                        );
                        echo functions::display_menu($icons, '&#160;', '&#160;');
                        echo '<a href="index.php?id=' . $res['id'] . '">' . $res['text'] . '</a> [' . $colmes1 . ']';
                        if ($cpg > 1) {
                            echo '<a href="index.php?id=' . $res['id'] . '&amp;page=' . $cpg . '">&#160;&gt;&gt;</a>';
                        }
                        echo '<div class="sub">';
                        echo $res['from'];
                        if (!empty($nam['from'])) {
                            echo '&#160;/&#160;' . $nam['from'];
                        }
                        $vrp = $res['time'] + $set_user['sdvig'] * 3600;
                        echo ' <span class="gray">(' . date("d.m.y / H:i", $vrp) . ')</span></div></div>';
                        ++$i;
                    }
                    unset($_SESSION['fsort_id']);
                    unset($_SESSION['fsort_users']);
                } else {
                    echo '<div class="menu"><p>' . $lng_forum['topic_list_empty'] . '</p></div>';
                }
                echo '<div class="phdr">' . $lng['total'] . ': ' . $total . '</div>';
                if ($total > $kmess) {
                    echo '<div class="topmenu">' . functions::display_pagination('index.php?id=' . $id . '&amp;', $start, $total, $kmess) . '</div>' .
                        '<p><form action="index.php?id=' . $id . '" method="post">' .
                        '<input type="text" name="page" size="2"/>' .
                        '<input type="submit" value="' . $lng['to_page'] . ' &gt;&gt;"/>' .
                        '</form></p>';
                }
                break;

            case 't':
                /*
                -----------------------------------------------------------------
                Читаем топик
                -----------------------------------------------------------------
                */
                $filter = isset($_SESSION['fsort_id']) && $_SESSION['fsort_id'] == $id ? 1 : 0;
                $sql = '';
                if ($filter && !empty($_SESSION['fsort_users'])) {
                    // Подготавливаем запрос на фильтрацию юзеров
                    $sw = 0;
                    $sql = ' AND (';
                    $fsort_users = unserialize($_SESSION['fsort_users']);
                    foreach ($fsort_users as $val) {
                        if ($sw)
                            $sql .= ' OR ';
                        $sortid = intval($val);
                        $sql .= "`forum`.`user_id` = '$sortid'";
                        $sw = 1;
                    }
                    $sql .= ')';
                }
                if ($user_id && !$filter) {
                // Фиксация факта прочтения топика
                }
                if ($rights < 7 && $type1['close'] == 1) {
                    echo '<div class="rmenu"><p>' . $lng_forum['topic_deleted'] . '<br/><a href="?id=' . $type1['refid'] . '">' . $lng_forum['to_section'] . '</a></p></div>';
                    require('../incfiles/end.php');
                    exit;
                }
                // Счетчик постов темы
                $colmes = mysql_result(mysql_query("SELECT COUNT(*) FROM `forum` WHERE `type`='m'$sql AND `refid`='$id'" . ($rights >= 7 ? '' : " AND `close` != '1'")), 0);
                // Выводим название топика
                echo '<div class="phdr"><a name="up" id="up"></a><a href="#down"><img src="../theme/' . $set_user['skin'] . '/images/down.png" alt="Вниз" width="20" height="10" border="0"/></a>&#160;&#160;<b>' . $type1['text'] . '</b></div>';
                if ($colmes > $kmess)
                    echo '<div class="topmenu">' . functions::display_pagination('index.php?id=' . $id . '&amp;', $start, $colmes, $kmess) . '</div>';
                // Метки удаления темы
                if ($type1['close'])
                    echo '<div class="rmenu">' . $lng_forum['topic_delete_who'] . ': <b>' . $type1['close_who'] . '</b></div>';
                elseif (!empty($type1['close_who']) && $rights >= 7)
                    echo '<div class="gmenu"><small>' . $lng_forum['topic_delete_whocancel'] . ': <b>' . $type1['close_who'] . '</b></small></div>';
                // Метки закрытия темы
                if ($type1['edit'])
                    echo '<div class="rmenu">' . $lng_forum['topic_closed'] . '</div>';
                /*
                -----------------------------------------------------------------
                Блок голосований
                -----------------------------------------------------------------
                */
                if ($type1['realid']) {
                    if (isset($_GET['clip']))
                        $clip_forum = '&amp;clip';
                    $vote_user = mysql_result(mysql_query("SELECT COUNT(*) FROM `cms_forum_vote_users` WHERE `user`='$user_id' AND `topic`='$id'"), 0);
                    $topic_vote = mysql_fetch_assoc(mysql_query("SELECT `name`, `time`, `count` FROM `cms_forum_vote` WHERE `type`='1' AND `topic`='$id' LIMIT 1"));
                    echo '<div  class="gmenu"><b>' . functions::checkout($topic_vote['name']) . '</b><br />';
                    $vote_result = mysql_query("SELECT `id`, `name`, `count` FROM `cms_forum_vote` WHERE `type`='2' AND `topic`='" . $id . "' ORDER BY `id` ASC");
                    if (!$type1['edit'] && !isset($_GET['vote_result']) && $user_id && $vote_user == 0) {
                        // Выводим форму с опросами
                        echo '<form action="index.php?act=vote&amp;id=' . $id . '" method="post">';
                        while ($vote = mysql_fetch_assoc($vote_result)) {
                            echo '<input type="radio" value="' . $vote['id'] . '" name="vote"/> ' . functions::checkout($vote['name']) . '<br />';
                        }
                        echo '<p><input type="submit" name="submit" value="' . $lng['vote'] . '"/><br /><a href="index.php?id=' . $id . '&amp;start=' . $start . '&amp;vote_result' . $clip_forum .
                            '">' . $lng_forum['results'] . '</a></p></form></div>';
                    } else {
                        // Выводим результаты голосования
                        echo '<small>';
                        while ($vote = mysql_fetch_assoc($vote_result)) {
                            $count_vote = $topic_vote['count'] ? round(100 / $topic_vote['count'] * $vote['count']) : 0;
                            echo functions::checkout($vote['name']) . ' [' . $vote['count'] . ']<br />';
                            echo '<img src="vote_img.php?img=' . $count_vote . '" alt="' . $lng_forum['rating'] . ': ' . $count_vote . '%" /><br />';
                        }
                        echo '</small></div><div class="bmenu">' . $lng_forum['total_votes'] . ': ';
                        if ($datauser['rights'] > 6)
                            echo '<a href="index.php?act=users&amp;id=' . $id . '">' . $topic_vote['count'] . '</a>';
                        else
                            echo $topic_vote['count'];
                        echo '</div>';
                        if ($user_id && $vote_user == 0)
                            echo '<div class="bmenu"><a href="index.php?id=' . $id . '&amp;start=' . $start . $clip_forum . '">' . $lng['vote'] . '</a></div>';
                    }
                }
                /*
                -----------------------------------------------------------------
                Фиксация первого поста в теме
                -----------------------------------------------------------------
                */
                if (($set_forum['postclip'] == 2 && ($set_forum['upfp'] ? $start < (ceil($colmes - $kmess)) : $start > 0)) || isset($_GET['clip'])) {
                    $postreq = mysql_query("SELECT `forum`.*, `users`.`sex`, `users`.`rights`, `users`.`lastdate`, `users`.`status`, `users`.`datereg`
                    FROM `forum` LEFT JOIN `users` ON `forum`.`user_id` = `users`.`id`
                    WHERE `forum`.`type` = 'm' AND `forum`.`refid` = '$id'" . ($rights >= 7 ? "" : " AND `forum`.`close` != '1'") . "
                    ORDER BY `forum`.`id` LIMIT 1");
                    $postres = mysql_fetch_assoc($postreq);
                    echo '<div class="clip">';
                    if ($postres['sex'])
                        echo '<img src="../theme/' . $set_user['skin'] . '/images/' . ($postres['sex'] == 'm' ? 'm' : 'f') . ($postres['datereg'] > $realtime - 86400 ? '_new.gif" width="14"' : '.gif" width="10"') . ' height="10"/>&#160;';
                    else
                        echo '<img src="../images/del.png" width="10" height="10" />&#160;';
                    if ($user_id && $user_id != $postres['user_id']) {
                        echo '<a href="../users/profile.php?user=' . $postres['user_id'] . '&amp;fid=' . $postres['id'] . '"><b>' . $postres['from'] . '</b></a> ' .
                            '<a href="index.php?act=say&amp;id=' . $postres['id'] . '&amp;start=' . $start . '"> ' . $lng_forum['reply_btn'] . '</a> ' .
                            '<a href="index.php?act=say&amp;id=' . $postres['id'] . '&amp;start=' . $start . '&amp;cyt"> ' . $lng_forum['cytate_btn'] . '</a> ';
                    } else {
                        echo '<b>' . $postres['from'] . '</b> ';
                    }
                    $user_rights = array (
                        1 => 'Kil',
                        3 => 'Mod',
                        6 => 'Smd',
                        7 => 'Adm',
                        8 => 'SV'
                    );
                    echo $user_rights[$postres['rights']];
                    echo ($realtime > $postres['lastdate'] + 300 ? '<span class="red"> [Off]</span>' : '<span class="green"> [ON]</span>');
                    echo ' <span class="gray">(' . date("d.m.Y / H:i", $postres['time'] + $set_user['sdvig'] * 3600) . ')</span><br/>';
                    if ($postres['close']) {
                        echo '<span class="red">' . $lng_forum['post_deleted'] . '</span><br/>';
                    }
                    echo functions::checkout(mb_substr($postres['text'], 0, 500), 0, 2);
                    if (mb_strlen($postres['text']) > 500)
                        echo '...<a href="index.php?act=post&amp;id=' . $postres['id'] . '">' . $lng_forum['read_all'] . '</a>';
                    echo '</div>';
                }
                if ($filter)
                    echo '<div class="rmenu">' . $lng_forum['filter_on'] . '</div>';
                // Задаем правила сортировки (новые внизу / вверху)
                if ($user_id)
                    $order = $set_forum['upfp'] ? 'DESC' : 'ASC';
                else
                    $order = ((empty($_SESSION['uppost'])) || ($_SESSION['uppost'] == 0)) ? 'ASC' : 'DESC';
                // Запрос в базу
                $req = mysql_query("SELECT `forum`.*, `users`.`sex`, `users`.`rights`, `users`.`lastdate`, `users`.`status`, `users`.`datereg`
                FROM `forum` LEFT JOIN `users` ON `forum`.`user_id` = `users`.`id`
                WHERE `forum`.`type` = 'm' AND `forum`.`refid` = '$id'"
                    . ($rights >= 7 ? "" : " AND `forum`.`close` != '1'") . "$sql ORDER BY `forum`.`id` $order LIMIT $start, $kmess");
                // Верхнее поле "Написать"
                if (($user_id && !$type1['edit'] && $set_forum['upfp']) || ($rights >= 7 && $set_forum['upfp'])) {
                    echo '<div class="gmenu"><form name="form1" action="index.php?act=say&amp;id=' . $id . '" method="post">';
                    if ($set_forum['farea']) {
                        echo '<p>';
                        if(!$is_mobile)
                            echo functions::auto_bb('form1', 'msg');
                        echo '<textarea cols="' . $set_user['field_w'] . '" rows="' . $set_user['field_h'] . '" name="msg"></textarea></p>' .
                            '<p><input type="checkbox" name="addfiles" value="1" /> ' . $lng_forum['add_file'];
                        if ($set_user['translit'])
                            echo '<br /><input type="checkbox" name="msgtrans" value="1" /> ' . $lng['translit'];
                        echo '</p>';
                    }
                    echo '<p><input type="submit" name="submit" value="' . $lng['write'] . '"/></p>' .
                        '</form></div>';
                }
                if ($rights == 3 || $rights >= 6)
                    echo '<form action="index.php?act=massdel" method="post">';
                $i = 1;
                while ($res = mysql_fetch_assoc($req)) {
                    if ($res['close'])
                        echo '<div class="rmenu">';
                    else
                        echo $i % 2 ? '<div class="list2">' : '<div class="list1">';
                    if ($set_user['avatar']) {
                        echo '<table cellpadding="0" cellspacing="0"><tr><td>';
                        if (file_exists(('../files/users/avatar/' . $res['user_id'] . '.png')))
                            echo '<img src="../files/users/avatar/' . $res['user_id'] . '.png" width="32" height="32" alt="' . $res['from'] . '" />&#160;';
                        else
                            echo '<img src="../images/empty.png" width="32" height="32" alt="' . $res['from'] . '" />&#160;';
                        echo '</td><td>';
                    }
                    if ($res['sex'])
                        echo '<img src="../theme/' . $set_user['skin'] . '/images/' . ($res['sex'] == 'm' ? 'm' : 'w') . ($res['datereg'] > $realtime - 86400 ? '_new' : '') . '.png" width="16" height="16" align="middle" />&#160;';
                    else
                        echo '<img src="../images/del.png" width="12" height="12" align="middle" />&#160;';
                    // Ник юзера и ссылка на его анкету
                    if ($user_id && $user_id != $res['user_id']) {
                        echo '<a href="../users/profile.php?user=' . $res['user_id'] . '"><b>' . $res['from'] . '</b></a> ';
                    } else {
                        echo '<b>' . $res['from'] . '</b> ';
                    }
                    // Метка должности
                    $user_rights = array (
                        3 => '(FMod)',
                        6 => '(Smd)',
                        7 => '(Adm)',
                        9 => '(SV!)'
                    );
                    echo $user_rights[$res['rights']];
                    // Метка Онлайн / Офлайн
                    echo ($realtime > $res['lastdate'] + 300 ? '<span class="red"> [Off]</span> ' : '<span class="green"> [ON]</span> ');
                    // Ссылки на ответ и цитирование
                    if ($user_id && $user_id != $res['user_id']) {
                        echo '<a href="index.php?act=say&amp;id=' . $res['id'] . '&amp;start=' . $start . '">' . $lng_forum['reply_btn'] . '</a>&#160;' .
                            '<a href="index.php?act=say&amp;id=' . $res['id'] . '&amp;start=' . $start . '&amp;cyt">' . $lng_forum['cytate_btn'] . '</a> ';
                    }
                    // Время поста
                    echo ' <span class="gray">(' . date("d.m.Y / H:i", $res['time'] + $set_user['sdvig'] * 3600) . ')</span><br />';
                    // Статус юзера
                    if (!empty($res['status']))
                        echo '<div class="status"><img src="../theme/' . $set_user['skin'] . '/images/label.png" alt="" align="middle"/>&#160;' . $res['status'] . '</div>';
                    if ($set_user['avatar'])
                        echo '</td></tr></table>';
                    /*
                    -----------------------------------------------------------------
                    Вывод текста поста
                    -----------------------------------------------------------------
                    */
                    $text = $res['text'];
                    if ($set_forum['postcut']) {
                        // Если текст длинный, обрезаем и даем ссылку на полный вариант
                        switch ($set_forum['postcut']) {
                            case 2:
                                $cut = 1000;
                                break;

                            case 3:
                                $cut = 3000;
                                break;
                                default :
                                $cut = 500;
                        }
                    }
                    if ($set_forum['postcut'] && mb_strlen($text) > $cut) {
                        $text = mb_substr($text, 0, $cut);
                        $text = functions::checkout($text, 1, 0);
                        $text = preg_replace('#\[c\](.*?)\[/c\]#si', '<div class="quote">\1</div>', $text);
                        echo $text . '...<br /><a href="index.php?act=post&amp;id=' . $res['id'] . '">' . $lng_forum['read_all'] . ' &gt;&gt;</a>';
                    } else {
                        // Или, обрабатываем тэги и выводим весь текст
                        $text = functions::checkout($text, 1, 1);
                        if ($set_user['smileys'])
                            $text = functions::smileys($text, $res['rights'] ? 1 : 0);
                        echo $text;
                    }
                    if ($res['kedit']) {
                        // Если пост редактировался, показываем кем и когда
                        $dizm = date("d.m /H:i", $res['tedit'] + $set_user['sdvig'] * 3600);
                        echo '<br /><span class="gray"><small>' . $lng_forum['edited'] . ' <b>' . $res['edit'] . '</b> (' . $dizm . ') <b>[' . $res['kedit'] . ']</b></small></span>';
                    }
                    // Если есть прикрепленный файл, выводим его описание
                    $freq = mysql_query("SELECT * FROM `cms_forum_files` WHERE `post` = '" . $res['id'] . "'");
                    if (mysql_num_rows($freq) > 0) {
                        $fres = mysql_fetch_assoc($freq);
                        $fls = round(@filesize('../files/forum/attach/' . $fres['filename']) / 1024, 2);
                        echo '<br /><span class="gray">' . $lng_forum['attached_file'] . ':';
                        // Предпросмотр изображений
                        $att_ext = strtolower(functions::format('./files/forum/attach/' . $fres['filename']));
                        $pic_ext = array (
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
                        echo $lng_forum['downloads'] . ': ' . $fres['dlcount'] . ' ' . $lng_forum['time'] . '</span>';
                    }
                    if ((($rights == 3 || $rights >= 6) && $rights >= $res['rights']) || ($res['user_id'] == $user_id && !$set_forum['upfp'] && ($start + $i) == $colmes && $res['time'] > $realtime - 300)
                        || ($res['user_id'] == $user_id && $set_forum['upfp'] && $start == 0 && $i == 1 && $res['time'] > $realtime - 300)) {
                        // Ссылки на редактирование / удаление постов
                        echo '<div class="sub">';
                        if ($rights == 3 || $rights >= 6)
                            echo '<input type="checkbox" name="delch[]" value="' . $res['id'] . '"/>&#160;';
                        echo '<a href="index.php?act=editpost&amp;id=' . $res['id'] . '">' . $lng['edit'] . '</a> | ';
                        if ($rights >= 7 && $res['close'] == 1)
                            echo '<a href="index.php?act=editpost&amp;do=restore&amp;id=' . $res['id'] . '">' . $lng_forum['restore'] . '</a> | ';
                        echo '<a href="index.php?act=editpost&amp;do=del&amp;id=' . $res['id'] . '">' . $lng['delete'] . '</a>';
                        if ($res['close']) {
                            echo '<div class="red">' . $lng_forum['who_delete_post'] . ': <b>' . $res['close_who'] . '</b></div>';
                        } elseif (!empty($res['close_who'])) {
                            echo '<div class="green">' . $lng_forum['who_restore_post'] . ': <b>' . $res['close_who'] . '</b></div>';
                        }
                        if ($rights == 3 || $rights >= 6)
                            echo '<div class="gray">' . $res['ip'] . ' - ' . $res['soft'] . '</div>';
                        echo '</div>';
                    }
                    echo '</div>';
                    ++$i;
                }
                if ($rights == 3 || $rights >= 6) {
                    echo '<div class="rmenu"><input type="submit" value=" ' . $lng['delete'] . ' "/></div>';
                    echo '</form>';
                }
                // Нижнее поле "Написать"
                if (($user_id && !$type1['edit'] && !$set_forum['upfp']) || ($rights >= 7 && !$set_forum['upfp'])) {
                    echo '<div class="gmenu"><form name="form2" action="index.php?act=say&amp;id=' . $id . '" method="post">';
                    if ($set_forum['farea']) {
                        echo '<p>';
                        if(!$is_mobile)
                            echo functions::auto_bb('form2', 'msg');
                        echo '<textarea cols="' . $set_user['field_w'] . '" rows="' . $set_user['field_h'] . '" name="msg"></textarea><br/></p>' .
                            '<p><input type="checkbox" name="addfiles" value="1" /> ' . $lng_forum['add_file'];
                        if ($set_user['translit'])
                            echo '<br /><input type="checkbox" name="msgtrans" value="1" /> ' . $lng['translit'];
                        echo '</p>';
                    }
                    echo '<p><input type="submit" name="submit" value="' . $lng['write'] . '"/></p>' .
                        '</form></div>';
                }
                echo '<div class="phdr"><a name="down" id="down"></a><a href="#up">' .
                    '<img src="../theme/' . $set_user['skin'] . '/images/up.png" alt="' . $lng['up'] . '" width="20" height="10" border="0"/></a>' .
                    '&#160;&#160;' . $lng['total'] . ': ' . $colmes . '</div>';
                if ($colmes > $kmess) {
                    echo '<div class="topmenu">' . functions::display_pagination('index.php?id=' . $id . '&amp;', $start, $colmes, $kmess) . '</div>' .
                        '<p><form action="index.php?id=' . $id . '" method="post">' .
                        '<input type="text" name="page" size="2"/>' .
                        '<input type="submit" value="' . $lng['to_page'] . ' &gt;&gt;"/>' .
                        '</form></p>';
                } else {
                    echo '<br />';
                }
                /*
                -----------------------------------------------------------------
                Ссылки на модераторские функции
                -----------------------------------------------------------------
                */
                if ($rights == 3 || $rights >= 6) {
                    echo '<p><div class="func">';
                    echo $topic_vote > 0 ? '<a href="index.php?act=editvote&amp;id=' . $id . '">' . $lng_forum['edit_vote'] . '</a><br/><a href="index.php?act=delvote&amp;id=' . $id . '">' . $lng_forum['delete_vote'] . '</a><br/>'
                        : '<a href="index.php?act=addvote&amp;id=' . $id . '">' . $lng_forum['add_vote'] . '</a><br/>';
                    echo '<a href="index.php?act=ren&amp;id=' . $id . '">' . $lng_forum['topic_rename'] . '</a><br/>';
                    // Закрыть - открыть тему
                    if ($type1['edit'] == 1)
                        echo '<a href="index.php?act=close&amp;id=' . $id . '">' . $lng_forum['topic_open'] . '</a><br/>';
                    else
                        echo '<a href="index.php?act=close&amp;id=' . $id . '&amp;closed">' . $lng_forum['topic_close'] . '</a><br/>';
                    // Удалить - восстановить тему
                    if ($type1['close'] == 1)
                        echo '<a href="index.php?act=restore&amp;id=' . $id . '">' . $lng_forum['topic_restore'] . '</a><br/>';
                    echo '<a href="index.php?act=deltema&amp;id=' . $id . '">' . $lng_forum['topic_delete'] . '</a><br/>';
                    if ($type1['vip'] == 1)
                        echo '<a href="index.php?act=vip&amp;id=' . $id . '">' . $lng_forum['topic_unfix'] . '</a>';
                    else
                        echo '<a href="index.php?act=vip&amp;id=' . $id . '&amp;vip">' . $lng_forum['topic_fix'] . '</a>';
                    echo '<br/><a href="index.php?act=per&amp;id=' . $id . '">' . $lng_forum['topic_move'] . '</a></div></p>';
                }
                if($wholink)
                    echo '<div>' . $wholink . '</div>';
                if ($filter)
                    echo '<div><a href="index.php?act=filter&amp;id=' . $id . '&amp;do=unset">' . $lng_forum['filter_cancel'] . '</a></div>';
                else
                    echo '<div><a href="index.php?act=filter&amp;id=' . $id . '&amp;start=' . $start . '">' . $lng_forum['filter_on_author'] . '</a></div>';
                echo '<a href="index.php?act=tema&amp;id=' . $id . '">' . $lng_forum['download_topic'] . '</a>';
                break;

            default:
                /*
                -----------------------------------------------------------------
                Если неверные данные, показываем ошибку
                -----------------------------------------------------------------
                */
                echo functions::display_error($lng['error_wrong_data']);
                break;
        }
    } else {
        /*
        -----------------------------------------------------------------
        Список Категорий форума
        -----------------------------------------------------------------
        */
        $count = mysql_result(mysql_query("SELECT COUNT(*) FROM `cms_forum_files`" . ($rights >= 7 ? '' : " WHERE `del` != '1'")), 0);
        echo '<p>' . functions::forum_new(1) . '</p>' .
            '<div class="phdr"><b>' . $lng['forum'] . '</b></div>' .
            '<div class="topmenu"><a href="search.php">' . $lng['search'] . '</a> | <a href="index.php?act=files">' . $lng_forum['files_forum'] . '</a> <span class="red">(' . $count . ')</span></div>';
        $req = mysql_query("SELECT `id`, `text`, `soft` FROM `forum` WHERE `type`='f' ORDER BY `realid`");
        while ($res = mysql_fetch_array($req)) {
            echo $i % 2 ? '<div class="list2">' : '<div class="list1">';
            $count = mysql_result(mysql_query("SELECT COUNT(*) FROM `forum` WHERE `type`='r' and `refid`='" . $res['id'] . "'"), 0);
            echo '<a href="index.php?id=' . $res['id'] . '">' . $res['text'] . '</a> [' . $count . ']';
            if (!empty($res['soft']))
                echo '<div class="sub"><span class="gray">' . $res['soft'] . '</span></div>';
            echo '</div>';
            ++$i;
        }
        $onltime = $realtime - 300;
        $online_u = mysql_result(mysql_query("SELECT COUNT(*) FROM `users` WHERE `lastdate` > $onltime AND `place` LIKE 'forum%'"), 0);
        $online_g = mysql_result(mysql_query("SELECT COUNT(*) FROM `cms_guests` WHERE `lastdate` > $onltime AND `place` LIKE 'forum%'"), 0);
        echo '<div class="phdr">' . ($user_id ? '<a href="index.php?act=who">' . $lng_forum['who_in_forum'] . '</a>' : $lng_forum['who_in_forum']) . '&#160;(' . $online_u . '&#160;/&#160;' . $online_g . ')</div>';
        unset($_SESSION['fsort_id']);
        unset($_SESSION['fsort_users']);
    }

    // Навигация внизу страницы
    echo '<p>' . ($id ? '<a href="index.php">' . $lng['to_forum'] . '</a><br />' : '');
    if (!$id) {
        echo '<a href="../pages/faq.php?act=forum">' . $lng_forum['forum_rules'] . '</a><br/>';
        echo '<a href="index.php?act=moders">' . $lng['moders'] . '</a><br />';
        echo '<a href="index.php?act=faq">FAQ</a>';
    }
    echo '</p>';
    if (!$user_id) {
        if ((empty($_SESSION['uppost'])) || ($_SESSION['uppost'] == 0)) {
            echo '<a href="index.php?id=' . $id . '&amp;page=' . $page . '&amp;newup">' . $lng_forum['new_on_top'] . '</a>';
        } else {
            echo '<a href="index.php?id=' . $id . '&amp;page=' . $page . '&amp;newdown">' . $lng_forum['new_on_bottom'] . '</a>';
        }
    }
}

require_once('../incfiles/end.php');
?>
