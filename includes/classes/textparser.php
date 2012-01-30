<?php

/**
 * @package     JohnCMS
 * @link        http://johncms.com
 * @copyright   Copyright (C) 2008-2012 JohnCMS Community
 * @license     LICENSE.txt (see attached file)
 * @version     VERSION.txt (see attached file)
 * @author      http://johncms.com/about
 */

class TextParser
{
    /*
    -----------------------------------------------------------------
    Обработка тэгов и ссылок
    -----------------------------------------------------------------
    */
    public static function tags($var)
    {
        $var = self::_highlightCode($var);                                     // Подсветка кода
        $var = self::highlightUrl($var);                                      // Обработка ссылок
        $var = self::_highlightBB($var);                                       // Обработка BBcode тэгов
        return $var;
    }

    /*
    -----------------------------------------------------------------
    Удаление bbCode из текста
    -----------------------------------------------------------------
    */
    public static function noTags($var = '')
    {
        $var = preg_replace('#\[color=(.+?)\](.+?)\[/color]#si', '$2', $var);
        $var = preg_replace('!\[bg=(#[0-9a-f]{3}|#[0-9a-f]{6}|[a-z\-]+)](.+?)\[/bg]!is', '$2', $var);
        $replace = array(
            '[small]' => '',
            '[/small]' => '',
            '[big]' => '',
            '[/big]' => '',
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
            '[quote]' => '',
            '[/quote]' => '',
            '[c]' => '',
            '[/c]' => '',
            '[*]' => '',
            '[/*]' => ''
        );
        return strtr($var, $replace);
    }

    /*
    -----------------------------------------------------------------
    Подсветка кода
    -----------------------------------------------------------------
    */
    private static function _highlightCode($var)
    {
        if (!function_exists('process_code')) {
            function process_code($php)
            {
                $php = strtr($php, array('<br />' => '', '\\' => 'slash_MOBICMS'));
                $php = html_entity_decode(trim($php), ENT_QUOTES, 'UTF-8');
                $php = substr($php, 0, 2) != "<?" ? "<?php\n" . $php . "\n?>" : $php;
                $php = highlight_string(stripslashes($php), true);
                $php = strtr($php, array('slash_MOBICMS' => '&#92;', ':' => '&#58;', '[' => '&#91;'));
                return '<div class="phpcode">' . $php . '</div>';
            }
        }
        return preg_replace(array('#\[php\](.+?)\[\/php\]#se'), array("''.process_code('$1').''"), str_replace("]\n", "]", $var));
    }

    /*
    -----------------------------------------------------------------
    Парсинг ссылок
    За основу взята доработанная функция от форума phpBB 3
    -----------------------------------------------------------------
    */
    public static function highlightUrl($text)
    {
        if (!function_exists('url_callback')) {
            function url_callback($type, $whitespace, $url, $relative_url)
            {
                $orig_url = $url;
                $orig_relative = $relative_url;
                $url = htmlspecialchars_decode($url);
                $relative_url = htmlspecialchars_decode($relative_url);
                $text = '';
                $chars = array('<', '>', '"');
                $split = false;
                foreach ($chars as $char) {
                    $next_split = strpos($url, $char);
                    if ($next_split !== false) {
                        $split = ($split !== false) ? min($split, $next_split) : $next_split;
                    }
                }
                if ($split !== false) {
                    $url = substr($url, 0, $split);
                    $relative_url = '';
                } else if ($relative_url) {
                    $split = false;
                    foreach ($chars as $char) {
                        $next_split = strpos($relative_url, $char);
                        if ($next_split !== false) {
                            $split = ($split !== false) ? min($split, $next_split) : $next_split;
                        }
                    }
                    if ($split !== false) {
                        $relative_url = substr($relative_url, 0, $split);
                    }
                }
                $last_char = ($relative_url) ? $relative_url[strlen($relative_url) - 1] : $url[strlen($url) - 1];
                switch ($last_char)
                {
                    case '.':
                    case '?':
                    case '!':
                    case ':':
                    case ',':
                        $append = $last_char;
                        if ($relative_url) $relative_url = substr($relative_url, 0, -1);
                        else $url = substr($url, 0, -1);
                        break;

                    default:
                        $append = '';
                        break;
                }
                $short_url = (mb_strlen($url) > 40) ? mb_substr($url, 0, 30) . ' ... ' . mb_substr($url, -5) : $url;
                switch ($type)
                {
                    case 1:
                        $relative_url = preg_replace('/[&?]sid=[0-9a-f]{32}$/', '', preg_replace('/([&?])sid=[0-9a-f]{32}&/', '$1', $relative_url));
                        $url = $url . '/' . $relative_url;
                        $text = $relative_url;
                        if (!$relative_url) {
                            return $whitespace . $orig_url . '/' . $orig_relative;
                        }
                        break;

                    case 2:
                        $text = $short_url;
                        if (!isset(Vars::$USER_SET['direct_url']) || !Vars::$USER_SET['direct_url']) {
                            $url = Vars::$HOME_URL . '/go.php?url=' . rawurlencode($url);
                        }
                        break;

                    case 3:
                        $url = 'http://' . $url;
                        $text = $short_url;
                        if (!isset(Vars::$USER_SET['direct_url']) || !Vars::$USER_SET['direct_url']) {
                            $url = Vars::$HOME_URL . '/go.php?url=' . rawurlencode($url);
                        }
                        break;

                    case 4:
                        $text = $short_url;
                        $url = 'mailto:' . $url;
                        break;
                }
                $url = htmlspecialchars($url);
                $text = htmlspecialchars($text);
                $append = htmlspecialchars($append);
                return $whitespace . '<a href="' . $url . '">' . $text . '</a>' . $append;
            }
        }

        static $url_match;
        static $url_replace;

        if (!is_array($url_match)) {
            $url_match = $url_replace = array();

            // Обработка внутренние ссылки
            $url_match[] = '#(^|[\n\t (>.])(' . preg_quote(Vars::$HOME_URL, '#') . ')/((?:[a-zа-яё0-9\-._~!$&\'(*+,;=:@|]+|%[\dA-F]{2})*(?:/(?:[a-zа-яё0-9\-._~!$&\'(*+,;=:@|]+|%[\dA-F]{2})*)*(?:\?(?:[a-zа-яё0-9\-._~!$&\'(*+,;=:@/?|]+|%[\dA-F]{2})*)?(?:\#(?:[a-zа-яё0-9\-._~!$&\'(*+,;=:@/?|]+|%[\dA-F]{2})*)?)#ieu';
            $url_replace[] = "url_callback(1, '\$1', '\$2', '\$3')";

            // Обработка обычных ссылок типа xxxx://aaaaa.bbb.cccc. ...
            $url_match[] = '#(^|[\n\t (>.])([a-z][a-z\d+]*:/{2}(?:(?:[a-zа-яё0-9\-._~!$&\'(*+,;=:@|]+|%[\dA-F]{2})+|[0-9.]+|\[[a-zа-яё0-9.]+:[a-zа-яё0-9.]+:[a-zа-яё0-9.:]+\])(?::\d*)?(?:/(?:[a-zа-яё0-9\-._~!$&\'(*+,;=:@|]+|%[\dA-F]{2})*)*(?:\?(?:[a-zа-яё0-9\-._~!$&\'(*+,;=:@/?|]+|%[\dA-F]{2})*)?(?:\#(?:[a-zа-яё0-9\-._~!$&\'(*+,;=:@/?|]+|%[\dA-F]{2})*)?)#ieu';
            $url_replace[] = "url_callback(2, '\$1', '\$2', '')";

            // Обработка сокращенных ссылок, без указания протокола "www.xxxx.yyyy[/zzzz]"
            $url_match[] = '#(^|[\n\t (>])(www\.(?:[a-zа-яё0-9\-._~!$&\'(*+,;=:@|]+|%[\dA-F]{2})+(?::\d*)?(?:/(?:[a-zа-яё0-9\-._~!$&\'(*+,;=:@|]+|%[\dA-F]{2})*)*(?:\?(?:[a-zа-яё0-9\-._~!$&\'(*+,;=:@/?|]+|%[\dA-F]{2})*)?(?:\#(?:[a-zа-яё0-9\-._~!$&\'(*+,;=:@/?|]+|%[\dA-F]{2})*)?)#ieu';
            $url_replace[] = "url_callback(3, '\$1', '\$2', '')";

            // Обработка адресов E-mail
            $url_match[] = '/(^|[\n\t (>])(([\w\!\#$\%\&\'\*\+\-\/\=\?\^\`{\|\}\~]+\.)*(?:[\w\!\#$\%\'\*\+\-\/\=\?\^\`{\|\}\~]|&amp;)+@((((([a-z0-9]{1}[a-z0-9\-]{0,62}[a-z0-9]{1})|[a-z])\.)+[a-z]{2,6})|(\d{1,3}\.){3}\d{1,3}(\:\d{1,5})?))/ie';
            $url_replace[] = "url_callback(4, '\$1', '\$2', '')";
        }
        return preg_replace($url_match, $url_replace, $text);
    }

    /*
    -----------------------------------------------------------------
    Обработка bbCode
    -----------------------------------------------------------------
    */
    private static function _highlightBB($var)
    {
        // Список поиска
        $search = array(
            '#\[b](.+?)\[/b]#is',                                              // Жирный
            '#\[i](.+?)\[/i]#is',                                              // Курсив
            '#\[u](.+?)\[/u]#is',                                              // Подчеркнутый
            '#\[s](.+?)\[/s]#is',                                              // Зачеркнутый
            '#\[small](.+?)\[/small]#is',                                      // Маленький шрифт
            '#\[big](.+?)\[/big]#is',                                          // Большой шрифт
            '#\[red](.+?)\[/red]#is',                                          // Красный
            '#\[green](.+?)\[/green]#is',                                      // Зеленый
            '#\[blue](.+?)\[/blue]#is',                                        // Синий
            '!\[color=(#[0-9a-f]{3}|#[0-9a-f]{6}|[a-z\-]+)](.+?)\[/color]!is', // Цвет шрифта
            '!\[bg=(#[0-9a-f]{3}|#[0-9a-f]{6}|[a-z\-]+)](.+?)\[/bg]!is',       // Цвет фона
            '#\[(q|c)](.+?)\[/(q|c)]#is',                                      // Цитата
            '#\[\*](.+?)\[/\*]#is',                                            // Список
            '#\[spoiler](.+?)\[/spoiler]#is'                                   // Спойлер
        );
        // Список замены
        $replace = array(
            '<span style="font-weight: bold">$1</span>',                       // Жирный
            '<span style="font-style:italic">$1</span>',                       // Курсив
            '<span style="text-decoration:underline">$1</span>',               // Подчеркнутый
            '<span style="text-decoration:line-through">$1</span>',            // Зачеркнутый
            '<span style="font-size:x-small">$1</span>',                       // Маленький шрифт
            '<span style="font-size:large">$1</span>',                         // Большой шрифт
            '<span style="color:red">$1</span>',                               // Красный
            '<span style="color:green">$1</span>',                             // Зеленый
            '<span style="color:blue">$1</span>',                              // Синий
            '<span style="color:$1">$2</span>',                                // Цвет шрифта
            '<span style="background-color:$1">$2</span>',                     // Цвет фона
            '<span class="quote" style="display:block">$2</span>',             // Цитата
            '<span class="bblist">$1</span>', // Список
            '<div><div class="hidetop" style="cursor:pointer;" onclick="var _n=this.parentNode.getElementsByTagName(\'div\')[1];if(_n.style.display==\'none\'){_n.style.display=\'\';}else{_n.style.display=\'none\';}">[+/-] ' .
            Vars::$LNG['spoiler'] . '</div><div class="hidemain" style="display:none">$1</div></div>' // Спойлер
        );
        return preg_replace($search, $replace, $var);
    }

    /*
    -----------------------------------------------------------------
    Панель кнопок bbCode (для компьютеров)
    -----------------------------------------------------------------
    */
    public static function autoBB($form, $field)
    {
        if (Vars::$IS_MOBILE) {
            return false;
        }
        $colors = array(
            'ffffff', 'bcbcbc', '708090', '6c6c6c', '454545',
            'fcc9c9', 'fe8c8c', 'fe5e5e', 'fd5b36', 'f82e00',
            'ffe1c6', 'ffc998', 'fcad66', 'ff9331', 'ff810f',
            'd8ffe0', '92f9a7', '34ff5d', 'b2fb82', '89f641',
            'b7e9ec', '56e5ed', '21cad3', '03939b', '039b80',
            'cac8e9', '9690ea', '6a60ec', '4866e7', '173bd3',
            'f3cafb', 'e287f4', 'c238dd', 'a476af', 'b53dd2'
        );
        $i = 1;
        $font_color = '<table><tr>';
        $bg_color = '<table><tr>';
        foreach ($colors as $value) {
            $font_color .= '<a href="javascript:tag(\'[color=#' . $value . ']\', \'[/color]\', \'\');" style="background-color:#' . $value . ';"></a>';
            $bg_color .= '<a href="javascript:tag(\'[bg=#' . $value . ']\', \'[/bg]\', \'\');" style="background-color:#' . $value . ';"></a>';
            if (!($i % sqrt(count($colors)))) {
                $font_color .= '</tr><tr>';
                $bg_color .= '</tr><tr>';
            }
            ++$i;
        }
        $font_color .= '</tr></table>';
        $bg_color .= '</tr></table>';
        if (($smileys = Vars::getUserData('smileys')) === false) $smileys = array();
        if (!empty($smileys)) {
            $res_sm = '';
            $bb_smileys = '<small><a href="' . Vars::$HOME_URL . '/pages/smileys.php?act=my_smileys">' . Vars::$LNG['edit_list'] . '</a></small><br />';
            foreach ($smileys as $value)
                $res_sm .= '<a href="javascript:tag(\'' . $value . '\', \'\', \':\');">:' . $value . ':</a> ';
            $bb_smileys .= Functions::smileys($res_sm, Vars::$USER_DATA['rights'] >= 1 ? 1 : 0);
        } else {
            $bb_smileys = '<small><a href="' . Vars::$HOME_URL . '/pages/smileys.php">' . Vars::$LNG['add_smileys'] . '</a></small>';
        }
        $out = '<style>' . "\n" .
               '.bb_hide{background-color: rgba(178,178,178,0.5); padding: 5px; border-radius: 3px; border: 1px solid #708090; display: none; overflow: auto; max-width: 300px; max-height: 150px; position: absolute;}' . "\n" .
               '.bb_opt:hover .bb_hide{display: block;}' . "\n" .
               '.bb_color a {float:left;  width:9px; height:9px; margin:1px; border: 1px solid black;}' . "\n" .
               '</style>' . "\n" .
               '<script language="JavaScript" type="text/javascript">' . "\n" .
               'function tag(text1, text2, text3) {' . "\n" .
               'if ((document.selection)) {' . "\n" .
               'document.' . $form . '.' . $field . '.focus();' . "\n" .
               'document.' . $form . '.document.selection.createRange().text = text3+text1+document.' . $form . '.document.selection.createRange().text+text2+text3;' . "\n" .
               '} else if(document.forms[\'' . $form . '\'].elements[\'' . $field . '\'].selectionStart!=undefined) {' . "\n" .
               'var element = document.forms[\'' . $form . '\'].elements[\'' . $field . '\'];' . "\n" .
               'var str = element.value;' . "\n" .
               'var start = element.selectionStart;' . "\n" .
               'var length = element.selectionEnd - element.selectionStart;' . "\n" .
               'element.value = str.substr(0, start) + text3 + text1 + str.substr(start, length) + text2 + text3 + str.substr(start + length);' . "\n" .
               '} else document.' . $form . '.' . $field . '.value += text3+text1+text2+text3;}</script>' . "\n" .
               '<a href="javascript:tag(\'[b]\', \'[/b]\', \'\')">' . Functions::getImage('bb_bold.gif', 'b', 'title="' . Vars::$LNG['tag_bold'] . '"') . '</a>' . "\n" .
               '<a href="javascript:tag(\'[i]\', \'[/i]\', \'\')">' . Functions::getImage('bb_italic.gif', 'i', 'title="' . Vars::$LNG['tag_italic'] . '"') . '</a>' . "\n" .
               '<a href="javascript:tag(\'[u]\', \'[/u]\', \'\')">' . Functions::getImage('bb_underline.gif', 'u', 'title="' . Vars::$LNG['tag_underline'] . '"') . '</a>' . "\n" .
               '<a href="javascript:tag(\'[s]\', \'[/s]\', \'\')">' . Functions::getImage('bb_strike.gif', 's', 'title="' . Vars::$LNG['tag_strike'] . '"') . '</a>' . "\n" .
               '<a href="javascript:tag(\'[*]\', \'[/*]\', \'\')">' . Functions::getImage('bb_list.gif', '*', 'title="' . Vars::$LNG['tag_list'] . '"') . '</a>' . "\n" .
               '<a href="javascript:tag(\'[spoiler]\', \'[/spoiler]\', \'\')">' . Functions::getImage('bb_spoiler.png', 'spoiler', 'title="' . Vars::$LNG['spoiler'] . '"') . '</a>' . "\n" .
               '<a href="javascript:tag(\'[q]\', \'[/q]\', \'\')">' . Functions::getImage('bb_quote.gif', 'q', 'title="' . Vars::$LNG['tag_quote'] . '"') . '</a>' . "\n" .
               '<a href="javascript:tag(\'[php]\', \'[/php]\', \'\')">' . Functions::getImage('bb_php.gif', 'php', 'title="' . Vars::$LNG['tag_code'] . '"') . '</a>' . "\n" .
               '<a href="javascript:tag(\'[url=]\', \'[/url]\', \'\')">' . Functions::getImage('bb_url.gif', 'url', 'title="' . Vars::$LNG['tag_link'] . '"') . '</a>' . "\n" .
               '<span class="bb_opt" style="display: inline-block; cursor:pointer">' . "\n" .
               Functions::getImage('bb_color.gif', 'color', 'title="' . Vars::$LNG['color_text'] . '"') . "\n" .
               '<div class="bb_hide bb_color">' . $font_color . '</div></span>' . "\n" .
               '<span class="bb_opt" style="display: inline-block; cursor:pointer">' . "\n" .
               Functions::getImage('bb_bgcolor.gif', 'bgcolor', 'title="' . Vars::$LNG['color_bg'] . '"') . "\n" .
               '<div class="bb_hide bb_color">' . $bg_color . '</div></span>';
        if (Vars::$USER_ID) {
            $out .= ' <span class="bb_opt" style="display: inline-block; cursor:pointer">' . "\n" .
                    Functions::getImage('bb_smileys.gif', 'smileys', 'title="' . Vars::$LNG['smileys'] . '"') . "\n" .
                    '<div class="bb_hide">' . $bb_smileys . '</div></span>';
        }
        return $out . '<br />';
    }
}