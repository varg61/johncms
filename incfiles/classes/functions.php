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

class functions extends core
{
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
    public static function antiflood()
    {
        global $realtime;
        $default = array(
            'mode' => 2,
            'day' => 10,
            'night' => 30,
            'dayfrom' => 10,
            'dayto' => 22
        );
        $af = isset(self::$system_set['antiflood']) ? unserialize(self::$system_set['antiflood']) : $default;
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
        if (self::$user_rights > 0)
            $limit = 4; // Для Администрации задаем лимит в 4 секунды
        $flood = self::$user_data['lastpost'] + $limit - $realtime;
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
    public static function antilink($var)
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
    Проверка переменных
    -----------------------------------------------------------------
    */
    public static function check($str)
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
    public static function checkout($str, $br = 0, $tags = 0)
    {
        $str = htmlentities(trim($str), ENT_QUOTES, 'UTF-8');
        if ($br == 1)
            $str = nl2br($str);
        elseif ($br == 2)
            $str = str_replace("\r\n", ' ', $str);
        //TODO: Передеать на новую функцию подсветки Тэгов
        if ($tags == 1)
            $str = bbcode::tags($str);
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
    public static function display_counters()
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
    public static function display_error($error = '', $link = '')
    {
        if ($error) {
            $out = '<div class="rmenu"><p><b>' . self::$lng['error'] . '!</b><br />';
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
    public static function display_menu($val = array(), $delimiter = ' | ', $end_space = '')
    {
        return implode($delimiter, array_diff($val, array(''))) . $end_space;
    }

    /*
    -----------------------------------------------------------------
    Постраничная навигация
    За основу взята аналогичная функция от форума SMF2.0
    -----------------------------------------------------------------
    */
    public static function display_pagination($base_url, $start, $max_value, $num_per_page)
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
        global $realtime, $rootpath;
        $out = false;

        if (!$user['id']) {
            $out = '<b>' . self::$lng['guest'] . '</b>';
            if (!empty($user['name']))
                $out .= ': ' . $user['name'];
            if (!empty($arg['header']))
                $out .= ' ' . $arg['header'];
        } else {
            if (self::$user_set['avatar']) {
                $out .= '<table cellpadding="0" cellspacing="0"><tr><td>';
                if (file_exists(($rootpath . 'files/users/avatar/' . $user['id'] . '.png')))
                    $out .= '<img src="' . self::$system_set['homeurl'] . '/files/users/avatar/' . $user['id'] . '.png" width="32" height="32" alt="" />&#160;';
                else
                    $out .= '<img src="' . self::$system_set['homeurl'] . '/images/empty.png" width="32" height="32" alt="" />&#160;';
                $out .= '</td><td>';
            }
            if ($user['sex'])
                $out .= '<img src="' . self::$system_set['homeurl'] . '/theme/' . self::$user_set['skin'] . '/images/' . ($user['sex'] == 'm' ? 'm' : 'w') . ($user['datereg'] > $realtime - 86400 ? '_new' : '')
                        . '.png" width="16" height="16" align="middle" alt="' . ($user['sex'] == 'm' ? 'М' : 'Ж') . '" />&#160;';
            else
                $out .= '<img src="' . self::$system_set['homeurl'] . '/images/del.png" width="12" height="12" align="middle" />&#160;';
            $out .= !self::$user_id || self::$user_id == $user['id'] ? '<b>' . $user['name'] . '</b>' : '<a href="' . self::$system_set['homeurl'] . '/users/profile.php?user=' . $user['id'] . '"><b>' . $user['name'] . '</b></a>';
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
                $out .= '<div class="status"><img src="' . self::$system_set['homeurl'] . '/theme/' . self::$user_set['skin'] . '/images/label.png" alt="" align="middle" />&#160;' . $user['status'] . '</div>';
            if (self::$user_set['avatar'])
                $out .= '</td></tr></table>';
        }
        if (isset($arg['body']))
            $out .= '<div>' . $arg['body'] . '</div>';
        $ipinf = self::$user_rights || ($user['id'] && $user['id'] == self::$user_id) && (!isset($arg['iphide']) || isset($arg['iphide']) && !$arg['iphide']) ? 1 : 0;
        $lastvisit = $realtime > $user['lastdate'] + 300 && isset($arg['lastvisit']) ? date("d.m.Y (H:i)", $user['lastdate']) : false;
        if ($ipinf || $lastvisit || isset($arg['sub']) || isset($arg['footer'])) {
            $out .= '<div class="sub">';
            if (isset($arg['sub']))
                $out .= '<div>' . $arg['sub'] . '</div>';
            if ($lastvisit)
                $out .= '<div><span class="gray">' . self::$lng['last_visit'] . ':</span> ' . $lastvisit . '</div>';
            $iphist = '';
            if ($ipinf) {
                $out .= '<div><span class="gray">' . self::$lng['browser'] . ':</span> ' . $user['browser'] . '</div>';
                $out .= '<div><span class="gray">' . self::$lng['ip_address'] . ':</span> ';
                if (self::$user_rights && isset($user['ip_via_proxy']) && $user['ip_via_proxy']) {
                    $out .= '<b class="red"><a href="' . self::$system_set['homeurl'] . '/' . self::$system_set['admp'] . '/index.php?act=search_ip&amp;ip=' . $user['ip'] . '">' . long2ip($user['ip']) . '</a></b> / ';
                    $out .= '<a href="' . self::$system_set['homeurl'] . '/' . self::$system_set['admp'] . '/index.php?act=search_ip&amp;ip=' . $user['ip_via_proxy'] . '">' . long2ip($user['ip_via_proxy']) . '</a>';
                } elseif (self::$user_rights) {
                    $out .= '<a href="' . self::$system_set['homeurl'] . '/' . self::$system_set['admp'] . '/index.php?act=search_ip&amp;ip=' . $user['ip'] . '">' . long2ip($user['ip']) . '</a>';
                } else {
                    $out .= long2ip($user['ip']) . $iphist;
                }
                if (isset($arg['iphist'])) {
                    $iptotal = mysql_result(mysql_query("SELECT COUNT(*) FROM `cms_users_iphistory` WHERE `user_id` = '" . $user['id'] . "'"), 0);
                    $out .= '<div><span class="gray">' . self::$lng['ip_history'] . ':</span> <a href="' . self::$system_set['homeurl'] . '/users/profile.php?act=ip&amp;user=' . $user['id'] . '">[' . $iptotal . ']</a></div>';
                }
                $out .= '</div>';
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
    Получаем данные пользователя
    -----------------------------------------------------------------
    */
    static function get_user($id = false)
    {
        if ($id && $id != self::$user_id) {
            $req = mysql_query("SELECT * FROM `users` WHERE `id` = '$id'");
            if (mysql_num_rows($req)) {
                return mysql_fetch_assoc($req);
            } else {
                return false;
            }
        } else {
            return self::$user_data;
        }
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
    static private $smileys_cache = array();

    static function smileys($str, $adm = false)
    {
        global $rootpath;
        if (empty(self::$smileys_cache)) {
            $file = $rootpath . 'files/cache/smileys.dat';
            if (file_exists($file) && ($smileys = file_get_contents($file)) !== false) {
                self::$smileys_cache = unserialize($smileys);
                return strtr($str, ($adm ? array_merge(self::$smileys_cache['usr'], self::$smileys_cache['adm']) : self::$smileys_cache['usr']));
            } else {
                return $str;
            }
        } else {
            return strtr($str, ($adm ? array_merge(self::$smileys_cache['usr'], self::$smileys_cache['adm']) : self::$smileys_cache['usr']));
        }
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
}

?>