<?php

/*
////////////////////////////////////////////////////////////////////////////////
// JohnCMS                             Content Management System              //
// Официальный сайт сайт проекта:      http://johncms.com                     //
// Дополнительный сайт поддержки:      http://gazenwagen.com                  //
////////////////////////////////////////////////////////////////////////////////
// JohnCMS core team:                                                         //
// Евгений Рябинин aka john77          john77@gazenwagen.com                  //
// Олег Касьянов aka AlkatraZ          alkatraz@gazenwagen.com                //
//                                                                            //
// Информацию о версиях смотрите в прилагаемом файле version.txt              //
////////////////////////////////////////////////////////////////////////////////
*/

defined('_IN_JOHNCMS') or die('Error:restricted access');

////////////////////////////////////////////////////////////
// Показ различных счетчиков внизу страницы               //
////////////////////////////////////////////////////////////
function counters()
{
    global $headmod;
    $req = mysql_query("SELECT * FROM `cms_counters` WHERE `switch` = '1' ORDER BY `sort` ASC");
    if (mysql_num_rows($req) > 0)
    {
        while ($res = mysql_fetch_array($req))
        {
            $link1 = ($res['mode'] == 1 || $res['mode'] == 2) ? $res['link1'] : $res['link2'];
            $link2 = $res['mode'] == 2 ? $res['link1'] : $res['link2'];
            $count = ($headmod == 'mainpage') ? $link1 : $link2;
            if (!empty($count))
                echo $count;
        }
    }
}

////////////////////////////////////////////////////////////
// Счетчик посетителей онлайн                             //
////////////////////////////////////////////////////////////
function usersonline()
{
    global $realtime;
    global $user_id;
    global $home;
    $ontime = $realtime - 300;
    $qon = mysql_query("SELECT COUNT(*) FROM `users` WHERE `lastdate`>='" . $ontime . "'");
    $qon2 = mysql_result($qon, 0);
    $all = mysql_query("SELECT `id` FROM `count` WHERE `time`>='" . $ontime . "' GROUP BY `ip`, `browser`");
    $all2 = mysql_num_rows($all);
    return ($user_id ? '<a href="' . $home . '/str/online.php">Онлайн: ' . $qon2 . ' / ' . $all2 . '</a>' : 'Онлайн: ' . $qon2 . ' / ' . $all2);
}

////////////////////////////////////////////////////////////
// Вывод коэффициента сжатия Zlib                         //
////////////////////////////////////////////////////////////
function zipcount()
{
    global $set;
    if ($set['gzip'])
    {
        $Contents = ob_get_contents();
        $gzib_file = strlen($Contents);
        $gzib_file_out = strlen(gzcompress($Contents, 9));
        $gzib_pro = round(100 - (100 / ($gzib_file / $gzib_file_out)), 1);
        echo '<div>Cжатие вкл. (' . $gzib_pro . '%)</div>';
    } else
    {
        echo '<div>Cжатие выкл.</div>';
    }
}

////////////////////////////////////////////////////////////
// Счетсик времени, проведенного на сайте                 //
////////////////////////////////////////////////////////////
function timeonline()
{
    global $realtime;
    global $datauser;
    global $user_id;
    if ($user_id)
        echo '<div>В онлайне: ' . gmdate('H:i:s', ($realtime - $datauser['sestime'])) . '</div>';
}

////////////////////////////////////////////////////////////
// Подсчет колличества переходов по сайту                 //
////////////////////////////////////////////////////////////
function movements()
{
    global $datauser;
    global $user_id;
    global $login;
    if ($user_id)
    {
        $req = mysql_query("SELECT COUNT(*) FROM `count` WHERE `time` > '" . $datauser['sestime'] . "' AND `name` = '" . $login . "'");
        $count = mysql_result($req, 0);
        echo '<div>Переходов: ' . $count . '</div>';
    }
}

////////////////////////////////////////////////////////////
// Счетчик непрочитанных тем на форуме                    //
////////////////////////////////////////////////////////////
function forum_new()
{
    global $user_id;
    global $realtime;
    global $dostadm;
    if ($user_id)
    {
        if ($dostadm)
        {
            $req = mysql_query("SELECT COUNT(*) FROM `forum`
			LEFT JOIN `cms_forum_rdm` ON `forum`.`id` = `cms_forum_rdm`.`topic_id` AND `cms_forum_rdm`.`user_id` = '" . $user_id . "'
			WHERE `forum`.`type`='t'
			AND (`cms_forum_rdm`.`topic_id` Is Null
			OR `forum`.`time` > `cms_forum_rdm`.`time`)");
            return mysql_result($req, 0);
        } else
        {
            $req = mysql_query("SELECT COUNT(*) FROM `forum`
			LEFT JOIN `cms_forum_rdm` ON `forum`.`id` = `cms_forum_rdm`.`topic_id` AND `cms_forum_rdm`.`user_id` = '" . $user_id . "'
			WHERE `forum`.`type`='t'
			AND `close`!='1'
			AND (`cms_forum_rdm`.`topic_id` Is Null
			OR `forum`.`time` > `cms_forum_rdm`.`time`)");
            return mysql_result($req, 0);
        }
    } else
    {
        return false;
    }
}

////////////////////////////////////////////////////////////
// Дата последней новости                                 //
////////////////////////////////////////////////////////////
function dnews()
{
    if (!empty($_SESSION['uid']))
    {
        global $sdvig;
    } else
    {
        global $sdvigclock;
        $sdvig = $sdvigclock;
    }
    $req = mysql_query("SELECT `time` FROM `news` ORDER BY `time` DESC LIMIT 1");
    $res = mysql_fetch_array($req);
    $vrn = $res['time'] + $sdvig * 3600;
    $vrn1 = date("H:i/d.m.y", $vrn);
    return $vrn1;
}

////////////////////////////////////////////////////////////
// Колличество зарегистрированных пользователей           //
////////////////////////////////////////////////////////////
function kuser()
{
    global $realtime;
    $total = mysql_result(mysql_query("SELECT COUNT(*) FROM `users`"), 0);
    $res = mysql_result(mysql_query("SELECT COUNT(*) FROM `users` WHERE `datereg` > '" . ($realtime - 86400) . "'"), 0);
    if ($res > 0)
        $total .= '&nbsp;<span class="red">+' . $res . '</span>';
    return $total;
}

////////////////////////////////////////////////////////////
// Счетчик "Кто в форуме?"                                //
////////////////////////////////////////////////////////////
function wfrm($id = '')
{
    //TODO: Переделать
    global $realtime;
    $onltime = $realtime - 300;
    $count = 0;
    $qf = mysql_query("select * from `users` where  lastdate > '" . $onltime . "'");
    while ($arrf = mysql_fetch_array($qf))
    {
        $whf = mysql_query("select * from `count` where name='" . $arrf['name'] . "' order by time desc ;");
        while ($whf1 = mysql_fetch_array($whf))
        {
            $whf2[] = $whf1['where'];
        }
        $wherf = $whf2[0];
        $whf2 = array();
        $wherf1 = explode(",", $wherf);
        if (empty($id))
        {
            if ($wherf1[0] == "forum")
            {
                $count = $count + 1;
            }
        } else
        {
            if ($wherf == "forum,$id")
            {
                $count = $count + 1;
            }
        }
    }
    return $count;
}

////////////////////////////////////////////////////////////
// Статистика загрузок                                    //
////////////////////////////////////////////////////////////
function dload()
{
    global $realtime;
    $total = mysql_result(mysql_query("SELECT COUNT(*) FROM `download` WHERE `type` = 'file'"), 0);
    $old = $realtime - (3 * 24 * 3600);
    $new = mysql_result(mysql_query("SELECT COUNT(*) FROM `download` WHERE `time` > '" . $old . "' AND `type` = 'file'"), 0);
    if ($new > 0)
        $total .= '/<span class="red">+' . $new . '</span>';
    return $total;
}

////////////////////////////////////////////////////////////
// Статистика галлереи                                    //
////////////////////////////////////////////////////////////
// Если вызвать с параметром 1, будет выдавать только колличество новых картинок
function fgal($mod = 0)
{
    global $realtime;
    $old = $realtime - (3 * 24 * 3600);
    $new = mysql_result(mysql_query("SELECT COUNT(*) FROM `gallery` WHERE `time` > '" . $old . "' AND `type` = 'ft'"), 0);
    mysql_free_result($req);
    if ($mod == 0)
    {
        $total = mysql_result(mysql_query("SELECT COUNT(*) FROM `gallery` WHERE `type` = 'ft'"), 0);
        $out = $total;
        if ($new > 0)
            $out .= '/<span class="red">+' . $new . '</span>';
    } else
    {
        $out = $new;
    }
    return $out;
}

////////////////////////////////////////////////////////////
// Дни рождения                                           //
////////////////////////////////////////////////////////////
function brth()
{
    global $realtime;
    $mon = date("m", $realtime);
    if (substr($mon, 0, 1) == 0)
    {
        $mon = str_replace("0", "", $mon);
    }
    $day = date("d", $realtime);
    if (substr($day, 0, 1) == 0)
    {
        $day = str_replace("0", "", $day);
    }
    $count = mysql_result(mysql_query("SELECT COUNT(*) FROM `users` WHERE `dayb` = '" . $day . "' AND `monthb` = '" . $mon . "' AND `preg` = '1'"), 0);
    return $count;
}

////////////////////////////////////////////////////////////
// Статистика библиотеки                                  //
////////////////////////////////////////////////////////////
function stlib()
{
    global $realtime;
    global $dostlmod;
    $countf = mysql_result(mysql_query("SELECT COUNT(*) FROM `lib` WHERE `type` = 'bk' AND `moder` = '1'"), 0);
    $old = $realtime - (3 * 24 * 3600);
    $countf1 = mysql_result(mysql_query("SELECT COUNT(*) FROM `lib` WHERE `time` > '" . $old . "' AND `type` = 'bk' AND `moder` = '1'"), 0);
    $out = $countf;
    if ($countf1 > 0)
        $out = $out . '/<span class="red">+' . $countf1 . '</span>';
    $countm = mysql_result(mysql_query("SELECT COUNT(*) FROM `lib` WHERE `type` = 'bk' AND `moder` = '0'"), 0);
    if ($dostlmod == '1' && ($countm > 0))
        $out = $out . "/<a href='" . $home . "/library/index.php?act=moder'><font color='#FF0000'> Мод:$countm</font></a>";
    return $out;
}

////////////////////////////////////////////////////////////
// Статистика Чата                                        //
////////////////////////////////////////////////////////////
// Если вызвать с параметром 0,1 то покажет общее число юзеров в Чате
function wch($id = false, $mod = false)
{
    global $realtime;
    $onltime = $realtime - 60;
    if ($mod)
    {
        $where = $id ? 'chat,' . $id : 'chat';
        $res = mysql_query("SELECT `id` FROM `count` WHERE
		`time` > '" . $onltime . "' AND
		`where` LIKE 'chat%'
		GROUP BY `name`");
    } else
    {
        $where = $id ? 'chat,' . $id : 'chat';
        $res = mysql_query("SELECT `id` FROM `count` WHERE
		`time` > '" . $onltime . "' AND
		`where` = '" . $where . "'
		GROUP BY `name`");
    }
    $count = mysql_num_rows($res);
    return $count;
}

////////////////////////////////////////////////////////////
// Статистика гостевой                                    //
////////////////////////////////////////////////////////////
// Если вызвать с параметром 1, то будет выдавать колличество новых в гостевой
// Если вызвать с параметром 2, то будет выдавать колличество новых в Админ-Клубе
function gbook($mod = 0)
{
    global $realtime;
    global $dostmod;
    switch ($mod)
    {
        case 1:
            $count = mysql_result(mysql_query("SELECT COUNT(*) FROM `guest` WHERE `adm`='0' AND `time` > '" . ($realtime - 86400) . "'"), 0);
            break;

        case 2:
            if ($dostmod == 1)
                $count = mysql_result(mysql_query("SELECT COUNT(*) FROM `guest` WHERE `adm`='1' AND `time` > '" . ($realtime - 86400) . "'"), 0);
            break;

        default:
            $count = mysql_result(mysql_query("SELECT COUNT(*) FROM `guest` WHERE `adm`='0' AND `time` > '" . ($realtime - 86400) . "'"), 0);
            if ($dostmod == 1)
            {
                $req = mysql_query("SELECT COUNT(*) FROM `guest` WHERE `adm`='1' AND `time`>'" . ($realtime - 86400) . "'");
                $count = $count . '&nbsp;/&nbsp;<span class="red">' . mysql_result($req, 0) . '</span>';
            }
    }
    return $count;
}

////////////////////////////////////////////////////////////
// Обработка ссылок и тэгов BBCODE в тексте               //
////////////////////////////////////////////////////////////
function tags($var = '')
{
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

// Служебная функция парсинга URL (прислал FlySelf)
function url_replace($m)
{
    if (!isset($m[3]))
    {
        return '<a href="' . $m[1] . '">' . $m[2] . '</a>';
    } else
    {
        return '<a href="' . $m[3] . '">' . $m[3] . '</a>';
    }
}

// Вырезание BBcode тэгов из текста
function notags($var = '')
{
    $var = strtr($var, array('[green]' => '', '[/green]' => '', '[red]' => '', '[/red]' => '', '[blue]' => '', '[/blue]' => '', '[b]' => '', '[/b]' => '', '[i]' => '', '[/i]' => '', '[u]' => '', '[/u]' => '', '[s]' => '', '[/s]' => '', '[c]' => '', '[/c]' => ''));
    return $var;
}

////////////////////////////////////////////////////////////
// Маскировка ссылок в тексте                             //
////////////////////////////////////////////////////////////
function antilink($var)
{
    $var = eregi_replace("((https?|ftp)://)([[:alnum:]_=/-]+(\\.[[:alnum:]_=/-]+)*(/[[:alnum:]+&._=/~%]*(\\?[[:alnum:]?+&_=/;%]*)?)?)", "[реклама]", $var);
    $var = strtr($var, array(".ru" => "***", ".com" => "***", ".net" => "***", ".org" => "***", ".info" => "***", ".mobi" => "***", ".wen" => "***", ".kmx" => "***", ".h2m" => "***"));
    return $var;
}

////////////////////////////////////////////////////////////
// Транслитерация текста                                  //
////////////////////////////////////////////////////////////
function trans($str)
{
    $str = strtr($str, array('a' => 'а', 'b' => 'б', 'v' => 'в', 'g' => 'г', 'd' => 'д', 'e' => 'е', 'yo' => 'ё', 'zh' => 'ж', 'z' => 'з', 'i' => 'и', 'j' => 'й', 'k' => 'к', 'l' => 'л', 'm' => 'м', 'n' => 'н', 'o' => 'о', 'p' => 'п', 'r' =>
        'р', 's' => 'с', 't' => 'т', 'u' => 'у', 'f' => 'ф', 'h' => 'х', 'c' => 'ц', 'ch' => 'ч', 'w' => 'ш', 'sh' => 'щ', 'q' => 'ъ', 'y' => 'ы', 'x' => 'э', 'yu' => 'ю', 'ya' => 'я', 'A' => 'А', 'B' => 'Б', 'V' => 'В', 'G' => 'Г', 'D' => 'Д', 'E' =>
        'Е', 'YO' => 'Ё', 'ZH' => 'Ж', 'Z' => 'З', 'I' => 'И', 'J' => 'Й', 'K' => 'К', 'L' => 'Л', 'M' => 'М', 'N' => 'Н', 'O' => 'О', 'P' => 'П', 'R' => 'Р', 'S' => 'С', 'T' => 'Т', 'U' => 'У', 'F' => 'Ф', 'H' => 'Х', 'C' => 'Ц', 'CH' => 'Ч', 'W' =>
        'Ш', 'SH' => 'Щ', 'Q' => 'Ъ', 'Y' => 'Ы', 'X' => 'Э', 'YU' => 'Ю', 'YA' => 'Я'));
    return $str;
}

////////////////////////////////////////////////////////////
// Декодирование htmlentities, PHP4совместимый режим      //
////////////////////////////////////////////////////////////
function unhtmlentities($string)
{
    $string = str_replace('&amp;', '&', $string);
    $string = preg_replace('~&#x0*([0-9a-f]+);~ei', 'chr(hexdec("\\1"))', $string);
    $string = preg_replace('~&#0*([0-9]+);~e', 'chr(\\1)', $string);
    $trans_tbl = get_html_translation_table(HTML_ENTITIES);
    $trans_tbl = array_flip($trans_tbl);
    return strtr($string, $trans_tbl);
}

////////////////////////////////////////////////////////////
// Функция постраничной навигации                         //
////////////////////////////////////////////////////////////
// За основу взята аналогичная функция от форума SMF2.0   //
////////////////////////////////////////////////////////////
function pagenav($base_url, $start, $max_value, $num_per_page)
{
    $pgcont = 4;
    $pgcont = (int)($pgcont - ($pgcont % 2)) / 2;
    if ($start >= $max_value)
        $start = max(0, (int)$max_value - (((int)$max_value % (int)$num_per_page) == 0 ? $num_per_page : ((int)$max_value % (int)$num_per_page)));
    else
        $start = max(0, (int)$start - ((int)$start % (int)$num_per_page));
    $base_link = '<a class="navpg" href="' . strtr($base_url, array('%' => '%%')) . 'start=%d' . '">%s</a> ';
    $pageindex = $start == 0 ? '' : sprintf($base_link, $start - $num_per_page, '&lt;&lt;');
    if ($start > $num_per_page * $pgcont)
        $pageindex .= sprintf($base_link, 0, '1');
    if ($start > $num_per_page * ($pgcont + 1))
        $pageindex .= '<span style="font-weight: bold;"> ... </span>';
    for ($nCont = $pgcont; $nCont >= 1; $nCont--)
        if ($start >= $num_per_page * $nCont)
        {
            $tmpStart = $start - $num_per_page * $nCont;
            $pageindex .= sprintf($base_link, $tmpStart, $tmpStart / $num_per_page + 1);
        }
    $pageindex .= '[<b>' . ($start / $num_per_page + 1) . '</b>] ';
    $tmpMaxPages = (int)(($max_value - 1) / $num_per_page) * $num_per_page;
    for ($nCont = 1; $nCont <= $pgcont; $nCont++)
        if ($start + $num_per_page * $nCont <= $tmpMaxPages)
        {
            $tmpStart = $start + $num_per_page * $nCont;
            $pageindex .= sprintf($base_link, $tmpStart, $tmpStart / $num_per_page + 1);
        }
    if ($start + $num_per_page * ($pgcont + 1) < $tmpMaxPages)
        $pageindex .= '<span style="font-weight: bold;"> ... </span>';
    if ($start + $num_per_page * $pgcont < $tmpMaxPages)
        $pageindex .= sprintf($base_link, $tmpMaxPages, $tmpMaxPages / $num_per_page + 1);
    if ($start + $num_per_page < $max_value)
    {
        $display_page = ($start + $num_per_page) > $max_value ? $max_value : ($start + $num_per_page);
        $pageindex .= sprintf($base_link, $display_page, '&gt;&gt;');
    }
    return $pageindex;
}

////////////////////////////////////////////////////////////
// Функция пересчета на дни, или часы                     //
////////////////////////////////////////////////////////////
function timecount($var)
{
    $str = '';
    if ($var < 0)
        $var = 0;
    $day = ceil($var / 86400);
    if ($var > 345600)
    {
        $str = $day . ' дней';
    } elseif ($var >= 172800)
    {
        $str = $day . ' дня';
    } elseif ($var >= 86400)
    {
        $str = '1 день';
    } else
    {
        $str = gmdate('H:i:s', round($var));
    }
    return $str;
}

////////////////////////////////////////////////////////////
// Форматирование размера файлов                          //
////////////////////////////////////////////////////////////
function formatsize($size)
{
    if ($size >= 1073741824)
    {
        $size = round($size / 1073741824 * 100) / 100 . ' Gb';
    } elseif ($size >= 1048576)
    {
        $size = round($size / 1048576 * 100) / 100 . ' Mb';
    } elseif ($size >= 1024)
    {
        $size = round($size / 1024 * 100) / 100 . ' Kb';
    } else
    {
        $size = $size . ' b';
    }
    return $size;
}

////////////////////////////////////////////////////////////
// Проверка переменных                                    //
////////////////////////////////////////////////////////////
function check($str)
{
    $str = htmlentities(trim($str), ENT_QUOTES, 'UTF-8');
    $str = str_replace("\'", "&#39;", $str);
    $str = str_replace("\r\n", "<br />", $str);
    $str = strtr($str, array(chr("0") => "", chr("1") => "", chr("2") => "", chr("3") => "", chr("4") => "", chr("5") => "", chr("6") => "", chr("7") => "", chr("8") => "", chr("9") => "", chr("10") => "", chr("11") => "", chr("12") => "", chr
        ("13") => "", chr("14") => "", chr("15") => "", chr("16") => "", chr("17") => "", chr("18") => "", chr("19") => "", chr("20") => "", chr("21") => "", chr("22") => "", chr("23") => "", chr("24") => "", chr("25") => "", chr("26") => "", chr("27") =>
        "", chr("28") => "", chr("29") => "", chr("30") => "", chr("31") => ""));
    $str = str_replace('\\', "&#92;", $str);
    $str = str_replace("|", "I", $str);
    $str = str_replace("||", "I", $str);
    $str = str_replace("/\\\$/", "&#36;", $str);
    $str = str_replace("[l]http://", "[l]", $str);
    $str = str_replace("[l] http://", "[l]", $str);
    $str = mysql_real_escape_string($str);
    return $str;
}

////////////////////////////////////////////////////////////
// Обработка смайлов                                      //
////////////////////////////////////////////////////////////
// Если $adm=1 то покажет и обычные и Админские смайлы
// Если $adm=2 то пересоздаст кэш смайлов

function smileys($str, $adm = 0)
{
    global $rootpath;
    // Записываем КЭШ смайлов
    if ($adm == 2)
    {
        // Обрабатываем простые смайлы
        $array1 = array();
        $path = $rootpath . 'smileys/simply/';
        $dir = opendir($path);
        while ($file = readdir($dir))
        {
            $name = explode(".", $file);
            if ($name[1] == 'gif' || $name[1] == 'jpg' || $name[1] == 'png')
            {
                $array1[':' . $name[0]] = '<img src="' . $path . $file . '" alt="" />';
                ++$count;
            }
        }
        closedir($dir);
        // Обрабатываем Админские смайлы
        $array2 = array();
        $array3 = array();
        $path = $rootpath . 'smileys/admin/';
        $dir = opendir($path);
        while ($file = readdir($dir))
        {
            $name = explode(".", $file);
            if ($name[1] == 'gif' || $name[1] == 'jpg' || $name[1] == 'png')
            {
                $array2[':' . trans($name[0]) . ':'] = '<img src="' . $path . $file . '" alt="" />';
                $array3[':' . $name[0] . ':'] = '<img src="' . $path . $file . '" alt="" />';
                ++$count;
            }
        }
        // Обрабатываем смайлы в каталогах
        $array4 = array();
        $array5 = array();
        $cat = glob($rootpath . 'smileys/user/*', GLOB_ONLYDIR);
        $total = count($cat);
        for ($i = 0; $i < $total; $i++)
        {
            $dir = opendir($cat[$i]);
            while ($file = readdir($dir))
            {
                $name = explode(".", $file);
                if ($name[1] == 'gif' || $name[1] == 'jpg' || $name[1] == 'png')
                {
                    $array4[':' . trans($name[0]) . ':'] = '<img src="' . $cat[$i] . '/' . $file . '" alt="" />';
                    $array5[':' . $name[0] . ':'] = '<img src="' . $cat[$i] . '/' . $file . '" alt="" />';
                    ++$count;
                }
            }
            closedir($dir);
        }
        $smileys = serialize(array_merge($array1, $array4, $array5));
        $smileys_adm = serialize(array_merge($array2, $array3));
        // Записываем в файл Кэша
        if ($fp = fopen($rootpath . 'smileys.dat', 'w'))
        {
            fputs($fp, $smileys . "\r\n" . $smileys_adm);
            fclose($fp);
            return $count;
        } else
        {
            return false;
        }
    } else
    {
        // Выдаем кэшированные смайлы
        $file = file($rootpath . 'smileys.dat');
        $smileys = unserialize($file[0]);
        if ($adm)
            $smileys = array_merge($smileys, unserialize($file[1]));
        return strtr($str, $smileys);
    }
}

/*
################################################################################
##                                                                            ##
##  Старые функции, которые постепенно будут удаляться.                       ##
##  НЕ ИСПОЛЬЗУЙТЕ их в своих модулях!!!                                      ##
##                                                                            ##
################################################################################
*/

function provcat($catalog) //TODO: Удалить функцию

{
    $cat1 = mysql_query("select * from `download` where type = 'cat' and id = '" . $catalog . "';");
    $cat2 = mysql_num_rows($cat1);
    $adrdir = mysql_fetch_array($cat1);
    if (($cat2 == 0) || (!is_dir("$adrdir[adres]/$adrdir[name]")))
    {
        echo 'Ошибка при выборе категории<br/><a href="?">К категориям</a><br/>';
        require_once ('../incfiles/end.php');
        exit;
    }
}

function deletcat($catalog)
{
    $dir = opendir($catalog);
    while (($file = readdir($dir)))
    {
        if (is_file($catalog . "/" . $file))
        {
            unlink($catalog . "/" . $file);
        } else
            if (is_dir($catalog . "/" . $file) && ($file != ".") && ($file != ".."))
            {
                deletcat($catalog . "/" . $file);
            }
    }
    closedir($dir);
    rmdir($catalog);
}

function format($name)
{
    $f1 = strrpos($name, ".");
    $f2 = substr($name, $f1 + 1, 999);
    $fname = strtolower($f2);
    return $fname;
}

function rus_lat($str)
{
    $str = strtr($str, array('а' => 'a', 'б' => 'b', 'в' => 'v', 'г' => 'g', 'д' => 'd', 'е' => 'e', 'ё' => 'e', 'ж' => 'j', 'з' => 'z', 'и' => 'i', 'й' => 'i', 'к' => 'k', 'л' => 'l', 'м' => 'm', 'н' => 'n', 'о' => 'o', 'п' => 'p', 'р' => 'r',
        'с' => 's', 'т' => 't', 'у' => 'u', 'ф' => 'f', 'х' => 'h', 'ц' => 'c', 'ч' => 'ch', 'ш' => 'sh', 'щ' => 'sch', 'ъ' => "", 'ы' => 'y', 'ь' => "", 'э' => 'ye', 'ю' => 'yu', 'я' => 'ya'));
    return $str;
}

?>