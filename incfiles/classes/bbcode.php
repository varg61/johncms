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

class bbcode
{
    /*
    -----------------------------------------------------------------
    Обработка тэгов и ссылок
    -----------------------------------------------------------------
    */
    public static function tags($var)
    {
        $var = self::highlight_code($var);                                     // Подсветка кода
        $var = self::highlight_url($var);                                      // Обработка ссылок
        $var = self::highlight_bb($var);                                       // Обработка ссылок
        return $var;
    }

    /*
    -----------------------------------------------------------------
    Удаление bbCode из текста
    -----------------------------------------------------------------
    */
    static function notags($var = '')
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
    private static function highlight_code($var)
    {
        if (!function_exists('process_code')) {
            function process_code($php)
            {
                $php = strtr($php, array('<br />' => '', '\\' => 'slash_JOHNCMS'));
                $php = html_entity_decode(trim($php), ENT_QUOTES, 'UTF-8');
                $php = substr($php, 0, 2) != "<?" ? "<?php\n" . $php . "\n?>" : $php;
                $php = highlight_string(stripslashes($php), true);
                $php = strtr($php, array('slash_JOHNCMS' => '&#92;', ':' => '&#58;', '[' => '&#91;'));
                return '<div class="phpcode">' . $php . '</div>';
            }
        }
        return preg_replace(array('#\[php\](.+?)\[\/php\]#se'), array("''.process_code('$1').''"), str_replace("]\n", "]", $var));
    }

    /*
    -----------------------------------------------------------------
    Обработка URL
    -----------------------------------------------------------------
    */
    private static function highlight_url($var)
    {
        if (!function_exists('process_url')) {
            function process_url($url)
            {
                if (!isset($url[3])) {
                    $tmp = parse_url($url[1]);
                    if ('http://' . $tmp['host'] == core::$system_set['homeurl'] || isset(core::$user_set['direct_url']) && core::$user_set['direct_url']) {
                        return '<a href="' . $url[1] . '">' . $url[2] . '</a>';
                    } else {
                        return '<a href="' . core::$system_set['homeurl'] . '/go.php?url=' . base64_encode($url[1]) . '">' . $url[2] . '</a>';
                    }
                } else {
                    $tmp = parse_url($url[3]);
                    $url[3] = str_replace(':', '&#58;', $url[3]);
                    if ('http://' . $tmp['host'] == core::$system_set['homeurl'] || isset(core::$user_set['direct_url']) && core::$user_set['direct_url']) {
                        return '<a href="' . $url[3] . '">' . $url[3] . '</a>';
                    } else {
                        return '<a href="' . core::$system_set['homeurl'] . '/go.php?url=' . base64_encode($url[3]) . '">' . $url[3] . '</a>';
                    }
                }
            }
        }
        return preg_replace_callback('~\\[url=(https?://.+?)\\](.+?)\\[/url\\]|(https?://(www.)?[0-9a-z\.-]+\.[0-9a-z]{2,6}[0-9a-zA-Z/\?\.\~&amp;_=/%-:#]*)~', 'process_url', $var);
    }

    /*
    -----------------------------------------------------------------
    Обработка bbCode
    -----------------------------------------------------------------
    */
    private static function highlight_bb($var)
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
            core::$lng['spoiler'] . '</div><div class="hidemain" style="display:none">$1</div></div>' // Спойлер
        );
        return preg_replace($search, $replace, $var);
    }

    /*
    -----------------------------------------------------------------
    Панель кнопок bbCode (для компьютеров)
    -----------------------------------------------------------------
    */
    public static function auto_bb($form, $field)
    {
        if (core::$is_mobile) {
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
        if (($smileys = registry::user_data_get('smileys')) === false) $smileys = array();
        if (!empty($smileys)) {
            $res_sm = '';
            $bb_smileys = '<small><a href="' . core::$system_set['homeurl'] . '/pages/faq.php?act=my_smileys">' . core::$lng['edit_list'] . '</a></small><br />';
            foreach ($smileys as $value)
                $res_sm .= '<a href="javascript:tag(\'' . $value . '\', \'\', \':\');">:' . $value . ':</a> ';
            $bb_smileys .= functions::smileys($res_sm, core::$user_data['rights'] >= 1 ? 1 : 0);
        } else {
            $bb_smileys = '<small><a href="' . core::$system_set['homeurl'] . '/pages/faq.php?act=smileys">' . core::$lng['add_smileys'] . '</a></small>';
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
               '<a href="javascript:tag(\'[b]\', \'[/b]\', \'\')">' . functions::get_image('bb_bold.gif', 'b', 'title="' . core::$lng['tag_bold'] . '"') . '</a>' . "\n" .
               '<a href="javascript:tag(\'[i]\', \'[/i]\', \'\')">' . functions::get_image('bb_italic.gif', 'i', 'title="' . core::$lng['tag_italic'] . '"') . '</a>' . "\n" .
               '<a href="javascript:tag(\'[u]\', \'[/u]\', \'\')">' . functions::get_image('bb_underline.gif', 'u', 'title="' . core::$lng['tag_underline'] . '"') . '</a>' . "\n" .
               '<a href="javascript:tag(\'[s]\', \'[/s]\', \'\')">' . functions::get_image('bb_strike.gif', 's', 'title="' . core::$lng['tag_strike'] . '"') . '</a>' . "\n" .
               '<a href="javascript:tag(\'[*]\', \'[/*]\', \'\')">' . functions::get_image('bb_list.gif', '*', 'title="' . core::$lng['tag_list'] . '"') . '</a>' . "\n" .
               '<a href="javascript:tag(\'[spoiler]\', \'[/spoiler]\', \'\')">' . functions::get_image('bb_spoiler.png', 'spoiler', 'title="' . core::$lng['spoiler'] . '"') . '</a>' . "\n" .
               '<a href="javascript:tag(\'[q]\', \'[/q]\', \'\')">' . functions::get_image('bb_quote.gif', 'q', 'title="' . core::$lng['tag_quote'] . '"') . '</a>' . "\n" .
               '<a href="javascript:tag(\'[php]\', \'[/php]\', \'\')">' . functions::get_image('bb_php.gif', 'php', 'title="' . core::$lng['tag_code'] . '"') . '</a>' . "\n" .
               '<a href="javascript:tag(\'[url=]\', \'[/url]\', \'\')">' . functions::get_image('bb_url.gif', 'url', 'title="' . core::$lng['tag_link'] . '"') . '</a>' . "\n" .
               '<span class="bb_opt" style="display: inline-block; cursor:pointer">' . "\n" .
               functions::get_image('bb_color.gif', 'color', 'title="' . core::$lng['color_text'] . '"') . "\n" .
               '<div class="bb_hide bb_color">' . $font_color . '</div></span>' . "\n" .
               '<span class="bb_opt" style="display: inline-block; cursor:pointer">' . "\n" .
               functions::get_image('bb_bgcolor.gif', 'bgcolor', 'title="' . core::$lng['color_bg'] . '"') . "\n" .
               '<div class="bb_hide bb_color">' . $bg_color . '</div></span>';
        if (core::$user_id) {
            $out .= ' <span class="bb_opt" style="display: inline-block; cursor:pointer">' . "\n" .
                    functions::get_image('bb_smileys.gif', 'smileys', 'title="' . core::$lng['smileys'] . '"') . "\n" .
                    '<div class="bb_hide">' . $bb_smileys . '</div></span>';
        }
        return $out . '<br />';
    }
}