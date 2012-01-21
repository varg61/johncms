<?php

/**
 * @package     JohnCMS
 * @link        http://johncms.com
 * @copyright   Copyright (C) 2008-2012 JohnCMS Community
 * @license     LICENSE.txt (see attached file)
 * @version     VERSION.txt (see attached file)
 * @author      http://johncms.com/about
 */

class Functions extends Vars
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
    public static function antiFlood()
    {
        $default = array(
            'mode' => 2,
            'day' => 10,
            'night' => 30,
            'dayfrom' => 10,
            'dayto' => 22
        );
        $af = isset(Vars::$SYSTEM_SET['antiflood']) ? unserialize(Vars::$SYSTEM_SET['antiflood']) : $default;
        switch ($af['mode']) {
            case 1:
                // Адаптивный режим
                $adm = mysql_result(mysql_query("SELECT COUNT(*) FROM `users` WHERE `rights` > 0 AND `lastdate` > " . (time() - 300)), 0);
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
                $c_time = date('G', time());
                $limit = $c_time > $af['day'] && $c_time < $af['night'] ? $af['day'] : $af['night'];
        }
        if (Vars::$USER_RIGHTS > 0)
            $limit = 4; // Для Администрации задаем лимит в 4 секунды
        $flood = Vars::$USER_DATA['lastpost'] + $limit - time();
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
    public static function antiLink($var)
    {
        $var = preg_replace('~\\[url=(https?://.+?)\\](.+?)\\[/url\\]|(https?://(www.)?[0-9a-z\.-]+\.[0-9a-z]{2,6}[0-9a-zA-Z/\?\.\~&amp;_=/%-:#]*)~', '###', $var);
        $replace = array(
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
        );
        return strtr($var, $replace);
    }

    /*
    -----------------------------------------------------------------
    Показ различных счетчиков внизу страницы
    -----------------------------------------------------------------
    */
    public static function displayCounters()
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
    Показываем дату с учетом сдвига времени
    -----------------------------------------------------------------
    */
    public static function displayDate($var)
    {
        $shift = (Vars::$SYSTEM_SET['timeshift'] + Vars::$USER_SET['timeshift']) * 3600;
        if (date('Y', $var) == date('Y', time())) {
            if (date('z', $var + $shift) == date('z', time() + $shift))
                return Vars::$LNG['today'] . ', ' . date("H:i", $var + $shift);
            if (date('z', $var + $shift) == date('z', time() + $shift) - 1)
                return Vars::$LNG['yesterday'] . ', ' . date("H:i", $var + $shift);
        }
        return date("d.m.Y / H:i", $var + $shift);
    }

    /*
    -----------------------------------------------------------------
    Сообщения об ошибках
    -----------------------------------------------------------------
    */
    public static function displayError($error = null, $link = null)
    {
        if (!empty($error)) {
            return '<div class="rmenu"><p><b>' . Vars::$LNG['error'] . '!</b><br />' .
                (is_array($error) ? implode('<br />', $error) : $error) . '</p>' .
                (!empty($link) ? '<p>' . $link . '</p>' : '') . '</div>';
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
    public static function displayMenu($val = array(), $delimiter = ' | ', $end_space = '')
    {
        return implode($delimiter, array_diff($val, array(''))) . $end_space;
    }

    /*
    -----------------------------------------------------------------
    Постраничная навигация
    -----------------------------------------------------------------
    За основу взята доработанная функция от форума SMF 2.x.x
    -----------------------------------------------------------------
    */
    public static function displayPagination($base_url, $start, $max_value, $num_per_page)
    {
        $neighbors = 2;
        if ($start >= $max_value)
            $start = max(0, (int)$max_value - (((int)$max_value % (int)$num_per_page) == 0 ? $num_per_page : ((int)$max_value % (int)$num_per_page)));
        else
            $start = max(0, (int)$start - ((int)$start % (int)$num_per_page));
        $base_link = '<a class="pagenav" href="' . strtr($base_url, array('%' => '%%')) . 'page=%d' . '">%s</a>';
        $out[] = $start == 0 ? '' : sprintf($base_link, $start / $num_per_page, '&lt;&lt;');
        if ($start > $num_per_page * $neighbors)
            $out[] = sprintf($base_link, 1, '1');
        if ($start > $num_per_page * ($neighbors + 1))
            $out[] = '<span style="font-weight: bold;">...</span>';
        for ($nCont = $neighbors; $nCont >= 1; $nCont--)
            if ($start >= $num_per_page * $nCont) {
                $tmpStart = $start - $num_per_page * $nCont;
                $out[] = sprintf($base_link, $tmpStart / $num_per_page + 1, $tmpStart / $num_per_page + 1);
            }
        $out[] = '<span class="currentpage"><b>' . ($start / $num_per_page + 1) . '</b></span>';
        $tmpMaxPages = (int)(($max_value - 1) / $num_per_page) * $num_per_page;
        for ($nCont = 1; $nCont <= $neighbors; $nCont++)
            if ($start + $num_per_page * $nCont <= $tmpMaxPages) {
                $tmpStart = $start + $num_per_page * $nCont;
                $out[] = sprintf($base_link, $tmpStart / $num_per_page + 1, $tmpStart / $num_per_page + 1);
            }
        if ($start + $num_per_page * ($neighbors + 1) < $tmpMaxPages)
            $out[] = '<span style="font-weight: bold;">...</span>';
        if ($start + $num_per_page * $neighbors < $tmpMaxPages)
            $out[] = sprintf($base_link, $tmpMaxPages / $num_per_page + 1, $tmpMaxPages / $num_per_page + 1);
        if ($start + $num_per_page < $max_value) {
            $display_page = ($start + $num_per_page) > $max_value ? $max_value : ($start / $num_per_page + 2);
            $out[] = sprintf($base_link, $display_page, '&gt;&gt;');
        }
        return implode(' ', $out);
    }

    /*
    -----------------------------------------------------------------
    Показываем местоположение пользователя
    -----------------------------------------------------------------
    */
    //TODO: Доработать!
    public static function displayPlace($user_id = '', $place = '')
    {

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
    public static function displayUser($user = false, $arg = false)
    {
        $out = false;

        if (!$user['id']) {
            $out = '<b>' . Vars::$LNG['guest'] . '</b>';
            if (!empty($user['nickname'])) {
                $out .= ': ' . $user['nickname'];
            }
            if (!empty($arg['header'])) {
                $out .= ' ' . $arg['header'];
            }
        } else {
            if (parent::$USER_SET['avatar']) {
                $out .= '<table cellpadding="0" cellspacing="0"><tr><td>';
                if (file_exists(ROOTPATH . 'files' . DIRECTORY_SEPARATOR . 'users' . DIRECTORY_SEPARATOR . 'avatar' . DIRECTORY_SEPARATOR . $user['id'] . '.png')) {
                    $out .= '<img src="' . parent::$HOME_URL . '/files/users/avatar/' . $user['id'] . '.png" width="32" height="32" alt="' . $user['nickname'] . '" />&#160;';
                } else {
                    $out .= self::getImage('empty.png') . '&#160;';
                }
                $out .= '</td><td>';
            }
            if ($user['sex']) {
                $out .= self::getImage('usr_' . ($user['sex'] == 'm' ? 'm' : 'w') . '.png', '', 'align="middle"');
            } else {
                $out = self::getImage('del.png', '', 'align="middle"');
            }
            $out .= '&#160;';
            $out .= !Vars::$USER_ID || Vars::$USER_ID == $user['id']
                ? '<b>' . $user['nickname'] . '</b>'
                : '<a href="' . parent::$HOME_URL . '/users/profile.php?user=' . $user['id'] . '"><b>' . $user['nickname'] . '</b></a>';
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
            $out .= (time() > $user['last_visit'] + 300 ? '<span class="red"> [Off]</span>' : '<span class="green"> [ON]</span>');
            if (!empty($arg['header'])) {
                $out .= ' ' . $arg['header'];
            }
            if (!isset($arg['stshide']) && !empty($user['status'])) {
                $out .= '<div class="status">' . self::getImage('label.png', '', 'align="middle"') . '&#160;' . $user['status'] . '</div>';
            }
            if (Vars::$USER_SET['avatar']) {
                $out .= '</td></tr></table>';
            }
        }
        if (isset($arg['body']))
            $out .= '<div>' . $arg['body'] . '</div>';
        $ipinf = !isset($arg['iphide']) && (Vars::$USER_RIGHTS || ($user['id'] && $user['id'] == Vars::$USER_ID)) ? 1 : 0;
        $lastvisit = time() > $user['last_visit'] + 300 && isset($arg['last_visit']) ? self::displayDate($user['last_visit']) : false;
        if ($ipinf || $lastvisit || isset($arg['sub']) && !empty($arg['sub']) || isset($arg['footer'])) {
            $out .= '<div class="sub">';
            if (isset($arg['sub'])) {
                $out .= '<div>' . $arg['sub'] . '</div>';
            }
            if ($lastvisit) {
                $out .= '<div><span class="gray">' . Vars::$LNG['last_visit'] . ':</span> ' . $lastvisit . '</div>';
            }
            $iphist = '';
            if ($ipinf) {
                $out .= '<div><span class="gray">' . Vars::$LNG['browser'] . ':</span> ' . $user['user_agent'] . '</div>' .
                    '<div><span class="gray">' . Vars::$LNG['ip_address'] . ':</span> ';
                $hist = Vars::$MOD == 'history' ? '&amp;mod=history' : '';
                $ip = long2ip($user['ip']);
                if (Vars::$USER_RIGHTS && isset($user['ip_via_proxy']) && $user['ip_via_proxy']) {
                    $out .= '<b class="red"><a href="' . Vars::$SYSTEM_SET['homeurl'] . '/' . Vars::$SYSTEM_SET['admp'] . '/index.php?act=search_ip&amp;ip=' . $ip . $hist . '">' . $ip . '</a></b>';
                    $out .= '&#160;[<a href="' . Vars::$SYSTEM_SET['homeurl'] . '/' . Vars::$SYSTEM_SET['admp'] . '/index.php?act=ip_whois&amp;ip=' . $ip . '">?</a>]';
                    $out .= ' / ';
                    $out .= '<a href="' . Vars::$SYSTEM_SET['homeurl'] . '/' . Vars::$SYSTEM_SET['admp'] . '/index.php?act=search_ip&amp;ip=' . long2ip($user['ip_via_proxy']) . $hist . '">' . long2ip($user['ip_via_proxy']) . '</a>';
                    $out .= '&#160;[<a href="' . Vars::$SYSTEM_SET['homeurl'] . '/' . Vars::$SYSTEM_SET['admp'] . '/index.php?act=ip_whois&amp;ip=' . long2ip($user['ip_via_proxy']) . '">?</a>]';
                } elseif (Vars::$USER_RIGHTS) {
                    $out .= '<a href="' . Vars::$SYSTEM_SET['homeurl'] . '/' . Vars::$SYSTEM_SET['admp'] . '/index.php?act=search_ip&amp;ip=' . $ip . $hist . '">' . $ip . '</a>';
                    $out .= '&#160;[<a href="' . Vars::$SYSTEM_SET['homeurl'] . '/' . Vars::$SYSTEM_SET['admp'] . '/ip_whois.php?ip=' . $ip . '">?</a>]';
                } else {
                    $out .= $ip . $iphist;
                }
                if (isset($arg['iphist'])) {
                    $iptotal = mysql_result(mysql_query("SELECT COUNT(*) FROM `cms_user_ip` WHERE `user_id` = '" . $user['id'] . "'"), 0);
                    $out .= '<div><span class="gray">' . Vars::$LNG['ip_history'] . ':</span> <a href="' . Vars::$SYSTEM_SET['homeurl'] . '/users/profile.php?act=ip&amp;user=' . $user['id'] . '">[' . $iptotal . ']</a></div>';
                }
                $out .= '</div>';
            }
            if (isset($arg['footer'])) {
                $out .= $arg['footer'];
            }
            $out .= '</div>';
        }
        return $out;
    }

    /*
    -----------------------------------------------------------------
    Скачка различных списков в виде файла
    -----------------------------------------------------------------
    */
    public static function downloadFile($str, $file)
    {
        ob_clean();
        ob_start();
        echo $str;
        header('Content-Description: File Transfer');
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename=' . $file);
        header('Content-Transfer-Encoding: binary');
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');
        header('Content-Length: ' . ob_get_length());
        flush();
        return true;
    }

    /*
    -----------------------------------------------------------------
    Форматирование имени файла
    -----------------------------------------------------------------
    */
    public static function format($name)
    {
        $f1 = strrpos($name, ".");
        $f2 = substr($name, $f1 + 1, 999);
        $fname = strtolower($f2);
        return $fname;
    }

    /*
    -----------------------------------------------------------------
    Загружаем изображение
    -----------------------------------------------------------------
    */
    public static function getImage($img = '', $alt = '', $style = '')
    {
        if (empty($img)) return false;
        if (file_exists(ROOTPATH . 'theme' . DIRECTORY_SEPARATOR . Vars::$USER_SET['skin'] . DIRECTORY_SEPARATOR . 'images' . DIRECTORY_SEPARATOR . $img)) {
            $file = parent::$HOME_URL . '/theme/' . Vars::$USER_SET['skin'] . '/images/' . $img;
        } elseif (file_exists(ROOTPATH . 'images' . DIRECTORY_SEPARATOR . 'system' . DIRECTORY_SEPARATOR . $img)) {
            $file = parent::$HOME_URL . '/images/system/' . $img;
        } else {
            return false;
        }
        $size = getimagesize($file);
        return '<img src="' . $file . '" width="' . $size[0] . '" height="' . $size[1] . '" alt="' . $alt . '" border="0" ' . $style . '/>';
    }

    /*
    -----------------------------------------------------------------
    Получаем данные пользователя
    -----------------------------------------------------------------
    */
    public static function getUser($id = false)
    {
        if ($id && $id != Vars::$USER_ID) {
            $req = mysql_query("SELECT * FROM `users` WHERE `id` = '$id'");
            if (mysql_num_rows($req)) {
                return mysql_fetch_assoc($req);
            } else {
                return false;
            }
        } else {
            return Vars::$USER_DATA;
        }
    }

    /*
    -----------------------------------------------------------------
    Обработка смайлов
    -----------------------------------------------------------------
    */
    public static function smileys($str, $adm = false)
    {
        global $rootpath;
        static $smileys_cache = array();
        if (empty($smileys_cache)) {
            $file = $rootpath . 'files/cache/smileys.dat';
            if (file_exists($file) && ($smileys = file_get_contents($file)) !== false) {
                $smileys_cache = unserialize($smileys);
                return strtr($str, ($adm ? array_merge($smileys_cache['usr'], $smileys_cache['adm']) : $smileys_cache['usr']));
            } else {
                return $str;
            }
        } else {
            return strtr($str, ($adm ? array_merge($smileys_cache['usr'], $smileys_cache['adm']) : $smileys_cache['usr']));
        }
    }

    /*
    -----------------------------------------------------------------
    Функция пересчета на дни, или часы
    -----------------------------------------------------------------
    */
    public static function timeCount($var)
    {
        global $lng;
        if ($var < 0) $var = 0;
        $day = ceil($var / 86400);
        if ($var > 345600) return $day . ' ' . $lng['timecount_days'];
        if ($var >= 172800) return $day . ' ' . $lng['timecount_days_r'];
        if ($var >= 86400) return '1 ' . $lng['timecount_day'];
        return date("G:i:s", mktime(0, 0, $var));
    }

    /*
    -----------------------------------------------------------------
    Транслитерация текста
    -----------------------------------------------------------------
    */
    public static function translit($str)
    {
        $replace = array(
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
        );
        return strtr($str, $replace);
    }
}