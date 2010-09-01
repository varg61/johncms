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

defined('_IN_JOHNCMS') or die('Error: restricted access');

/*
-----------------------------------------------------------------
Показ различных счетчиков внизу страницы
-----------------------------------------------------------------
*/
function display_counters() {
    global $headmod;
    $req = mysql_query("SELECT * FROM `cms_counters` WHERE `switch` = '1' ORDER BY `sort` ASC");

    if (mysql_num_rows($req) > 0) {
        while ($res = mysql_fetch_array($req)) {
            $link1 = ($res['mode'] == 1 || $res['mode'] == 2) ? $res['link1'] : $res['link2'];
            $link2 = $res['mode'] == 2 ? $res['link1'] : $res['link2'];
            $count = ($headmod == 'mainpage') ? $link1 : $link2;
            if (!empty($count))
                echo $count;
        }
    }
}

/*
-----------------------------------------------------------------
Сообщения об ошибках
-----------------------------------------------------------------
*/
function display_error($error = false, $link = '') {
    global $lng;

    if ($error) {
        $out = '<div class="rmenu"><p><b>' . $lng['error'] . '!</b>';
        if (is_array($error)) {
            foreach ($error as $val)$out .= '<div>' . $val . '</div>';
        } else {
            $out .= '<br />' . $error;
        }
        $out .= '</p><p>' . $link . '</p></div>';
        return $out;
    } else {
        return false;
    }
}

/*
-----------------------------------------------------------------
Отображение различных меню
-----------------------------------------------------------------
*/
function display_menu($val = array ()) {
    $out = '';
    ksort($val);
    $last = array_pop($val);

    foreach ($val as $menu) {
        $out .= $menu . ' | ';
    }
    return $out . $last;
}

/*
-----------------------------------------------------------------
Постраничная навигация
За основу взята аналогичная функция от форума SMF2.0
-----------------------------------------------------------------
*/
function display_pagination($base_url, $start, $max_value, $num_per_page) {
    $pgcont = 4;
    $pgcont = (int)($pgcont - ($pgcont % 2)) / 2;

    if ($start >= $max_value)
        $start = max(0, (int)$max_value - (((int)$max_value % (int)$num_per_page) == 0 ? $num_per_page : ((int)$max_value % (int)$num_per_page)));
    else
        $start = max(0, (int)$start - ((int)$start % (int)$num_per_page));
    $base_link = '<a class="navpg" href="' . strtr($base_url, array ('%' => '%%')) . 'start=%d' . '">%s</a> ';
    $pageindex = $start == 0 ? '' : sprintf($base_link, $start - $num_per_page, '&lt;&lt;');

    if ($start > $num_per_page * $pgcont)
        $pageindex .= sprintf($base_link, 0, '1');

    if ($start > $num_per_page * ($pgcont + 1))
        $pageindex .= '<span style="font-weight: bold;"> ... </span>';

    for ($nCont = $pgcont; $nCont >= 1; $nCont--)
        if ($start >= $num_per_page * $nCont) {
            $tmpStart = $start - $num_per_page * $nCont;
            $pageindex .= sprintf($base_link, $tmpStart, $tmpStart / $num_per_page + 1);
        }
    $pageindex .= '[<b>' . ($start / $num_per_page + 1) . '</b>] ';
    $tmpMaxPages = (int)(($max_value - 1) / $num_per_page) * $num_per_page;

    for ($nCont = 1; $nCont <= $pgcont; $nCont++)
        if ($start + $num_per_page * $nCont <= $tmpMaxPages) {
            $tmpStart = $start + $num_per_page * $nCont;
            $pageindex .= sprintf($base_link, $tmpStart, $tmpStart / $num_per_page + 1);
        }

    if ($start + $num_per_page * ($pgcont + 1) < $tmpMaxPages)
        $pageindex .= '<span style="font-weight: bold;"> ... </span>';

    if ($start + $num_per_page * $pgcont < $tmpMaxPages)
        $pageindex .= sprintf($base_link, $tmpMaxPages, $tmpMaxPages / $num_per_page + 1);

    if ($start + $num_per_page < $max_value) {
        $display_page = ($start + $num_per_page) > $max_value ? $max_value : ($start + $num_per_page);
        $pageindex .= sprintf($base_link, $display_page, '&gt;&gt;');
    }
    return $pageindex;
}

/*
-----------------------------------------------------------------
Отображения личных данных пользователя
-----------------------------------------------------------------
$user          (array)     массив запроса в таблицу `users`
$arg           (array)     Массив параметров отображения
   [lastvisit] (boolean)   Дата и время последнего визита
   [stshide]   (boolean)   Скрыть статус (если есть)
   [iphide]    (boolean)   Скрыть (не показывать) IP и UserAgent
   [iphist]    (boolean)   Показывать ссылку на историю IP

   [header]    (string)    Текст в строке после Ника пользователя
   [body]      (string)    Основной текст, под ником пользователя
   [sub]       (string)    Строка выводится вверху области "sub"
   [footer]    (string)    Строка выводится внизу области "sub"
-----------------------------------------------------------------
*/
function display_user($user = array (), $arg = array ()) {
    global $set_user, $realtime, $user_id, $admp, $home, $rights, $lng;
    $out = false;

    if (!$user['id']) {
        $out = '<b>Гость</b>';
        if (!empty($user['name']))
            $out .= ': ' . $user['name'];
        if (!empty($arg['header']))
            $out .= ' ' . $arg['header'];
    } else {
        if ($set_user['avatar']) {
            $out .= '<table cellpadding="0" cellspacing="0"><tr><td>';
            if (file_exists(($home . '/files/users/avatar/' . $user['id'] . '.png')))
                $out .= '<img src="' . $home . '/files/users/avatar/' . $user['id'] . '.png" width="32" height="32" alt="" />&#160;';
            else
                $out .= '<img src="' . $home . '/images/empty.png" width="32" height="32" alt="" />&#160;';
            $out .= '</td><td>';
        }
        if ($user['sex'])
            $out .= '<img src="' . $home . '/theme/' . $set_user['skin'] . '/images/' . ($user['sex'] == 'm' ? 'm' : 'w') . ($user['datereg'] > $realtime - 86400 ? '_new' : '') . '.png" width="16" height="16" align="middle" alt="' . ($user['sex'] == 'm' ? 'М' : 'Ж') . '" />&#160;';
        else
            $out .= '<img src="' . $home . '/images/del.png" width="12" height="12" align="middle" />&#160;';
        $out .= !$user_id || $user_id == $user['id'] ? '<b>' . $user['name'] . '</b>' : '<a href="' . $home . '/users/profile/index.php?id=' . $user['id'] . '"><b>' . $user['name'] . '</b></a>';
        $rank = array (
            0 => '',
            1 => '(GMod)',
            2 => '(CMod)',
            3 => '(FMod)',
            4 => '(DMod)',
            5 => '(LMod)',
            6 => '(Smd)',
            7 => '(Adm)',
            9 => '(SV!)'
        );
        $out .= ' ' . $rank[$user['rights']];
        $out .= ($realtime > $user['lastdate'] + 300 ? '<span class="red"> [Off]</span>' : '<span class="green"> [ON]</span>');
        if (!empty($arg['header']))
            $out .= ' ' . $arg['header'];
        if (!$arg['stshide'] && !empty($user['status']))
            $out .= '<div class="status"><img src="' . $home . '/theme/' . $set_user['skin'] . '/images/label.png" alt="" align="middle" />&#160;' . $user['status'] . '</div>';
        if ($set_user['avatar'])
            $out .= '</td></tr></table>';
    }

    if ($arg['body'])
        $out .= '<div>' . $arg['body'] . '</div>';
    $ipinf = $rights > 0 && !$arg['iphide'] ? 1 : 0;
    $lastvisit = $realtime > $user['lastdate'] + 300 && $arg['lastvisit'] ? date("d.m.Y (H:i)", $user['lastdate']) : false;

    if ($ipinf || $lastvisit || $arg['sub'] || $arg['footer']) {
        $out .= '<div class="sub">';
        if ($arg['sub'])
            $out .= '<div>' . $arg['sub'] . '</div>';
        if ($lastvisit)
            $out .= '<div><span class="gray">' . $lng['last_visit'] . ':</span> ' . $lastvisit . '</div>';
        $iphist = '';
        if ($ipinf && $arg['iphist']) {
            $iptotal = mysql_result(mysql_query("SELECT COUNT(*) FROM `cms_users_iphistory` WHERE `user_id` = '" . $user['id'] . "'"), 0);
            $iphist = '&#160;<a href="' . $home . '/users/profile/index.php?act=history_ip&amp;id=' . $user['id'] . '">[' . $iptotal . ']</a>';
        }
        if ($ipinf) {
            $out .= '<div><span class="gray">UserAgent:</span> ' . $user['browser'] . '</div>';
            $out .= '<div><span class="gray">' . $lng['last_ip'] . ':</span> <a href="' . $home . '/' . $admp . '/index.php?act=usr_search_ip&amp;ip=' . $user['ip'] . '">' . long2ip($user['ip']) . '</a>' . $iphist . '</div>';
        }
        if ($arg['footer'])
            $out .= $arg['footer'];
        $out .= '</div>';
    }
    return $out;
}

/*
-----------------------------------------------------------------
Счетчик непрочитанных тем на форуме
-----------------------------------------------------------------
$mod = 0   Возвращает число непрочитанных тем
$mod = 1   Выводит ссылки на непрочитанное
-----------------------------------------------------------------
*/
function forum_new($mod = 0) {
    global $user_id, $rights, $lng;

    if ($user_id) {
        $req = mysql_query("SELECT COUNT(*) FROM `forum`
        LEFT JOIN `cms_forum_rdm` ON `forum`.`id` = `cms_forum_rdm`.`topic_id` AND `cms_forum_rdm`.`user_id` = '" . $user_id . "'
        WHERE `forum`.`type`='t'" . ($rights >= 7 ? "" : " AND `forum`.`close` != '1'") . "
        AND (`cms_forum_rdm`.`topic_id` Is Null
        OR `forum`.`time` > `cms_forum_rdm`.`time`)");
        $total = mysql_result($req, 0);
        if ($mod)
            return '<a href="index.php?act=new">' . $lng['unread'] . '</a>&#160;' . ($total ? '<span class="red">(<b>' . $total . '</b>)</span>' : '');
        else
            return $total;
    } else {
        if ($mod)
            return '<a href="index.php?act=new">' . $lng['last_activity'] . '</a>';
        else
            return false;
    }
}

/*
-----------------------------------------------------------------
Получаем данные пользователя
-----------------------------------------------------------------
*/
function get_user($id = false) {
    global $datauser, $user_id;

    if ($id && $id != $user_id) {
        $req = mysql_query("SELECT * FROM `users` WHERE `id` = '$id' LIMIT 1");
        if (mysql_num_rows($req)) {
            return mysql_fetch_assoc($req);
        } else {
            return false;
        }
    } else {
        return $datauser;
    }
}

/*
-----------------------------------------------------------------
Статистика Чата
-----------------------------------------------------------------
*/
function stat_chat() {
//TODO: Написать функцию статистики Чата
return 0; }

/*
-----------------------------------------------------------------
Колличество зарегистрированных пользователей
-----------------------------------------------------------------
*/
function stat_countusers() {
    global $realtime;
    $total = mysql_result(mysql_query("SELECT COUNT(*) FROM `users`"), 0);
    $res = mysql_result(mysql_query("SELECT COUNT(*) FROM `users` WHERE `datereg` > '" . ($realtime - 86400) . "'"), 0);

    if ($res > 0)
        $total .= '&#160;<span class="red">+' . $res . '</span>';
    return $total;
}

/*
-----------------------------------------------------------------
Статистика загрузок
-----------------------------------------------------------------
*/
function stat_download() {
    global $realtime;
    $total = mysql_result(mysql_query("SELECT COUNT(*) FROM `download` WHERE `type` = 'file'"), 0);
    $old = $realtime - (3 * 24 * 3600);
    $new = mysql_result(mysql_query("SELECT COUNT(*) FROM `download` WHERE `time` > '" . $old . "' AND `type` = 'file'"), 0);

    if ($new > 0)
        $total .= '&#160;/&#160;<span class="red"><a href="/download/?act=new">+' . $new . '</a></span>';
    return $total;
}

/*
-----------------------------------------------------------------
Статистика Форума
-----------------------------------------------------------------
*/
function stat_forum() {
    global $user_id, $rights, $home;
    $total_thm = mysql_result(mysql_query("SELECT COUNT(*) FROM `forum` WHERE `type` = 't'" . ($rights >= 7 ? "" : " AND `close` != '1'")), 0);
    $total_msg = mysql_result(mysql_query("SELECT COUNT(*) FROM `forum` WHERE `type` = 'm'" . ($rights >= 7 ? "" : " AND `close` != '1'")), 0);
    $out = $total_thm . '&#160;/&#160;' . $total_msg . '';

    if ($user_id) {
        $new = forum_new();
        if ($new)
            $out .= '&#160;/&#160;<span class="red"><a href="' . $home . '/forum/index.php?act=new">+' . $new . '</a></span>';
    }

    return $out;
}

/*
-----------------------------------------------------------------
Статистика галлереи
-----------------------------------------------------------------
$mod = 1    будет выдавать только колличество новых картинок
-----------------------------------------------------------------
*/
function stat_gallery($mod = 0) {
    global $realtime;
    $old = $realtime - (3 * 24 * 3600);
    $new = mysql_result(mysql_query("SELECT COUNT(*) FROM `gallery` WHERE `time` > '" . $old . "' AND `type` = 'ft'"), 0);

    if ($mod == 0) {
        $total = mysql_result(mysql_query("SELECT COUNT(*) FROM `gallery` WHERE `type` = 'ft'"), 0);
        $out = $total;
        if ($new > 0)
            $out .= '&#160;/&#160;<span class="red"><a href="/gallery/index.php?act=new">+' . $new . '</a></span>';
    } else {
        $out = $new;
    }
    return $out;
}

/*
-----------------------------------------------------------------
Статистика гостевой
-----------------------------------------------------------------
$mod = 1    колличество новых в гостевой
$mod = 2    колличество новых в Админ-Клубе
-----------------------------------------------------------------
*/
function stat_guestbook($mod = 0) {
    global $realtime, $rights;

    switch ($mod) {
        case 1:
            $count = mysql_result(mysql_query("SELECT COUNT(*) FROM `guest` WHERE `adm`='0' AND `time` > '" . ($realtime - 86400) . "'"), 0);
            break;

        case 2:
            if ($rights >= 1)
                $count = mysql_result(mysql_query("SELECT COUNT(*) FROM `guest` WHERE `adm`='1' AND `time` > '" . ($realtime - 86400) . "'"), 0);
            break;

        default:
            $count = mysql_result(mysql_query("SELECT COUNT(*) FROM `guest` WHERE `adm`='0' AND `time` > '" . ($realtime - 86400) . "'"), 0);
            if ($rights >= 1) {
                $req = mysql_query("SELECT COUNT(*) FROM `guest` WHERE `adm`='1' AND `time`>'" . ($realtime - 86400) . "'");
                $count = $count . '&#160;/&#160;<span class="red"><a href="guestbook/index.php?act=ga&amp;do=set">' . mysql_result($req, 0) . '</a></span>';
            }
    }
    return $count;
}

/*
-----------------------------------------------------------------
Вывод коэффициента сжатия Zlib
-----------------------------------------------------------------
*/
function stat_gzip() {
    global $set, $lng;

    if ($set['gzip']) {
        $Contents = ob_get_contents();
        $gzib_file = strlen($Contents);
        $gzib_file_out = strlen(gzcompress($Contents, 9));
        $gzib_pro = round(100 - (100 / ($gzib_file / $gzib_file_out)), 1);
        echo '<div>' . $lng['gzip_on'] . ' (' . $gzib_pro . '%)</div>';
    } else {
        echo '<div>' . $lng['gzip_off'] . '</div>';
    }
}

/*
-----------------------------------------------------------------
Статистика библиотеки
-----------------------------------------------------------------
*/
function stat_library() {
    global $realtime, $rights;
    $countf = mysql_result(mysql_query("SELECT COUNT(*) FROM `lib` WHERE `type` = 'bk' AND `moder` = '1'"), 0);
    $old = $realtime - (3 * 24 * 3600);
    $countf1 = mysql_result(mysql_query("SELECT COUNT(*) FROM `lib` WHERE `time` > '" . $old . "' AND `type` = 'bk' AND `moder` = '1'"), 0);
    $out = $countf;

    if ($countf1 > 0)
        $out = $out . '&#160;/&#160;<span class="red"><a href="/library/index.php?act=new">+' . $countf1 . '</a></span>';
    $countm = mysql_result(mysql_query("SELECT COUNT(*) FROM `lib` WHERE `type` = 'bk' AND `moder` = '0'"), 0);

    if (($rights == 5 || $rights >= 6) && $countm > 0)
        $out = $out . "/<a href='" . $home . "/library/index.php?act=moder'><font color='#FF0000'> M:$countm</font></a>";
    return $out;
}

/*
-----------------------------------------------------------------
Дата последней новости
-----------------------------------------------------------------
*/
function stat_news() {
    global $set_user;
    $req = mysql_query("SELECT `time` FROM `news` ORDER BY `time` DESC LIMIT 1");

    if (mysql_num_rows($req)) {
        $res = mysql_fetch_array($req);
        return date("H:i/d.m.y", $res['time'] + $set_user['sdvig'] * 3600);
    } else {
        return false;
    }
}

/*
-----------------------------------------------------------------
Счетчик посетителей онлайн
-----------------------------------------------------------------
*/
function stat_online() {
    global $realtime, $user_id, $home, $lng;
    $users = mysql_result(mysql_query("SELECT COUNT(*) FROM `users` WHERE `lastdate` > '" . ($realtime - 300) . "'"), 0);
    $guests = mysql_result(mysql_query("SELECT COUNT(*) FROM `cms_guests` WHERE `lastdate` > '" . ($realtime - 300) . "'"), 0);
    return ($user_id ? '<a href="' . $home . '/users/online.php">' . $lng['online'] . ': ' . $users . ' / ' . $guests . '</a>' : $lng['online'] . ': ' . $users . ' / ' . $guests);
}

/*
-----------------------------------------------------------------
Счетсик времени, проведенного на сайте
-----------------------------------------------------------------
*/
function stat_timeonline() {
    global $realtime, $datauser, $user_id, $lng;

    if ($user_id)
        echo '<div>' . $lng['online'] . ': ' . gmdate('H:i:s', ($realtime - $datauser['sestime'])) . '</div>';
}

/*
-----------------------------------------------------------------
Обработка ссылок и тэгов BBCODE в тексте
-----------------------------------------------------------------
*/
function tags($var = '') {
    $var = preg_replace('#\[php\](.*?)\[\/php\]#se', "highlight('$1')", $var);
    $var = preg_replace('#\[b\](.*?)\[/b\]#si', '<span style="font-weight: bold;">\1</span>', $var);
    $var = preg_replace('#\[i\](.*?)\[/i\]#si', '<span style="font-style:italic;">\1</span>', $var);
    $var = preg_replace('#\[u\](.*?)\[/u\]#si', '<span style="text-decoration:underline;">\1</span>', $var);
    $var = preg_replace('#\[s\](.*?)\[/s\]#si', '<span style="text-decoration: line-through;">\1</span>', $var);
    $var = preg_replace('#\[red\](.*?)\[/red\]#si', '<span style="color:red">\1</span>', $var);
    $var = preg_replace('#\[green\](.*?)\[/green\]#si', '<span style="color:green">\1</span>', $var);
    $var = preg_replace('#\[blue\](.*?)\[/blue\]#si', '<span style="color:blue">\1</span>', $var);
    $var = preg_replace('#\[c\](.*?)\[/c\]#si', '<div class="quote">\1</div>', $var);
    $var = preg_replace_callback('~\\[url=(https?://.+?)\\](.+?)\\[/url\\]|(https?://(www.)?[0-9a-z\.-]+\.[0-9a-z]{2,6}[0-9a-zA-Z/\?\.\~&amp;_=/%-:#]*)~', 'url_replace', $var);
    return $var;
}

/*
-----------------------------------------------------------------
Вспомогательная Функция обработки ссылок форума
-----------------------------------------------------------------
*/
function forum_link($m) {
    global $home;

    if (!isset($m[3])) {
        return '[url=' . $m[1] . ']' . $m[2] . '[/url]';
    } else {
        $p = parse_url($m[3]);
        if ('http://' . $p['host'] . $p['path'] . '?id=' == $home . '/forum/index.php?id=') {
            $thid = abs(intval(preg_replace('/(.*?)id=/si', '', $m[3])));
            $req = mysql_query("SELECT `text` FROM `forum` WHERE `id`= '$thid' AND `type` = 't' AND `close` != '1'");
            if (mysql_num_rows($req) > 0) {
                $res = mysql_fetch_array($req);
                $name = strtr($res['text'], array (
                    '&quot;' => '',
                    '&amp;' => '',
                    '&lt;' => '',
                    '&gt;' => '',
                    '&#039;' => '',
                    '[' => '',
                    ']' => ''
                ));
                if (mb_strlen($name) > 40)
                    $name = mb_substr($name, 0, 40) . '...';
                return '[url=' . $m[3] . ']' . $name . '[/url]';
            } else {
                return $m[3];
            }
        } else
            return $m[3];
    }
}

/*
-----------------------------------------------------------------
Служебная функция подсветки PHP кода
-----------------------------------------------------------------
*/
function highlight($php) {
    $php = strtr($php, array (
        '<br />' => '',
        '\\' => 'slash_JOHNCMS'
    ));

    $php = html_entity_decode(trim($php), ENT_QUOTES, 'UTF-8');
    $php = substr($php, 0, 2) != "<?" ? $php = "<?php\n" . $php . "\n?>" : $php;
    $php = highlight_string(stripslashes($php), true);
    $php = strtr($php, array (
        'slash_JOHNCMS' => '&#92;',
        ':' => '&#58;',
        '[' => '&#91;',
        '&#160;' => ' '
    ));

    return '<div class="phpcode">' . $php . '</div>';
}

/*
-----------------------------------------------------------------
Служебная функция парсинга URL
-----------------------------------------------------------------
*/
function url_replace($m) {
    if (!isset($m[3]))
        return '<a href="' . str_replace(':', '&#58;', $m[1]) . '">' . str_replace(':', '&#58;', $m[2]) . '</a>';
    else {
        $m[3] = str_replace(':', '&#58;', $m[3]);
        return '<a href="' . $m[3] . '">' . $m[3] . '</a>';
    }
}

/*
-----------------------------------------------------------------
Вырезание BBcode тэгов из текста
-----------------------------------------------------------------
*/
function notags($var = '') {
    $var = strtr($var, array (
        '[green]' => '',
        '[/green]' => '',
        '[red]' => '',
        '[/red]' => '',
        '[blue]' => '',
        '[/blue]' => '',
        '[b]' => '',
        '[/b]' => '',
        '[i]' => '',
        '[/i]' => '',
        '[u]' => '',
        '[/u]' => '',
        '[s]' => '',
        '[/s]' => '',
        '[c]' => '',
        '[/c]' => ''
    ));

    return $var;
}

/*
-----------------------------------------------------------------
Маскировка ссылок в тексте
-----------------------------------------------------------------
*/
function antilink($var) {
    //TODO: Убрать eregi
    //$var = eregi_replace("((https?|ftp)://)([[:alnum:]_=/-]+(\\.[[:alnum:]_=/-]+)*(/[[:alnum:]+&._=/~%]*(\\?[[:alnum:]?+&_=/;%]*)?)?)", "[реклама]", $var);
    $var = strtr($var, array (
        ".ru" => "***",
        ".com" => "***",
        ".net" => "***",
        ".org" => "***",
        ".info" => "***",
        ".mobi" => "***",
        ".wen" => "***",
        ".kmx" => "***",
        ".h2m" => "***"
    ));

    return $var;
}

/*
-----------------------------------------------------------------
Транслитерация текста
-----------------------------------------------------------------
*/
function trans($str) {
    $str = strtr($str, array (
        'a' => 'а',
        'b' => 'б',
        'v' => 'в',
        'g' => 'г',
        'd' => 'д',
        'e' => 'е',
        'yo' => 'ё',
        'zh' => 'ж',
        'z' => 'з',
        'i' => 'и',
        'j' => 'й',
        'k' => 'к',
        'l' => 'л',
        'm' => 'м',
        'n' => 'н',
        'o' => 'о',
        'p' => 'п',
        'r' => 'р',
        's' => 'с',
        't' => 'т',
        'u' => 'у',
        'f' => 'ф',
        'h' => 'х',
        'c' => 'ц',
        'ch' => 'ч',
        'w' => 'ш',
        'sh' => 'щ',
        'q' => 'ъ',
        'y' => 'ы',
        'x' => 'э',
        'yu' => 'ю',
        'ya' => 'я',
        'A' => 'А',
        'B' => 'Б',
        'V' => 'В',
        'G' => 'Г',
        'D' => 'Д',
        'E' => 'Е',
        'YO' => 'Ё',
        'ZH' => 'Ж',
        'Z' => 'З',
        'I' => 'И',
        'J' => 'Й',
        'K' => 'К',
        'L' => 'Л',
        'M' => 'М',
        'N' => 'Н',
        'O' => 'О',
        'P' => 'П',
        'R' => 'Р',
        'S' => 'С',
        'T' => 'Т',
        'U' => 'У',
        'F' => 'Ф',
        'H' => 'Х',
        'C' => 'Ц',
        'CH' => 'Ч',
        'W' => 'Ш',
        'SH' => 'Щ',
        'Q' => 'Ъ',
        'Y' => 'Ы',
        'X' => 'Э',
        'YU' => 'Ю',
        'YA' => 'Я'
    ));

    return $str;
}

/*
-----------------------------------------------------------------
Декодирование htmlentities, PHP4совместимый режим
-----------------------------------------------------------------
*/
function unhtmlentities($string) {
    $string = str_replace('&amp;', '&', $string);
    $string = preg_replace('~&#x0*([0-9a-f]+);~ei', 'chr(hexdec("\\1"))', $string);
    $string = preg_replace('~&#0*([0-9]+);~e', 'chr(\\1)', $string);
    $trans_tbl = get_html_translation_table(HTML_ENTITIES);
    $trans_tbl = array_flip($trans_tbl);
    return strtr($string, $trans_tbl);
}

/*
-----------------------------------------------------------------
Функция пересчета на дни, или часы
-----------------------------------------------------------------
*/
function timecount($var) {
    global $lng;
    $str = '';

    if ($var < 0)
        $var = 0;
    $day = ceil($var / 86400);

    if ($var > 345600) {
        $str = $day . ' ' . $lng['timecount_days'];
    }  elseif ($var >= 172800) {
        $str = $day . ' ' . $lng['timecount_days_r'];
    }  elseif ($var >= 86400) {
        $str = '1 ' . $lng['timecount_day'];
    } else {
        $str = date('G:i', $var);
    }
    return $str;
}

/*
-----------------------------------------------------------------
Форматирование размера файлов
-----------------------------------------------------------------
*/
function formatsize($size) {
    if ($size >= 1073741824) {
        $size = round($size / 1073741824 * 100) / 100 . ' Gb';
    }  elseif ($size >= 1048576) {
        $size = round($size / 1048576 * 100) / 100 . ' Mb';
    }  elseif ($size >= 1024) {
        $size = round($size / 1024 * 100) / 100 . ' Kb';
    } else {
        $size = $size . ' b';
    }
    return $size;
}

/*
-----------------------------------------------------------------
Проверка переменных
-----------------------------------------------------------------
*/
function check($str) {
    $str = htmlentities(trim($str), ENT_QUOTES, 'UTF-8');
    $str = nl2br($str);
    $str = strtr($str, array (
        chr(0)=> '',
        chr(1)=> '',
        chr(2)=> '',
        chr(3)=> '',
        chr(4)=> '',
        chr(5)=> '',
        chr(6)=> '',
        chr(7)=> '',
        chr(8)=> '',
        chr(9)=> '',
        chr(10)=> '',
        chr(11)=> '',
        chr(12)=> '',
        chr(13)=> '',
        chr(14)=> '',
        chr(15)=> '',
        chr(16)=> '',
        chr(17)=> '',
        chr(18)=> '',
        chr(19)=> '',
        chr(20)=> '',
        chr(21)=> '',
        chr(22)=> '',
        chr(23)=> '',
        chr(24)=> '',
        chr(25)=> '',
        chr(26)=> '',
        chr(27)=> '',
        chr(28)=> '',
        chr(29)=> '',
        chr(30)=> '',
        chr(31)=> ''
    ));

    $str = str_replace("\'", "&#39;", $str);
    $str = str_replace('\\', "&#92;", $str);
    $str = str_replace("|", "I", $str);
    $str = str_replace("||", "I", $str);
    $str = str_replace("/\\\$/", "&#36;", $str);
    $str = mysql_real_escape_string($str);
    return $str;
}

/*
-----------------------------------------------------------------
Обработка текстов перед выводом на экран
-----------------------------------------------------------------
$br=1           обработка переносов строк
$br=2           подстановка пробела, вместо переноса
$tags=1         обработка тэгов
$tags=2         вырезание тэгов
-----------------------------------------------------------------
*/
function checkout($str, $br = 0, $tags = 0) {
    $str = htmlentities(trim($str), ENT_QUOTES, 'UTF-8');

    if ($br == 1)
        $str = nl2br($str);
    elseif ($br == 2)
        $str = str_replace("\r\n", ' ', $str);

    if ($tags == 1)
        $str = tags($str);
    elseif ($tags == 2)
        $str = notags($str);
    $str = strtr($str, array (
        chr(0)=> '',
        chr(1)=> '',
        chr(2)=> '',
        chr(3)=> '',
        chr(4)=> '',
        chr(5)=> '',
        chr(6)=> '',
        chr(7)=> '',
        chr(8)=> '',
        chr(9)=> '',
        chr(11)=> '',
        chr(12)=> '',
        chr(13)=> '',
        chr(14)=> '',
        chr(15)=> '',
        chr(16)=> '',
        chr(17)=> '',
        chr(18)=> '',
        chr(19)=> '',
        chr(20)=> '',
        chr(21)=> '',
        chr(22)=> '',
        chr(23)=> '',
        chr(24)=> '',
        chr(25)=> '',
        chr(26)=> '',
        chr(27)=> '',
        chr(28)=> '',
        chr(29)=> '',
        chr(30)=> '',
        chr(31)=> ''
    ));

    return $str;
}

/*
-----------------------------------------------------------------
Обработка смайлов
-----------------------------------------------------------------
$adm=1 покажет и обычные и Админские смайлы
$adm=2 пересоздаст кэш смайлов
-----------------------------------------------------------------
*/
function smileys($str, $adm = 0) {
    global $rootpath, $home;

    // Записываем КЭШ смайлов
    if ($adm == 2) {
        // Обрабатываем простые смайлы
        $array1 = array ();
        $path = 'images/smileys/simply/';
        $dir = opendir($rootpath . $path);
        while ($file = readdir($dir)) {
            $name = explode(".", $file);
            if ($name[1] == 'gif' || $name[1] == 'jpg' || $name[1] == 'png') {
                $array1[':' . $name[0]] = '<img src="' . $home . '/' . $path . $file . '" alt="" />';
                ++$count;
            }
        }
        closedir($dir);
        // Обрабатываем Админские смайлы
        $array2 = array ();
        $array3 = array ();
        $path = 'images/smileys/admin/';
        $dir = opendir($rootpath . $path);
        while ($file = readdir($dir)) {
            $name = explode(".", $file);
            if ($name[1] == 'gif' || $name[1] == 'jpg' || $name[1] == 'png') {
                $array2[':' . trans($name[0]) . ':'] = '<img src="' . $home . '/' . $path . $file . '" alt="" />';
                $array3[':' . $name[0] . ':'] = '<img src="' . $home . '/' . $path . $file . '" alt="" />';
                ++$count;
            }
        }
        // Обрабатываем смайлы в каталогах
        $array4 = array ();
        $array5 = array ();
        $cat = glob($rootpath . 'images/smileys/user/*', GLOB_ONLYDIR);
        $total = count($cat);
        for ($i = 0; $i < $total; $i++) {
            $dir = opendir($cat[$i]);
            while ($file = readdir($dir)) {
                $name = explode(".", $file);
                if ($name[1] == 'gif' || $name[1] == 'jpg' || $name[1] == 'png') {
                    $path = str_replace('..', $home, $cat[$i]);
                    $array4[':' . trans($name[0]) . ':'] = '<img src="' . $path . '/' . $file . '" alt="" />';
                    $array5[':' . $name[0] . ':'] = '<img src="' . $path . '/' . $file . '" alt="" />';
                    ++$count;
                }
            }
            closedir($dir);
        }
        $smileys = serialize(array_merge($array1, $array4, $array5));
        $smileys_adm = serialize(array_merge($array2, $array3));
        // Записываем в файл Кэша
        if ($fp = fopen($rootpath . 'files/cache/smileys_cache.dat', 'w')) {
            fputs($fp, $smileys . "\r\n" . $smileys_adm);
            fclose($fp);
            return $count;
        } else {
            return false;
        }
    } else {
        // Выдаем кэшированные смайлы
        if (file_exists($rootpath . 'files/cache/smileys_cache.dat')) {
            $file = file($rootpath . 'files/cache/smileys_cache.dat');
            $smileys = unserialize($file[0]);
            if ($adm)
                $smileys = array_merge($smileys, unserialize($file[1]));
            return strtr($str, $smileys);
        } else {
            return $str;
        }
    }
}

/*
-----------------------------------------------------------------
Транслитерация с Русского в латиницу
-----------------------------------------------------------------
*/
function rus_lat($str) {
    $str = strtr($str, array (
        'а' => 'a',
        'б' => 'b',
        'в' => 'v',
        'г' => 'g',
        'д' => 'd',
        'е' => 'e',
        'ё' => 'e',
        'ж' => 'j',
        'з' => 'z',
        'и' => 'i',
        'й' => 'i',
        'к' => 'k',
        'л' => 'l',
        'м' => 'm',
        'н' => 'n',
        'о' => 'o',
        'п' => 'p',
        'р' => 'r',
        'с' => 's',
        'т' => 't',
        'у' => 'u',
        'ф' => 'f',
        'х' => 'h',
        'ц' => 'c',
        'ч' => 'ch',
        'ш' => 'sh',
        'щ' => 'sch',
        'ъ' => "",
        'ы' => 'y',
        'ь' => "",
        'э' => 'ye',
        'ю' => 'yu',
        'я' => 'ya'
    ));

    return $str;
}

/*
-----------------------------------------------------------------
Функция валидации IP адреса
-----------------------------------------------------------------
*/
function ip_valid($ip = '') {
    $d = explode('.', $ip);

    for ($x = 0; $x < 4; $x++)
        if (!is_numeric($d[$x]) || ($d[$x] < 0) || ($d[$x] > 255))
            return false;

    return $ip;
}

/*
-----------------------------------------------------------------
Глобальная система Антифлуда
-----------------------------------------------------------------
Режимы работы:
1 - Адаптивный
2 - День / Ночь
3 - День
4 - Ночь
-----------------------------------------------------------------
*/
function antiflood() {
    global $set, $user_id, $datauser, $realtime;
    $default = array (
        'mode' => 2,
        'day' => 10,
        'night' => 30,
        'dayfrom' => 10,
        'dayto' => 22
    );

    $af = isset($set['antiflood']) ? unserialize($set['antiflood']) : $default;

    switch ($af['mode']) {
        case 1:
            // Адаптивный режим
            $onltime = $realtime - 600;
            $adm = mysql_result(mysql_query("SELECT COUNT(*) FROM `users` WHERE `rights` > 0 AND `lastdate` > '$onltime'"), 0);
            $limit = $adm > 0 ? $af['day'] : $af['night'];
            break;

        case 3:
            // День
            $limit = $af['day'];
            break;

        case 4:
            // Ночь
            $limit = $af['night'];
            break;

        default:
            // По умолчанию день / ночь
            $c_time = date('G', $realtime);
            $limit = $c_time > $af['day'] && $c_time < $af['night'] ? $af['day'] : $af['night'];
    }

    if ($datauser['rights'] > 0)
        $limit = 4; // Для Администрации задаем лимит в 4 секунды
    $flood = $datauser['lastpost'] + $limit - $realtime;

    if ($flood > 0)
        return $flood;
    else
        return false;
}

/*
-----------------------------------------------------------------
Рекламная сеть mobileads.ru
-----------------------------------------------------------------
*/
function mobileads($mad_siteId = NULL) {
    $out = '';
    $mad_socketTimeout = 2;      // таймаут соединения с сервером mobileads.ru
    ini_set("default_socket_timeout", $mad_socketTimeout);
    $mad_pageEncoding = "UTF-8"; // устанавливаем кодировку страницы
    $mad_ua = urlencode(@$_SERVER['HTTP_USER_AGENT']);
    $mad_ip = urlencode(@$_SERVER['REMOTE_ADDR']);
    $mad_xip = urlencode(@$_SERVER['HTTP_X_FORWARDED_FOR']);
    $mad_ref = urlencode(@$_SERVER['SERVER_NAME'] . @$_SERVER['REQUEST_URI']);
    $mad_lines = "";
    $mad_fp = @fsockopen("mobileads.ru", 80, $mad_errno, $mad_errstr, $mad_socketTimeout);

    if ($mad_fp) {
        // переменная $mad_lines будет содержать массив, непарные элементы которого будут ссылками, парные - названием
        $mad_lines = @file("http://mobileads.ru/links?id=$mad_siteId&ip=$mad_ip&xip=$mad_xip&ua=$mad_ua&ref=$mad_ref");
    }
    @fclose($mad_fp); // вывод ссылок

    for ($malCount = 0; $malCount < count($mad_lines); $malCount += 2) {
        $linkURL = trim($mad_lines[$malCount]);
        $linkName = iconv("Windows-1251", $mad_pageEncoding, $mad_lines[$malCount + 1]);
        $out .= '<a href="' . $linkURL . '">' . $linkName . '</a><br />';
    }
    $_SESSION['mad_links'] = $out;
    $_SESSION['mad_time'] = $realtime;
    return $out;
}

/*
################################################################################
##                                                                            ##
##  Старые функции, которые постепенно будут удаляться.                       ##
##  НЕ ИСПОЛЬЗУЙТЕ их в своих модулях!!!                                      ##
##                                                                            ##
################################################################################
*/

function provcat($catalog) {
    $cat1 = mysql_query("select * from `download` where type = 'cat' and id = '" . $catalog . "';");
    $cat2 = mysql_num_rows($cat1);
    $adrdir = mysql_fetch_array($cat1);

    if (($cat2 == 0) || (!is_dir("$adrdir[adres]/$adrdir[name]"))) {
        echo 'Ошибка при выборе категории<br/><a href="?">К категориям</a><br/>';
        require_once('../incfiles/end.php');
        exit;
    }
}
function deletcat($catalog) {
    $dir = opendir($catalog);

    while (($file = readdir($dir))) {
        if (is_file($catalog . "/" . $file)) {
            unlink($catalog . "/" . $file);
        } else if (is_dir($catalog . "/" . $file) && ($file != ".") && ($file != "..")) {
            deletcat($catalog . "/" . $file);
        }
    }
    closedir($dir);
    rmdir($catalog);
}
function format($name) {
    $f1 = strrpos($name, ".");
    $f2 = substr($name, $f1 + 1, 999);
    $fname = strtolower($f2);
    return $fname;
}
?>