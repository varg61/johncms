<?php

/**
 * @package     JohnCMS
 * @link        http://johncms.com
 * @copyright   Copyright (C) 2008-2012 JohnCMS Community
 * @license     LICENSE.txt (see attached file)
 * @version     VERSION.txt (see attached file)
 * @author      http://johncms.com/about
 */

if (isset($_SESSION['ref']))
    unset($_SESSION['ref']);

/*
-----------------------------------------------------------------
Настройки форума
-----------------------------------------------------------------
*/
if (!Vars::$USER_ID || ($set_forum = Vars::getUserData('set_forum')) === FALSE) {
    $set_forum = array(
        'farea'    => 0,
        'upfp'     => 0,
        'preview'  => 1,
        'postclip' => 1,
        'postcut'  => 2
    );
}

/*
-----------------------------------------------------------------
Список расширений файлов, разрешенных к выгрузке
-----------------------------------------------------------------
*/
// Файлы архивов
$ext_arch = array(
    'zip',
    'rar',
    '7z',
    'tar',
    'gz'
);
// Звуковые файлы
$ext_audio = array(
    'mp3',
    'amr'
);
// Файлы документов и тексты
$ext_doc = array(
    'txt',
    'pdf',
    'doc',
    'rtf',
    'djvu',
    'xls'
);
// Файлы Java
$ext_java = array(
    'jar',
    'jad'
);
// Файлы картинок
$ext_pic = array(
    'jpg',
    'jpeg',
    'gif',
    'png',
    'bmp'
);
// Файлы SIS
$ext_sis = array(
    'sis',
    'sisx'
);
// Файлы видео
$ext_video = array(
    '3gp',
    'avi',
    'flv',
    'mpeg',
    'mp4'
);
// Файлы Windows
$ext_win = array(
    'exe',
    'msi'
);
// Другие типы файлов (что не перечислены выше)
$ext_other = array('wmf');

/*
-----------------------------------------------------------------
Ограничиваем доступ к Форуму
-----------------------------------------------------------------
*/
$error = '';
if ((!isset(Vars::$ACL['forum']) || !Vars::$ACL['forum']) && Vars::$USER_RIGHTS < 7) {
    $error = lng('forum_closed');
} elseif (isset(Vars::$ACL['forum']) && Vars::$ACL['forum'] == 1 && !Vars::$USER_ID) {
    $error = lng('access_guest_forbidden');
}
if ($error) {
    echo '<div class="rmenu"><p>' . $error . '</p></div>';
    exit;
}

/*
-----------------------------------------------------------------
Заголовки страниц форума
-----------------------------------------------------------------
*/
if (!Vars::$ID) {
    $textl = '' . lng('forum') . '';
} else {
    $req = mysql_query("SELECT `text` FROM `forum` WHERE `id`= " . Vars::$ID);
    $res = mysql_fetch_assoc($req);
    $hdr = strtr($res['text'], array(
        '&quot;' => '',
        '&amp;'  => '',
        '&lt;'   => '',
        '&gt;'   => '',
        '&#039;' => ''
    ));
    $hdr = mb_substr($hdr, 0, 30);
    $hdr = Validate::checkout($hdr);
    $textl = mb_strlen($res['text']) > 30 ? $hdr . '...' : $hdr;
}

/*
-----------------------------------------------------------------
Переключаем режимы работы
-----------------------------------------------------------------
*/
$actions = array(
    'addfile'  => 'addfile.php',
    'addvote'  => 'addvote.php',
    'close'    => 'close.php',
    'curators' => 'curators.php',
    'deltema'  => 'deltema.php',
    'delvote'  => 'delvote.php',
    'editpost' => 'editpost.php',
    'editvote' => 'editvote.php',
    'file'     => 'file.php',
    'files'    => 'files.php',
    'filter'   => 'filter.php',
    'loadtem'  => 'loadtem.php',
    'massdel'  => 'massdel.php',
    'nt'       => 'nt.php',
    'per'      => 'per.php',
    'post'     => 'post.php',
    'ren'      => 'ren.php',
    'restore'  => 'restore.php',
    'say'      => 'say.php',
    'tema'     => 'tema.php',
    'users'    => 'users.php',
    'vip'      => 'vip.php',
    'vote'     => 'vote.php',
    'who'      => 'who.php'
);
if (isset($actions[Vars::$ACT]) && is_file(MODPATH . Vars::$MODULE . DIRECTORY_SEPARATOR . '_inc' . DIRECTORY_SEPARATOR . $actions[Vars::$ACT])) {
    require_once(MODPATH . Vars::$MODULE . DIRECTORY_SEPARATOR . '_inc' . DIRECTORY_SEPARATOR . $actions[Vars::$ACT]);
} else {
    /*
    -----------------------------------------------------------------
    Если форум закрыт, то для Админов выводим напоминание
    -----------------------------------------------------------------
    */
    if (!isset(Vars::$ACL['forum']) || !Vars::$ACL['forum']) {
        echo '<div class="alarm">' . lng('forum_closed') . '</div>';
    } elseif (isset(Vars::$ACL['forum']) && Vars::$ACL['forum'] == 3) {
        echo '<div class="rmenu">' . lng('read_only') . '</div>';
    }
    if (!Vars::$USER_ID) {
        if (isset($_GET['newup'])) {
            $_SESSION['uppost'] = 1;
        }
        if (isset($_GET['newdown'])) {
            $_SESSION['uppost'] = 0;
        }
    }
    if (Vars::$ID) {
        /*
        -----------------------------------------------------------------
        Определяем тип запроса (каталог, или тема)
        -----------------------------------------------------------------
        */
        $type = mysql_query("SELECT * FROM `forum` WHERE `id`= " . Vars::$ID);
        if (!mysql_num_rows($type)) {
            // Если темы не существует, показываем ошибку
            echo Functions::displayError(lng('error_topic_deleted'), '<a href="' . Vars::$URI . '">' . lng('to_forum') . '</a>');
            exit;
        }
        $type1 = mysql_fetch_assoc($type);

        /*
        -----------------------------------------------------------------
        Фиксация факта прочтения Топика
        -----------------------------------------------------------------
        */
        if (Vars::$USER_ID && $type1['type'] == 't') {
            $req_r = mysql_query("SELECT * FROM `cms_forum_rdm` WHERE `topic_id` = " . Vars::$ID . " AND `user_id` = " . Vars::$USER_ID . " LIMIT 1");
            if (mysql_num_rows($req_r)) {
                $res_r = mysql_fetch_assoc($req_r);
                if ($type1['time'] > $res_r['time'])
                    mysql_query("UPDATE `cms_forum_rdm` SET `time` = '" . time() . "' WHERE `topic_id` = " . Vars::$ID . " AND `user_id` = " . Vars::$USER_ID . " LIMIT 1");
            } else {
                mysql_query("INSERT INTO `cms_forum_rdm` SET `topic_id` = " . Vars::$ID . ", `user_id` = " . Vars::$USER_ID . ", `time` = '" . time() . "'");
            }
        }

        /*
        -----------------------------------------------------------------
        Получаем структуру форума
        -----------------------------------------------------------------
        */
        $res = TRUE;
        $parent = $type1['refid'];
        while ($parent != '0' && $res != FALSE) {
            $req = mysql_query("SELECT * FROM `forum` WHERE `id` = '$parent' LIMIT 1");
            $res = mysql_fetch_assoc($req);
            if ($res['type'] == 'f' || $res['type'] == 'r')
                $tree[] = '<a href="' . Vars::$URI . '?id=' . $parent . '">' . $res['text'] . '</a>';
            $parent = $res['refid'];
        }
        $tree[] = '<a href="' . Vars::$URI . '">' . lng('forum') . '</a>';
        krsort($tree);
        if ($type1['type'] != 't' && $type1['type'] != 'm')
            $tree[] = '<b>' . $type1['text'] . '</b>';

        /*
        -----------------------------------------------------------------
        Счетчик файлов и ссылка на них
        -----------------------------------------------------------------
        */
        $sql = (Vars::$USER_RIGHTS == 9) ? "" : " AND `del` != '1'";
        if ($type1['type'] == 'f') {
            $count = mysql_result(mysql_query("SELECT COUNT(*) FROM `cms_forum_files` WHERE `cat` = " . Vars::$ID . $sql), 0);
            if ($count > 0)
                $filelink = '<a href="' . Vars::$URI . '?act=files&amp;c=' . Vars::$ID . '">' . lng('files_category') . '</a>';
        } elseif ($type1['type'] == 'r') {
            $count = mysql_result(mysql_query("SELECT COUNT(*) FROM `cms_forum_files` WHERE `subcat` = " . Vars::$ID . $sql), 0);
            if ($count > 0)
                $filelink = '<a href="' . Vars::$URI . '?act=files&amp;s=' . Vars::$ID . '">' . lng('files_section') . '</a>';
        } elseif ($type1['type'] == 't') {
            $count = mysql_result(mysql_query("SELECT COUNT(*) FROM `cms_forum_files` WHERE `topic` = " . Vars::$ID . $sql), 0);
            if ($count > 0)
                $filelink = '<a href="' . Vars::$URI . '?act=files&amp;t=' . Vars::$ID . '">' . lng('files_topic') . '</a>';
        }
        $filelink = isset($filelink) ? $filelink . '&#160;<span class="red">(' . $count . ')</span>' : FALSE;

        /*
        -----------------------------------------------------------------
        Счетчик "Кто в теме?"
        -----------------------------------------------------------------
        */
        //TODO: Доработать!
        $wholink = FALSE;
//        if (Vars::$user_id && $type1['type'] == 't') {
//            $online_u = mysql_result(mysql_query("SELECT COUNT(*) FROM `users` WHERE `last_visit` > " . (time() - 300) . " AND `place` = 'forum,$id'"), 0);
//            $online_g = mysql_result(mysql_query("SELECT COUNT(*) FROM `cms_sessions` WHERE `last_visit` > " . (time() - 300) . " AND `place` = 'forum,$id'"), 0);
//            $wholink = '<a href="' . Vars::$URI . '?act=who&amp;id=' . $id . '">' . lng('who_here') . '?</a>&#160;<span class="red">(' . $online_u . '&#160;/&#160;' . $online_g . ')</span><br/>';
//        }

        /*
        -----------------------------------------------------------------
        Выводим верхнюю панель навигации
        -----------------------------------------------------------------
        */
        echo '<p>' . Counters::forumCountNew(1) . '</p>' .
            '<div class="phdr">' . Functions::displayMenu($tree) . '</div>' .
            '<div class="topmenu"><a href="' . Vars::$URI . '/search?id=' . Vars::$ID . '">' . lng('search') . '</a>' . ($filelink ? ' | ' . $filelink : '') . ($wholink ? ' | ' . $wholink : '') . '</div>';

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
                $req = mysql_query("SELECT `id`, `text`, `soft` FROM `forum` WHERE `type`='r' AND `refid` = " . Vars::$ID . " ORDER BY `realid`");
                $total = mysql_num_rows($req);
                if ($total) {
                    $i = 0;
                    while (($res = mysql_fetch_assoc($req)) !== FALSE) {
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
                    echo '<div class="menu"><p>' . lng('section_list_empty') . '</p></div>';
                }
                echo '<div class="phdr">' . lng('total') . ': ' . $total . '</div>';
                break;

            case 'r':
                /*
                -----------------------------------------------------------------
                Список топиков
                -----------------------------------------------------------------
                */
                $total = mysql_result(mysql_query("SELECT COUNT(*) FROM `forum` WHERE `type`='t' AND `refid` = " . Vars::$ID . (Vars::$USER_RIGHTS >= 7 ? '' : " AND `close`!='1'")), 0);
                if ((Vars::$USER_ID && !isset(Vars::$USER_BAN['1']) && !isset(Vars::$USER_BAN['11']) && Vars::$SYSTEM_SET['mod_forum'] != 3) || Vars::$USER_RIGHTS) {
                    // Кнопка создания новой темы
                    echo '<div class="gmenu"><form action="' . Vars::$URI . '?act=nt&amp;id=' . Vars::$ID . '" method="post"><input type="submit" value="' . lng('new_topic') . '" /></form></div>';
                }
                if ($total) {
                    $req = mysql_query("SELECT * FROM `forum` WHERE `type`='t'" . (Vars::$USER_RIGHTS >= 7 ? '' : " AND `close`!='1'") . " AND `refid` = " . Vars::$ID . " ORDER BY `vip` DESC, `time` DESC " . Vars::db_pagination());
                    $i = 0;
                    while (($res = mysql_fetch_assoc($req)) !== FALSE) {
                        if ($res['close'])
                            echo '<div class="rmenu">';
                        else
                            echo $i % 2 ? '<div class="list2">' : '<div class="list1">';
                        $nikuser = mysql_query("SELECT `from` FROM `forum` WHERE `type` = 'm' AND `close` != '1' AND `refid` = '" . $res['id'] . "' ORDER BY `time` DESC LIMIT 1");
                        $nam = mysql_fetch_assoc($nikuser);
                        $colmes = mysql_query("SELECT COUNT(*) FROM `forum` WHERE `type`='m' AND `refid`='" . $res['id'] . "'" . (Vars::$USER_RIGHTS >= 7 ? '' : " AND `close` != '1'"));
                        $colmes1 = mysql_result($colmes, 0);
                        $cpg = ceil($colmes1 / Vars::$USER_SET['page_size']);
                        $np = mysql_result(mysql_query("SELECT COUNT(*) FROM `cms_forum_rdm` WHERE `time` >= '" . $res['time'] . "' AND `topic_id` = '" . $res['id'] . "' AND `user_id` = " . Vars::$USER_ID), 0);
                        // Значки
                        $icons = array(
                            ($np ? (!$res['vip'] ? Functions::getIcon('forum_normal.png') : '') : Functions::getIcon('forum_new.png')),
                            ($res['vip'] ? Functions::getIcon('forum_pin.png') : ''),
                            ($res['realid'] ? Functions::loadModuleImage('chart.png') : ''),
                            ($res['edit'] ? Functions::getIcon('forum_closed.png') : '')
                        );
                        echo Functions::displayMenu($icons, '&#160;', '&#160;');
                        echo '<a href="' . Vars::$URI . '?id=' . $res['id'] . '">' . $res['text'] . '</a> [' . $colmes1 . ']';
                        if ($cpg > 1) {
                            echo '<a href="' . Vars::$URI . '?id=' . $res['id'] . '&amp;page=' . $cpg . '">&#160;&gt;&gt;</a>';
                        }
                        echo '<div class="sub">';
                        echo $res['from'];
                        if (!empty($nam['from'])) {
                            echo '&#160;/&#160;' . $nam['from'];
                        }
                        echo ' <span class="gray">(' . Functions::displayDate($res['time']) . ')</span></div></div>';
                        ++$i;
                    }
                    unset($_SESSION['fsort_id']);
                    unset($_SESSION['fsort_users']);
                } else {
                    echo '<div class="menu"><p>' . lng('topic_list_empty') . '</p></div>';
                }
                echo '<div class="phdr">' . lng('total') . ': ' . $total . '</div>';
                if ($total > Vars::$USER_SET['page_size']) {
                    echo '<div class="topmenu">' . Functions::displayPagination(Vars::$URI . '?id=' . Vars::$ID . '&amp;', Vars::$START, $total, Vars::$USER_SET['page_size']) . '</div>' .
                        '<p><form action="' . Vars::$URI . '?id=' . Vars::$ID . '" method="post">' .
                        '<input type="text" name="page" size="2"/>' .
                        '<input type="submit" value="' . lng('to_page') . ' &gt;&gt;"/>' .
                        '</form></p>';
                }
                break;

            case 't':
                /*
                -----------------------------------------------------------------
                Читаем топик
                -----------------------------------------------------------------
                */
                $filter = isset($_SESSION['fsort_id']) && $_SESSION['fsort_id'] == Vars::$ID ? 1 : 0;
                $sql = '';
                if ($filter && !empty($_SESSION['fsort_users'])) {
                    // Подготавливаем запрос на фильтрацию юзеров
                    $sw = 0;
                    $sql = ' AND (';
                    $fsort_users = unserialize($_SESSION['fsort_users']);
                    foreach ($fsort_users as $val) {
                        if ($sw) {
                            $sql .= ' OR ';
                        }
                        $sortid = intval($val);
                        $sql .= "`forum`.`user_id` = '$sortid'";
                        $sw = 1;
                    }
                    $sql .= ')';
                }
                if (Vars::$USER_ID && !$filter) {
                    // Фиксация факта прочтения топика
                }
                if (Vars::$USER_RIGHTS < 7 && $type1['close'] == 1) {
                    echo '<div class="rmenu"><p>' . lng('topic_deleted') . '<br/><a href="?id=' . $type1['refid'] . '">' . lng('to_section') . '</a></p></div>';
                    exit;
                }
                // Счетчик постов темы
                $colmes = mysql_result(mysql_query("SELECT COUNT(*) FROM `forum` WHERE `type`='m'$sql AND `refid` = " . Vars::$ID . (Vars::$USER_RIGHTS >= 7 ? '' : " AND `close` != '1'")), 0);
                // Выводим название топика
                echo '<div class="phdr"><a name="up" id="up"></a><a href="#down">' . Functions::getImage('down.png') . '</a>&#160;&#160;<b>' . $type1['text'] . '</b></div>';
                if ($colmes > Vars::$USER_SET['page_size']) {
                    echo '<div class="topmenu">' . Functions::displayPagination(Vars::$URI . '?id=' . Vars::$ID . '&amp;', Vars::$START, $colmes, Vars::$USER_SET['page_size']) . '</div>';
                }
                // Метки удаления темы
                if ($type1['close']) {
                    echo '<div class="rmenu">' . lng('topic_delete_who') . ': <b>' . $type1['close_who'] . '</b></div>';
                } elseif (!empty($type1['close_who']) && Vars::$USER_RIGHTS >= 7) {
                    echo '<div class="gmenu"><small>' . lng('topic_delete_whocancel') . ': <b>' . $type1['close_who'] . '</b></small></div>';
                }
                // Метки закрытия темы
                if ($type1['edit']) {
                    echo '<div class="rmenu">' . lng('topic_closed') . '</div>';
                }
                /*
                -----------------------------------------------------------------
                Блок голосований
                -----------------------------------------------------------------
                */
                if ($type1['realid']) {
                    $clip_forum = isset($_GET['clip']) ? '&amp;clip' : '';
                    $vote_user = mysql_result(mysql_query("SELECT COUNT(*) FROM `cms_forum_vote_users` WHERE `user` = " . Vars::$USER_ID . " AND `topic` = " . Vars::$ID), 0);
                    $topic_vote = mysql_fetch_assoc(mysql_query("SELECT `name`, `time`, `count` FROM `cms_forum_vote` WHERE `type`='1' AND `topic` = " . Vars::$ID . " LIMIT 1"));
                    echo '<div  class="gmenu"><b>' . Validate::checkout($topic_vote['name']) . '</b><br />';
                    $vote_result = mysql_query("SELECT `id`, `name`, `count` FROM `cms_forum_vote` WHERE `type`='2' AND `topic` = " . Vars::$ID . " ORDER BY `id` ASC");
                    if (!$type1['edit'] && !isset($_GET['vote_result']) && Vars::$USER_ID && $vote_user == 0) {
                        // Выводим форму с опросами
                        echo'<form action="' . Vars::$URI . '?act=vote&amp;id=' . Vars::$ID . '" method="post">';
                        while (($vote = mysql_fetch_assoc($vote_result)) !== FALSE) {
                            echo '<input type="radio" value="' . $vote['id'] . '" name="vote"/> ' . Validate::checkout($vote['name'], 0, 1) . '<br />';
                        }
                        echo'<p><input type="submit" name="submit" value="' . lng('vote') . '"/><br /><a href="' . Vars::$URI . '?id=' . Vars::$ID . '&amp;start=' . Vars::$START . '&amp;vote_result' . $clip_forum .
                            '">' . lng('results') . '</a></p></form></div>';
                    } else {
                        // Выводим результаты голосования
                        echo '<small>';
                        while (($vote = mysql_fetch_assoc($vote_result)) !== FALSE) {
                            $count_vote = $topic_vote['count'] ? round(100 / $topic_vote['count'] * $vote['count']) : 0;
                            echo Validate::checkout($vote['name'], 0, 1) . ' [' . $vote['count'] . ']<br />';
                            echo '<img src="' . Vars::$HOME_URL . '/assets/misc/vote_img.php?img=' . $count_vote . '" alt="' . lng('rating') . ': ' . $count_vote . '%" /><br />';
                        }
                        echo '</small></div><div class="bmenu">' . lng('total_votes') . ': ';
                        if (Vars::$USER_RIGHTS > 6) {
                            echo '<a href="' . Vars::$URI . '?act=users&amp;id=' . Vars::$ID . '">' . $topic_vote['count'] . '</a>';
                        } else {
                            echo $topic_vote['count'];
                        }
                        echo '</div>';
                        if (Vars::$USER_ID && $vote_user == 0) {
                            echo '<div class="bmenu"><a href="' . Vars::$URI . '?id=' . Vars::$ID . '&amp;start=' . Vars::$START . $clip_forum . '">' . lng('vote') . '</a></div>';
                        }
                    }
                }
                $curators = !empty($type1['curators']) ? unserialize($type1['curators']) : array();
                $curator = FALSE;
                if (Vars::$USER_RIGHTS < 6 && Vars::$USER_RIGHTS != 3 && Vars::$USER_ID) {
                    if (array_key_exists(Vars::$USER_ID, $curators)) $curator = TRUE;
                }
                /*
                -----------------------------------------------------------------
                Фиксация первого поста в теме
                -----------------------------------------------------------------
                */
                if (($set_forum['postclip'] == 2 && ($set_forum['upfp'] ? Vars::$START < (ceil($colmes - Vars::$USER_SET['page_size'])) : Vars::$START > 0)) || isset($_GET['clip'])) {
                    $postreq = mysql_query("SELECT `forum`.*, `users`.`sex`, `users`.`rights`, `users`.`last_visit`, `users`.`status`, `users`.`datereg`
                    FROM `forum` LEFT JOIN `users` ON `forum`.`user_id` = `users`.`id`
                    WHERE `forum`.`type` = 'm' AND `forum`.`refid` = " . Vars::$ID . (Vars::$USER_RIGHTS >= 7 ? "" : " AND `forum`.`close` != '1'") . "
                    ORDER BY `forum`.`id` LIMIT 1");
                    $postres = mysql_fetch_assoc($postreq);
                    echo '<div class="topmenu"><p>';
                    if (Vars::$USER_ID && Vars::$USER_ID != $postres['user_id']) {
                        echo '<a href="../users/profile.php?user=' . $postres['user_id'] . '&amp;fid=' . $postres['id'] . '"><b>' . $postres['from'] . '</b></a> ' .
                            '<a href="' . Vars::$URI . '?act=say&amp;id=' . $postres['id'] . '&amp;start=' . Vars::$START . '"> ' . lng('reply_btn') . '</a> ' .
                            '<a href="' . Vars::$URI . '?act=say&amp;id=' . $postres['id'] . '&amp;start=' . Vars::$START . '&amp;cyt"> ' . lng('cytate_btn') . '</a> ';
                    } else {
                        echo '<b>' . $postres['from'] . '</b> ';
                    }
                    $user_rights = array(
                        1 => 'Kil',
                        3 => 'Mod',
                        6 => 'Smd',
                        7 => 'Adm',
                        8 => 'SV'
                    );
                    echo @$user_rights[$postres['rights']];
                    echo (time() > $postres['last_visit'] + 300 ? '<span class="red"> [Off]</span>' : '<span class="green"> [ON]</span>');
                    echo ' <span class="gray">(' . Functions::displayDate($postres['time']) . ')</span><br/>';
                    if ($postres['close']) {
                        echo '<span class="red">' . lng('post_deleted') . '</span><br/>';
                    }
                    echo Validate::checkout(mb_substr($postres['text'], 0, 500), 0, 2);
                    if (mb_strlen($postres['text']) > 500) {
                        echo '...<a href="' . Vars::$URI . '?act=post&amp;id=' . $postres['id'] . '">' . lng('read_all') . '</a>';
                    }
                    echo '</p></div>';
                }
                if ($filter) {
                    echo '<div class="rmenu">' . lng('filter_on') . '</div>';
                }
                // Задаем правила сортировки (новые внизу / вверху)
                if (Vars::$USER_ID) {
                    $order = $set_forum['upfp'] ? 'DESC' : 'ASC';
                } else {
                    $order = ((empty($_SESSION['uppost'])) || ($_SESSION['uppost'] == 0)) ? 'ASC' : 'DESC';
                }
                // Запрос в базу
                $req = mysql_query("SELECT `forum`.*, `users`.`sex`, `users`.`rights`, `users`.`last_visit`, `users`.`status`, `users`.`join_date`
                FROM `forum` LEFT JOIN `users` ON `forum`.`user_id` = `users`.`id`
                WHERE `forum`.`type` = 'm' AND `forum`.`refid` = " . Vars::$ID . (Vars::$USER_RIGHTS >= 7 ? "" : " AND `forum`.`close` != '1'") . "$sql ORDER BY `forum`.`id` $order " . Vars::db_pagination());
                // Верхнее поле "Написать"
                if ((Vars::$USER_ID && !$type1['edit'] && $set_forum['upfp'] && Vars::$SYSTEM_SET['mod_forum'] != 3) || (Vars::$USER_RIGHTS >= 7 && $set_forum['upfp'])) {
                    echo '<div class="gmenu"><form name="form1" action="' . Vars::$URI . '?act=say&amp;id=' . Vars::$ID . '" method="post">';
                    if ($set_forum['farea']) {
                        echo '<p>' .
                            (!Vars::$IS_MOBILE ? TextParser::autoBB('form1', 'msg') : '') .
                            '<textarea rows="' . Vars::$USER_SET['field_h'] . '" name="msg"></textarea></p>' .
                            '<p><input type="checkbox" name="addfiles" value="1" /> ' . lng('add_file') .
                            (Vars::$USER_SET['translit'] ? '<br /><input type="checkbox" name="msgtrans" value="1" /> ' . lng('translit') : '') .
                            '</p><p><input type="submit" name="submit" value="' . lng('write') . '" style="width: 107px; cursor: pointer;"/> ' .
                            ($set_forum['preview'] ? '<input type="submit" value="' . lng('preview') . '" style="width: 107px; cursor: pointer;"/>' : '') .
                            '</p></form></div>';
                    } else {
                        echo '<p><input type="submit" name="submit" value="' . lng('write') . '"/></p></form></div>';
                    }
                }

                if (Vars::$USER_RIGHTS == 3 || Vars::$USER_RIGHTS >= 6) {
                    echo '<form action="' . Vars::$URI . '?act=massdel" method="post">';
                }

                for ($i = 1; $res = mysql_fetch_assoc($req); ++$i) {
                    if ($res['close']) {
                        echo '<div class="rmenu">';
                    } else {
                        echo $i % 2 ? '<div class="list2">' : '<div class="list1">';
                    }
                    if (Vars::$USER_SET['avatar']) {
                        echo '<table cellpadding="0" cellspacing="0"><tr><td>';
                        if (file_exists((ROOTPATH . 'files' . DIRECTORY_SEPARATOR . 'users' . DIRECTORY_SEPARATOR . 'avatar' . DIRECTORY_SEPARATOR . $res['user_id'] . '.gif'))) {
                            echo '<img src="' . Vars::$HOME_URL . '/files/users/avatar/' . $res['user_id'] . '.gif" width="32" height="32" alt="' . $res['from'] . '" />&#160;';
                        } else {
                            echo Functions::getImage('empty.png', $res['from']) . '&#160;';
                        }
                        echo '</td><td>';
                    }
                    if ($res['sex']) {
                        echo Functions::getImage('usr_' . ($res['sex'] == 'm' ? 'm' : 'w') . ($res['join_date'] > time() - 86400 ? '_new' : '') . '.png', '', 'align="middle"') . '&#160;';
                    } else {
                        echo Functions::getIcon('delete.png', '', '', 'align="middle"') . '&#160;';
                    }
                    // Ник юзера и ссылка на его анкету
                    if (Vars::$USER_ID && Vars::$USER_ID != $res['user_id']) {
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
                    echo (time() > $res['last_visit'] + 300 ? '<span class="red"> [Off]</span> ' : '<span class="green"> [ON]</span> ');
                    // Ссылки на ответ и цитирование
                    if (Vars::$USER_ID && Vars::$USER_ID != $res['user_id']) {
                        echo '<a href="' . Vars::$URI . '?act=say&amp;id=' . $res['id'] . '&amp;start=' . Vars::$START . '">' . lng('reply_btn') . '</a>&#160;' .
                            '<a href="' . Vars::$URI . '?act=say&amp;id=' . $res['id'] . '&amp;start=' . Vars::$START . '&amp;cyt">' . lng('cytate_btn') . '</a> ';
                    }
                    // Время поста
                    echo ' <span class="gray">(' . Functions::displayDate($res['time']) . ')</span><br />';
                    // Статус юзера
                    if (!empty($res['status'])) {
                        echo '<div class="status">' . Functions::getImage('label.png', '', 'align="middle"') . '&#160;' . Validate::checkout($res['status']) . '</div>';
                    }
                    if (Vars::$USER_SET['avatar']) {
                        echo '</td></tr></table>';
                    }
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
                        $text = Validate::checkout($text, 1, 1);
                        $text = preg_replace('#\[c\](.*?)\[/c\]#si', '<div class="quote">\1</div>', $text);
                        if (Vars::$USER_SET['smileys']) {
                            $text = Functions::smileys($text, $res['rights'] ? 1 : 0);
                        }
                        echo TextParser::noTags($text) . '...<br /><a href="' . Vars::$URI . '?act=post&amp;id=' . $res['id'] . '">' . lng('read_all') . ' &gt;&gt;</a>';
                    } else {
                        // Или, обрабатываем тэги и выводим весь текст
                        $text = Validate::checkout($text, 1, 1);
                        if (Vars::$USER_SET['smileys']) {
                            $text = Functions::smileys($text, $res['rights'] ? 1 : 0);
                        }
                        echo $text;
                    }
                    if ($res['kedit']) {
                        // Если пост редактировался, показываем кем и когда
                        echo '<br /><span class="gray"><small>' . lng('edited') . ' <b>' . $res['edit'] . '</b> (' . Functions::displayDate($res['tedit']) . ') <b>[' . $res['kedit'] . ']</b></small></span>';
                    }
                    // Если есть прикрепленный файл, выводим его описание
                    $freq = mysql_query("SELECT * FROM `cms_forum_files` WHERE `post` = '" . $res['id'] . "'");
                    if (mysql_num_rows($freq) > 0) {
                        $fres = mysql_fetch_assoc($freq);
                        $fls = round(@filesize(ROOTPATH . 'files' . DIRECTORY_SEPARATOR . 'forum' . DIRECTORY_SEPARATOR . $fres['filename']) / 1024, 2);
                        echo '<br /><span class="gray">' . lng('attached_file') . ':';
                        // Предпросмотр изображений
                        $att_ext = strtolower(Functions::format(ROOTPATH . 'files' . DIRECTORY_SEPARATOR . 'forum' . DIRECTORY_SEPARATOR . $fres['filename']));
                        $pic_ext = array(
                            'gif',
                            'jpg',
                            'jpeg',
                            'png'
                        );
                        if (in_array($att_ext, $pic_ext)) {
                            echo '<div><a href="' . Vars::$URI . '?act=file&amp;id=' . $fres['id'] . '">';
                            echo '<img src="' . Vars::$HOME_URL . '/assets/misc/forum_thumbinal.php?file=' . (urlencode($fres['filename'])) . '" alt="' . lng('click_to_view') . '" /></a></div>';
                        } else {
                            echo '<br /><a href="' . Vars::$URI . '?act=file&amp;id=' . $fres['id'] . '">' . $fres['filename'] . '</a>';
                        }
                        echo ' (' . $fls . ' кб.)<br/>';
                        echo lng('downloads') . ': ' . $fres['dlcount'] . ' ' . lng('time') . '</span>';
                        $file_id = $fres['id'];
                    }
                    if (((Vars::$USER_RIGHTS == 3 || Vars::$USER_RIGHTS >= 6 || $curator) && Vars::$USER_RIGHTS >= $res['rights']) || ($res['user_id'] == Vars::$USER_ID && !$set_forum['upfp'] && (Vars::$START + $i) == $colmes && $res['time'] > time() - 300) || ($res['user_id'] == Vars::$USER_ID && $set_forum['upfp'] && Vars::$START == 0 && $i == 1 && $res['time'] > time() - 300)) {
                        // Ссылки на редактирование / удаление постов
                        $menu = array(
                            '<a href="' . Vars::$URI . '?act=editpost&amp;id=' . $res['id'] . '">' . lng('edit') . '</a>',
                            (Vars::$USER_RIGHTS >= 7 && $res['close'] == 1 ? '<a href="' . Vars::$URI . '?act=editpost&amp;mod=restore&amp;id=' . $res['id'] . '">' . lng('restore') . '</a>' : ''),
                            ($res['close'] == 1 ? '' : '<a href="' . Vars::$URI . '?act=editpost&amp;mod=del&amp;id=' . $res['id'] . '">' . lng('delete') . '</a>')
                        );
                        echo '<div class="sub">';
                        if (Vars::$USER_RIGHTS == 3 || Vars::$USER_RIGHTS >= 6)
                            echo '<input type="checkbox" name="delch[]" value="' . $res['id'] . '"/>&#160;';
                        echo Functions::displayMenu($menu);
                        if ($res['close']) {
                            echo '<div class="red">' . lng('who_delete_post') . ': <b>' . $res['close_who'] . '</b></div>';
                        } elseif (!empty($res['close_who'])) {
                            echo '<div class="green">' . lng('who_restore_post') . ': <b>' . $res['close_who'] . '</b></div>';
                        }
                        if (Vars::$USER_RIGHTS == 3 || Vars::$USER_RIGHTS >= 6) {
                            if ($res['ip_via_proxy']) {
                                echo '<div class="gray"><b class="red"><a href="' . Vars::$HOME_URL . '/admin?act=search_ip&amp;ip=' . long2ip($res['ip']) . '">' . long2ip($res['ip']) . '</a></b> - ' .
                                    '<a href="' . Vars::$HOME_URL . '/admin?act=search_ip&amp;ip=' . long2ip($res['ip_via_proxy']) . '">' . long2ip($res['ip_via_proxy']) . '</a>' .
                                    ' - ' . $res['soft'] . '</div>';
                            } else {
                                echo '<div class="gray"><a href="' . Vars::$HOME_URL . '/admin?act=search_ip&amp;ip=' . long2ip($res['ip']) . '">' . long2ip($res['ip']) . '</a> - ' . $res['soft'] . '</div>';
                            }
                        }
                        echo '</div>';
                    }
                    echo '</div>';
                }
                if (Vars::$USER_RIGHTS == 3 || Vars::$USER_RIGHTS >= 6) {
                    echo '<div class="rmenu"><input type="submit" value=" ' . lng('delete') . ' "/></div>';
                    echo '</form>';
                }
                // Нижнее поле "Написать"
                if ((Vars::$USER_ID && !$type1['edit'] && !$set_forum['upfp'] && Vars::$SYSTEM_SET['mod_forum'] != 3) || (Vars::$USER_RIGHTS >= 7 && !$set_forum['upfp'])) {
                    echo '<div class="gmenu"><form name="form2" action="' . Vars::$URI . '?act=say&amp;id=' . Vars::$ID . '" method="post">';
                    if ($set_forum['farea']) {
                        echo '<p>';
                        if (!Vars::$IS_MOBILE) {
                            echo TextParser::autoBB('form2', 'msg');
                        }
                        echo '<textarea rows="' . Vars::$USER_SET['field_h'] . '" name="msg"></textarea><br/></p>' .
                            '<p><input type="checkbox" name="addfiles" value="1" /> ' . lng('add_file');
                        if (Vars::$USER_SET['translit']) {
                            echo '<br /><input type="checkbox" name="msgtrans" value="1" /> ' . lng('translit');
                        }
                        echo'</p><p><input type="submit" name="submit" value="' . lng('write') . '" style="width: 107px; cursor: pointer;"/> ' .
                            ($set_forum['preview'] ? '<input type="submit" value="' . lng('preview') . '" style="width: 107px; cursor: pointer;"/>' : '') .
                            '</p></form></div>';
                    } else {
                        echo '<p><input type="submit" name="submit" value="' . lng('write') . '"/></p></form></div>';
                    }
                }
                echo '<div class="phdr"><a name="down" id="down"></a><a href="#up">' . Functions::getImage('up.png') . '</a>' .
                    '&#160;&#160;' . lng('total') . ': ' . $colmes . '</div>';
                if ($colmes > Vars::$USER_SET['page_size']) {
                    echo '<div class="topmenu">' . Functions::displayPagination(Vars::$URI . '?id=' . Vars::$ID . '&amp;', Vars::$START, $colmes, Vars::$USER_SET['page_size']) . '</div>' .
                        '<p><form action="' . Vars::$URI . '?id=' . Vars::$ID . '" method="post">' .
                        '<input type="text" name="page" size="2"/>' .
                        '<input type="submit" value="' . lng('to_page') . ' &gt;&gt;"/>' .
                        '</form></p>';
                } else {
                    echo '<br />';
                }
                /*
                -----------------------------------------------------------------
                Ссылки на модераторские функции
                -----------------------------------------------------------------
                */
                if ($curators) {
                    $array = array();
                    foreach ($curators as $key => $value) {
                        $array[] = '<a href="../users/profile.php?user=' . $key . '">' . $value . '</a>';
                    }
                    echo '<p><div class="func">' . lng('curators') . ': ' . implode(', ', $array) . '</div></p>';
                }
                if (Vars::$USER_RIGHTS == 3 || Vars::$USER_RIGHTS >= 6) {
                    echo '<p><div class="func">';
                    if (Vars::$USER_RIGHTS >= 7) {
                        echo '<a href="' . Vars::$URI . '?act=curators&amp;id=' . Vars::$ID . '&amp;start=' . Vars::$START . '">' . lng('curators_of_the_topic') . '</a><br />';
                    }
                    echo isset($topic_vote) && $topic_vote > 0
                        ? '<a href="' . Vars::$URI . '?act=editvote&amp;id=' . Vars::$ID . '">' . lng('edit_vote') . '</a><br/><a href="' . Vars::$URI . '?act=delvote&amp;id=' . Vars::$ID . '">' . lng('delete_vote') . '</a><br/>'
                        : '<a href="' . Vars::$URI . '?act=addvote&amp;id=' . Vars::$ID . '">' . lng('add_vote') . '</a><br/>';
                    echo '<a href="' . Vars::$URI . '?act=ren&amp;id=' . Vars::$ID . '">' . lng('topic_rename') . '</a><br/>';
                    // Закрыть - открыть тему
                    if ($type1['edit'] == 1) {
                        echo '<a href="' . Vars::$URI . '?act=close&amp;id=' . Vars::$ID . '">' . lng('topic_open') . '</a><br/>';
                    } else {
                        echo '<a href="' . Vars::$URI . '?act=close&amp;id=' . Vars::$ID . '&amp;closed">' . lng('topic_close') . '</a><br/>';
                    }
                    // Удалить - восстановить тему
                    if ($type1['close'] == 1) {
                        echo '<a href="' . Vars::$URI . '?act=restore&amp;id=' . Vars::$ID . '">' . lng('topic_restore') . '</a><br/>';
                    }
                    echo '<a href="' . Vars::$URI . '?act=deltema&amp;id=' . Vars::$ID . '">' . lng('topic_delete') . '</a><br/>';
                    if ($type1['vip'] == 1) {
                        echo '<a href="' . Vars::$URI . '?act=vip&amp;id=' . Vars::$ID . '">' . lng('topic_unfix') . '</a>';
                    } else {
                        echo '<a href="' . Vars::$URI . '?act=vip&amp;id=' . Vars::$ID . '&amp;vip">' . lng('topic_fix') . '</a>';
                    }
                    echo '<br/><a href="' . Vars::$URI . '?act=per&amp;id=' . Vars::$ID . '">' . lng('topic_move') . '</a></div></p>';
                }
                if ($wholink) {
                    echo '<div>' . $wholink . '</div>';
                }
                if ($filter) {
                    echo '<div><a href="' . Vars::$URI . '?act=filter&amp;id=' . Vars::$ID . '&amp;do=unset">' . lng('filter_cancel') . '</a></div>';
                } else {
                    echo '<div><a href="' . Vars::$URI . '?act=filter&amp;id=' . Vars::$ID . '&amp;start=' . Vars::$START . '">' . lng('filter_on_author') . '</a></div>';
                }
                echo '<a href="' . Vars::$URI . '?act=tema&amp;id=' . Vars::$ID . '">' . lng('download_topic') . '</a>';
                break;

            default:
                /*
                -----------------------------------------------------------------
                Если неверные данные, показываем ошибку
                -----------------------------------------------------------------
                */
                echo Functions::displayError(lng('error_wrong_data'));
                break;
        }
    } else {
        /*
        -----------------------------------------------------------------
        Список Категорий форума
        -----------------------------------------------------------------
        */
        $count = mysql_result(mysql_query("SELECT COUNT(*) FROM `cms_forum_files`" . (Vars::$USER_RIGHTS >= 7 ? '' : " WHERE `del` != '1'")), 0);
        echo'<p>' . Counters::forumCountNew(1) . '</p>' .
            '<div class="phdr"><b>' . lng('forum') . '</b></div>' .
            '<div class="topmenu"><a href="' . Vars::$URI . '/search">' . lng('search') . '</a> | <a href="' . Vars::$URI . '?act=files">' . lng('files_forum') . '</a> <span class="red">(' . $count . ')</span></div>';
        $req = mysql_query("SELECT `id`, `text`, `soft` FROM `forum` WHERE `type`='f' ORDER BY `realid`");
        if (mysql_num_rows($req)) {
            for ($i = 0; $res = mysql_fetch_assoc($req); ++$i) {
                echo $i % 2 ? '<div class="list2">' : '<div class="list1">';
                $count = mysql_result(mysql_query("SELECT COUNT(*) FROM `forum` WHERE `type`='r' and `refid`='" . $res['id'] . "'"), 0);
                echo '<a href="' . Vars::$URI . '?id=' . $res['id'] . '">' . $res['text'] . '</a> [' . $count . ']';
                if (!empty($res['soft'])) {
                    echo '<div class="sub"><span class="gray">' . $res['soft'] . '</span></div>';
                }
                echo '</div>';
            }
        } else {
            echo'<div class="menu"><p>' . lng('list_empty') . '</p></div>';
        }
        //TODO: Доработать счетчики
        $online_u = mysql_result(mysql_query("SELECT COUNT(*) FROM `cms_sessions` WHERE `session_timestamp` > " . (time() - 300) . " AND `place` LIKE 'forum%'"), 0);
        $online_g = mysql_result(mysql_query("SELECT COUNT(*) FROM `cms_sessions` WHERE `session_timestamp` > " . (time() - 300) . " AND `place` LIKE 'forum%'"), 0);
        echo '<div class="phdr">' . (Vars::$USER_ID ? '<a href="' . Vars::$URI . '?act=who">' . lng('who_in_forum') . '</a>' : lng('who_in_forum')) . '&#160;(' . $online_u . '&#160;/&#160;' . $online_g . ')</div>';
        unset($_SESSION['fsort_id']);
        unset($_SESSION['fsort_users']);
    }

    // Навигация внизу страницы
    echo '<p>' . (Vars::$ID ? '<a href="' . Vars::$URI . '">' . lng('to_forum') . '</a><br />' : '');
    if (!Vars::$ID) {
        echo'<a href="' . Vars::$URI . '/rules">' . lng('forum_rules') . '</a><br/>';
        if (Vars::$USER_RIGHTS >= 7) {
            echo'<a href="' . Vars::$URI . '/admin">' . lng('admin_panel') . '</a><br/>';
        }
    }
    echo '</p>';
    if (!Vars::$USER_ID) {
        if ((empty($_SESSION['uppost'])) || ($_SESSION['uppost'] == 0)) {
            echo '<a href="' . Vars::$URI . '?id=' . Vars::$ID . '&amp;page=' . Vars::$PAGE . '&amp;newup">' . lng('new_on_top') . '</a>';
        } else {
            echo '<a href="' . Vars::$URI . '?id=' . Vars::$ID . '&amp;page=' . Vars::$PAGE . '&amp;newdown">' . lng('new_on_bottom') . '</a>';
        }
    }
}
