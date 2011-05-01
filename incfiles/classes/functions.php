<?php

/**
 * @package     JohnCMS
 * @link        http://johncms.com
 * @copyright   Copyright (C) 2008-2011 JohnCMS Community
 * @license     LICENSE.txt (see attached file)
 * @version     VERSION.txt (see attached file)
 * @author      http://johncms.com/about
 */

defined('_IN_JOHNCMS') or die('Restricted access');

class functions
{
    static private $smileys_cache = array();

    /*
    -----------------------------------------------------------------
    Антифлуд
    -----------------------------------------------------------------
    Режимы работы:
    1 - Адаптивный
    2 - День / Ночь
    3 - День
    4 - Ночь
    -----------------------------------------------------------------
    */
    static function antiflood()
    {
        global $set, $datauser, $realtime;
        $default = array(
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
    Маскировка ссылок в тексте
    -----------------------------------------------------------------
    */
    static function antilink($var)
    {
        $var = preg_replace('~\\[url=(https?://.+?)\\](.+?)\\[/url\\]|(https?://(www.)?[0-9a-z\.-]+\.[0-9a-z]{2,6}[0-9a-zA-Z/\?\.\~&amp;_=/%-:#]*)~', '###', $var);
        $var = strtr($var, array(
                                '.ru' => '***',
                                '.com' => '***',
                                '.biz' => '***',
                                '.cn' => '***',
                                '.in' => '***',
                                '.net' => '***',
                                '.org' => '***',
                                '.info' => '***',
                                '.mobi' => '***',
                                '.wen' => '***',
                                '.kmx' => '***',
                                '.h2m' => '***'
                           ));
        return $var;
    }

    /*
    -----------------------------------------------------------------
    ББ панель (для компьютеров)
    -----------------------------------------------------------------
    */
    static function auto_bb($form, $field)
    {
        global $set, $datauser, $lng, $user_id, $is_mobile;
        if ($is_mobile) {
            return false;
        }
        $smileys = !empty($datauser['smileys']) ? unserialize($datauser['smileys']) : '';
        if (!empty($smileys)) {
            $res_sm = '';
            $my_smileys = '<small><a href="' . $set['homeurl'] . '/pages/faq.php?act=my_smileys">' . $lng['edit_list'] . '</a></small><br />';
            foreach ($smileys as $value)
                $res_sm .= '<a href="javascript:tag(\'' . $value . '\', \'\', \':\');">:' . $value . ':</a> ';
            $my_smileys .= functions::smileys($res_sm, $datauser['rights'] >= 1 ? 1 : 0);
        } else {
            $my_smileys = '<small><a href="' . $set['homeurl'] . '/pages/faq.php?act=smileys">' . $lng['add_smileys'] . '</a></small>';
        }
        $out = '<style>
            .smileys{
			background-color: rgba(178,178,178,0.5);
            padding: 5px;
            border-radius: 3px;
            border: 1px solid white;
            display: none;
            overflow: auto;
            max-width: 250px;
            max-height: 100px;
            position: absolute;
            }
            .smileys_from:hover .smileys{
            display: block;
            }
            </style>
            <script language="JavaScript" type="text/javascript">
            function tag(text1, text2, text3) {
            if ((document.selection)) {
                document.' . $form . '.' . $field . '.focus();
                document.' . $form . '.document.selection.createRange().text = text3+text1+document.' . $form . '.document.selection.createRange().text+text2+text3;
            } else if(document.forms[\'' . $form . '\'].elements[\'' . $field . '\'].selectionStart!=undefined) {
                var element = document.forms[\'' . $form . '\'].elements[\'' . $field . '\'];
                var str = element.value;
                var start = element.selectionStart;
                var length = element.selectionEnd - element.selectionStart;
                element.value = str.substr(0, start) + text3 + text1 + str.substr(start, length) + text2 + text3 + str.substr(start + length);
            } else document.' . $form . '.' . $field . '.value += text3+text1+text2+text3;}</script>
            <a href="javascript:tag(\'[b]\', \'[/b]\', \'\')"><img src="' . $set['homeurl'] . '/images/bb/b.png" alt="b" title="' . $lng['tag_bold'] . '" border="0"/></a>
            <a href="javascript:tag(\'[i]\', \'[/i]\', \'\')"><img src="' . $set['homeurl'] . '/images/bb/i.png" alt="i" title="' . $lng['tag_italic'] . '" border="0"/></a>
            <a href="javascript:tag(\'[u]\', \'[/u]\', \'\')"><img src="' . $set['homeurl'] . '/images/bb/u.png" alt="u" title="' . $lng['tag_underline'] . '" border="0"/></a>
            <a href="javascript:tag(\'[s]\', \'[/s]\', \'\')"><img src="' . $set['homeurl'] . '/images/bb/s.png" alt="s" title="' . $lng['tag_strike'] . '" border="0"/></a>
            <a href="javascript:tag(\'[c]\', \'[/c]\', \'\')"><img src="' . $set['homeurl'] . '/images/bb/q.png" alt="quote" title="' . $lng['tag_quote'] . '" border="0"/></a>
            <a href="javascript:tag(\'[php]\', \'[/php]\', \'\')"><img src="' . $set['homeurl'] . '/images/bb/cod.png" alt="cod" title="' . $lng['tag_code'] . '" border="0"/></a>
            <a href="javascript:tag(\'[url=]\', \'[/url]\', \'\')"><img src="' . $set['homeurl'] . '/images/bb/l.png" alt="url" title="' . $lng['tag_link'] . '" border="0"/></a>
            <a href="javascript:tag(\'[red]\', \'[/red]\', \'\')"><img src="' . $set['homeurl'] . '/images/bb/re.png" alt="red" title="' . $lng['tag_red'] . '" border="0"/></a>
            <a href="javascript:tag(\'[green]\', \'[/green]\', \'\')"><img src="' . $set['homeurl'] . '/images/bb/gr.png" alt="green" title="' . $lng['tag_green'] . '" border="0"/></a>
            <a href="javascript:tag(\'[blue]\', \'[/blue]\', \'\')"><img src="' . $set['homeurl'] . '/images/bb/bl.png" alt="blue" title="' . $lng['tag_blue'] . '" border="0"/></a>';
        if ($user_id) {
            $out .= ' <span class="smileys_from" style="display: inline-block; cursor:pointer"><img src="' . $set['homeurl'] . '/images/bb/sm.png" alt="sm" title="' . $lng['smileys'] . '" border="0"/>
                <div class="smileys">' . $my_smileys . '</div></span>';
        }
        return $out . '<br />';
    }

    /*
    -----------------------------------------------------------------
    Проверка переменных
    -----------------------------------------------------------------
    */
    static function check($str)
    {
        $str = htmlentities(trim($str), ENT_QUOTES, 'UTF-8');
        $str = nl2br($str);
        $str = strtr($str, array(
                                chr(0) => '',
                                chr(1) => '',
                                chr(2) => '',
                                chr(3) => '',
                                chr(4) => '',
                                chr(5) => '',
                                chr(6) => '',
                                chr(7) => '',
                                chr(8) => '',
                                chr(9) => '',
                                chr(10) => '',
                                chr(11) => '',
                                chr(12) => '',
                                chr(13) => '',
                                chr(14) => '',
                                chr(15) => '',
                                chr(16) => '',
                                chr(17) => '',
                                chr(18) => '',
                                chr(19) => '',
                                chr(20) => '',
                                chr(21) => '',
                                chr(22) => '',
                                chr(23) => '',
                                chr(24) => '',
                                chr(25) => '',
                                chr(26) => '',
                                chr(27) => '',
                                chr(28) => '',
                                chr(29) => '',
                                chr(30) => '',
                                chr(31) => ''
                           ));
        $str = str_replace("'", "&#39;", $str);
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
    static function checkout($str, $br = 0, $tags = 0)
    {
        $str = htmlentities(trim($str), ENT_QUOTES, 'UTF-8');
        if ($br == 1)
            $str = nl2br($str);
        elseif ($br == 2)
            $str = str_replace("\r\n", ' ', $str);
        //TODO: Передеать на новую функцию подсветки Тэгов
        if ($tags == 1)
            $str = call_user_func('tags', $str);
        elseif ($tags == 2)
            $str = self::notags($str);
        $str = strtr($str, array(
                                chr(0) => '',
                                chr(1) => '',
                                chr(2) => '',
                                chr(3) => '',
                                chr(4) => '',
                                chr(5) => '',
                                chr(6) => '',
                                chr(7) => '',
                                chr(8) => '',
                                chr(9) => '',
                                chr(11) => '',
                                chr(12) => '',
                                chr(13) => '',
                                chr(14) => '',
                                chr(15) => '',
                                chr(16) => '',
                                chr(17) => '',
                                chr(18) => '',
                                chr(19) => '',
                                chr(20) => '',
                                chr(21) => '',
                                chr(22) => '',
                                chr(23) => '',
                                chr(24) => '',
                                chr(25) => '',
                                chr(26) => '',
                                chr(27) => '',
                                chr(28) => '',
                                chr(29) => '',
                                chr(30) => '',
                                chr(31) => ''
                           ));
        return $str;
    }

    /*
    -----------------------------------------------------------------
    Показ различных счетчиков внизу страницы
    -----------------------------------------------------------------
    */
    static function display_counters()
    {
        global $headmod;
        $req = mysql_query("SELECT * FROM `cms_counters` WHERE `switch` = '1' ORDER BY `sort` ASC");
        if (mysql_num_rows($req) > 0) {
            while (($res = mysql_fetch_array($req)) !== false) {
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
    static function display_error($error = '', $link = '')
    {
        global $lng;
        if ($error) {
            $out = '<div class="rmenu"><p><b>' . $lng['error'] . '!</b><br />';
            $out .= is_array($error) ? implode('<br />', $error) : $error;
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
    $delimiter - разделитель между пунктами
    $end_space - выводится в конце
    -----------------------------------------------------------------
    */
    static function display_menu($val = array(), $delimiter = ' | ', $end_space = '')
    {
        return implode($delimiter, array_diff($val, array(''))) . $end_space;
    }

    /*
    -----------------------------------------------------------------
    Постраничная навигация
    За основу взята аналогичная функция от форума SMF2.0
    -----------------------------------------------------------------
    */
    static function display_pagination($base_url, $start, $max_value, $num_per_page)
    {
        $neighbors = 2;
        if ($start >= $max_value)
            $start = max(0, (int)$max_value - (((int)$max_value % (int)$num_per_page) == 0 ? $num_per_page : ((int)$max_value % (int)$num_per_page)));
        else
            $start = max(0, (int)$start - ((int)$start % (int)$num_per_page));
        $base_link = '<a class="navpg" href="' . strtr($base_url, array('%' => '%%')) . 'start=%d' . '">%s</a> ';
        $pageindex = $start == 0 ? '' : sprintf($base_link, $start - $num_per_page, '&lt;&lt;');
        if ($start > $num_per_page * $neighbors)
            $pageindex .= sprintf($base_link, 0, '1');
        if ($start > $num_per_page * ($neighbors + 1))
            $pageindex .= '<span style="font-weight: bold;"> ... </span>';
        for ($nCont = $neighbors; $nCont >= 1; $nCont--)
            if ($start >= $num_per_page * $nCont) {
                $tmpStart = $start - $num_per_page * $nCont;
                $pageindex .= sprintf($base_link, $tmpStart, $tmpStart / $num_per_page + 1);
            }
        $pageindex .= '[<b>' . ($start / $num_per_page + 1) . '</b>] ';
        $tmpMaxPages = (int)(($max_value - 1) / $num_per_page) * $num_per_page;
        for ($nCont = 1; $nCont <= $neighbors; $nCont++)
            if ($start + $num_per_page * $nCont <= $tmpMaxPages) {
                $tmpStart = $start + $num_per_page * $nCont;
                $pageindex .= sprintf($base_link, $tmpStart, $tmpStart / $num_per_page + 1);
            }
        if ($start + $num_per_page * ($neighbors + 1) < $tmpMaxPages)
            $pageindex .= '<span style="font-weight: bold;"> ... </span>';
        if ($start + $num_per_page * $neighbors < $tmpMaxPages)
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
    static function display_user($user = false, $arg = false)
    {
        global $set, $set_user, $realtime, $user_id, $rights, $lng, $rootpath;
        $out = false;

        if (!$user['id']) {
            $out = '<b>' . $lng['guest'] . '</b>';
            if (!empty($user['name']))
                $out .= ': ' . $user['name'];
            if (!empty($arg['header']))
                $out .= ' ' . $arg['header'];
        } else {
            if ($set_user['avatar']) {
                $out .= '<table cellpadding="0" cellspacing="0"><tr><td>';
                if (file_exists(($rootpath . 'files/users/avatar/' . $user['id'] . '.png')))
                    $out .= '<img src="' . $set['homeurl'] . '/files/users/avatar/' . $user['id'] . '.png" width="32" height="32" alt="" />&#160;';
                else
                    $out .= '<img src="' . $set['homeurl'] . '/images/empty.png" width="32" height="32" alt="" />&#160;';
                $out .= '</td><td>';
            }
            if ($user['sex'])
                $out .= '<img src="' . $set['homeurl'] . '/theme/' . $set_user['skin'] . '/images/' . ($user['sex'] == 'm' ? 'm' : 'w') . ($user['datereg'] > $realtime - 86400 ? '_new' : '')
                        . '.png" width="16" height="16" align="middle" alt="' . ($user['sex'] == 'm' ? 'М' : 'Ж') . '" />&#160;';
            else
                $out .= '<img src="' . $set['homeurl'] . '/images/del.png" width="12" height="12" align="middle" />&#160;';
            $out .= !$user_id || $user_id == $user['id'] ? '<b>' . $user['name'] . '</b>' : '<a href="' . $set['homeurl'] . '/users/profile.php?user=' . $user['id'] . '"><b>' . $user['name'] . '</b></a>';
            $rank = array(
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
            if (!isset($arg['stshide']) && !empty($user['status']))
                $out .= '<div class="status"><img src="' . $set['homeurl'] . '/theme/' . $set_user['skin'] . '/images/label.png" alt="" align="middle" />&#160;' . $user['status'] . '</div>';
            if ($set_user['avatar'])
                $out .= '</td></tr></table>';
        }
        if (isset($arg['body']))
            $out .= '<div>' . $arg['body'] . '</div>';
        $ipinf = ($rights || $user['id'] && $user['id'] == $user_id) && !isset($arg['iphide']) ? 1 : 0;
        $lastvisit = $realtime > $user['lastdate'] + 300 && isset($arg['lastvisit']) ? date("d.m.Y (H:i)", $user['lastdate']) : false;

        if ($ipinf || $lastvisit || isset($arg['sub']) || isset($arg['footer'])) {
            $out .= '<div class="sub">';
            if (isset($arg['sub']))
                $out .= '<div>' . $arg['sub'] . '</div>';
            if ($lastvisit)
                $out .= '<div><span class="gray">' . $lng['last_visit'] . ':</span> ' . $lastvisit . '</div>';
            $iphist = '';
            if ($ipinf && isset($arg['iphist'])) {
                $iptotal = mysql_result(mysql_query("SELECT COUNT(*) FROM `cms_users_iphistory` WHERE `user_id` = '" . $user['id'] . "'"), 0);
                $iphist = '&#160;<a href="' . $set['homeurl'] . '/users/profile.php?act=ip&amp;user=' . $user['id'] . '">[' . $iptotal . ']</a>';
            }
            if ($ipinf) {
                $out .= '<div><span class="gray">UserAgent:</span> ' . $user['browser'] . '</div>';
                if ($rights)
                    $out .= '<div><span class="gray">' . $lng['last_ip'] . ':</span> <a href="' . $set['homeurl'] . '/' . $set['admp'] . '/index.php?act=search_ip&amp;ip=' . $user['ip'] . '">' . long2ip($user['ip']) . '</a>' . $iphist
                            . '</div>';
                else
                    $out .= '<div><span class="gray">' . $lng['last_ip'] . ':</span> ' . long2ip($user['ip']) . $iphist . '</div>';
            }
            if (isset($arg['footer']))
                $out .= $arg['footer'];
            $out .= '</div>';
        }
        return $out;
    }

    /*
    -----------------------------------------------------------------
    Форматирование имени файла
    -----------------------------------------------------------------
    */
    static function format($name)
    {
        $f1 = strrpos($name, ".");
        $f2 = substr($name, $f1 + 1, 999);
        $fname = strtolower($f2);
        return $fname;
    }

    /*
    -----------------------------------------------------------------
    Вспомогательная Функция обработки ссылок форума
    -----------------------------------------------------------------
    */
    static function forum_link($m)
    {
        global $set;
        if (!isset($m[3])) {
            return '[url=' . $m[1] . ']' . $m[2] . '[/url]';
        } else {
            $p = parse_url($m[3]);
            if ('http://' . $p['host'] . $p['path'] . '?id=' == $set['homeurl'] . '/forum/index.php?id=') {
                $thid = abs(intval(preg_replace('/(.*?)id=/si', '', $m[3])));
                $req = mysql_query("SELECT `text` FROM `forum` WHERE `id`= '$thid' AND `type` = 't' AND `close` != '1'");
                if (mysql_num_rows($req) > 0) {
                    $res = mysql_fetch_array($req);
                    $name = strtr($res['text'], array(
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
    Получаем данные пользователя
    -----------------------------------------------------------------
    */
    static function get_user($id = false)
    {
        global $datauser, $user_id;
        if(!$id)
            return false;
        if ($id && $id != $user_id) {
            $req = mysql_query("SELECT * FROM `users` WHERE `id` = '$id'");
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
    Вырезание BBcode тэгов из текста
    -----------------------------------------------------------------
    */
    static function notags($var = '')
    {
        $var = strtr($var, array(
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
    Транслитерация с Русского в латиницу
    -----------------------------------------------------------------
    */
    static function rus_lat($str)
    {
        $str = strtr($str, array(
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
    Обработка смайлов
    -----------------------------------------------------------------
    */
    static function smileys($str, $adm = false)
    {
        global $rootpath;
        if (empty(self::$smileys_cache) && ($file = file_get_contents($rootpath . 'files/cache/smileys.dat')) !== false)
            self::$smileys_cache = unserialize($file);
        return strtr($str, ($adm ? array_merge(self::$smileys_cache['usr'], self::$smileys_cache['adm']) : self::$smileys_cache['usr']));
    }

    /*
    -----------------------------------------------------------------
    Функция пересчета на дни, или часы
    -----------------------------------------------------------------
    */
    static function timecount($var)
    {
        global $lng;
        if ($var < 0)
            $var = 0;
        $day = ceil($var / 86400);
        if ($var > 345600) {
            $str = $day . ' ' . $lng['timecount_days'];
        } elseif ($var >= 172800) {
            $str = $day . ' ' . $lng['timecount_days_r'];
        } elseif ($var >= 86400) {
            $str = '1 ' . $lng['timecount_day'];
        } else {
            $str = date('G:i', $var);
        }
        return $str;
    }

    /*
    -----------------------------------------------------------------
    Транслитерация текста
    -----------------------------------------------------------------
    */
    static function trans($str)
    {
        $str = strtr($str, array(
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
    Проверка, мобильный ли браузер?
    -----------------------------------------------------------------
    */
    static function mobile_detect()
    {
        if (isset($_SESSION['is_mobile'])) {
            return $_SESSION['is_mobile'] == 1 ? true : false;
        }
        $user_agent = strtolower($_SERVER['HTTP_USER_AGENT']);
        $accept = strtolower($_SERVER['HTTP_ACCEPT']);
        if ((strpos($accept, 'text/vnd.wap.wml') !== false) || (strpos($accept, 'application/vnd.wap.xhtml+xml') !== false)) {
            $_SESSION['is_mobile'] = 1;
            return true;
        }
        if (isset($_SERVER['HTTP_X_WAP_PROFILE']) || isset($_SERVER['HTTP_PROFILE'])) {
            $_SESSION['is_mobile'] = 1;
            return true;
        }
        if (preg_match('/android|avantgo|blackberry|blazer|compal|elaine|fennec|hiptop|iemobile|ip(hone|od)|iris|kindle|lge |maemo|midp|mmp|opera m(ob|in)i|palm( os)?|phone|p(ixi|re)\/|plucker|pocket|psp|symbian|treo|up\.(browser|link)|vodafone|wap|windows (ce|phone)|xda|xiino/i', $user_agent)
            || preg_match('/1207|6310|6590|3gso|4thp|50[1-6]i|770s|802s|a wa|abac|ac(er|oo|s\-)|ai(ko|rn)|al(av|ca|co)|amoi|an(ex|ny|yw)|aptu|ar(ch|go)|as(te|us)|attw|au(di|\-m|r |s )|avan|be(ck|ll|nq)|bi(lb|rd)|bl(ac|az)|br(e|v)w|bumb|bw\-(n|u)|c55\/|capi|ccwa|cdm\-|cell|chtm|cldc|cmd\-|co(mp|nd)|craw|da(it|ll|ng)|dbte|dc\-s|devi|dica|dmob|do(c|p)o|ds(12|\-d)|el(49|ai)|em(l2|ul)|er(ic|k0)|esl8|ez([4-7]0|os|wa|ze)|fetc|fly(\-|_)|g1 u|g560|gene|gf\-5|g\-mo|go(\.w|od)|gr(ad|un)|haie|hcit|hd\-(m|p|t)|hei\-|hi(pt|ta)|hp( i|ip)|hs\-c|ht(c(\-| |_|a|g|p|s|t)|tp)|hu(aw|tc)|i\-(20|go|ma)|i230|iac( |\-|\/)|ibro|idea|ig01|ikom|im1k|inno|ipaq|iris|ja(t|v)a|jbro|jemu|jigs|kddi|keji|kgt( |\/)|klon|kpt |kwc\-|kyo(c|k)|le(no|xi)|lg( g|\/(k|l|u)|50|54|e\-|e\/|\-[a-w])|libw|lynx|m1\-w|m3ga|m50\/|ma(te|ui|xo)|mc(01|21|ca)|m\-cr|me(di|rc|ri)|mi(o8|oa|ts)|mmef|mo(01|02|bi|de|do|t(\-| |o|v)|zz)|mt(50|p1|v )|mwbp|mywa|n10[0-2]|n20[2-3]|n30(0|2)|n50(0|2|5)|n7(0(0|1)|10)|ne((c|m)\-|on|tf|wf|wg|wt)|nok(6|i)|nzph|o2im|op(ti|wv)|oran|owg1|p800|pan(a|d|t)|pdxg|pg(13|\-([1-8]|c))|phil|pire|pl(ay|uc)|pn\-2|po(ck|rt|se)|prox|psio|pt\-g|qa\-a|qc(07|12|21|32|60|\-[2-7]|i\-)|qtek|r380|r600|raks|rim9|ro(ve|zo)|s55\/|sa(ge|ma|mm|ms|ny|va)|sc(01|h\-|oo|p\-)|sdk\/|se(c(\-|0|1)|47|mc|nd|ri)|sgh\-|shar|sie(\-|m)|sk\-0|sl(45|id)|sm(al|ar|b3|it|t5)|so(ft|ny)|sp(01|h\-|v\-|v )|sy(01|mb)|t2(18|50)|t6(00|10|18)|ta(gt|lk)|tcl\-|tdg\-|tel(i|m)|tim\-|t\-mo|to(pl|sh)|ts(70|m\-|m3|m5)|tx\-9|up(\.b|g1|si)|utst|v400|v750|veri|vi(rg|te)|vk(40|5[0-3]|\-v)|vm40|voda|vulc|vx(52|53|60|61|70|80|81|83|85|98)|w3c(\-| )|webc|whit|wi(g |nc|nw)|wmlb|wonu|x700|xda(\-|2|g)|yas\-|your|zeto|zte\-/i', substr($user_agent, 0, 4))) {
            $_SESSION['is_mobile'] = 1;
            return true;
        }
        $_SESSION['is_mobile'] = 2;
        return false;
    }
}

?>