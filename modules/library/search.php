<?php

/**
 * @package     JohnCMS
 * @link        http://johncms.com
 * @copyright   Copyright (C) 2008-2012 JohnCMS Community
 * @license     LICENSE.txt (see attached file)
 * @version     VERSION.txt (see attached file)
 * @author      http://johncms.com/about
 */

/*
-----------------------------------------------------------------
Функция подсветки результатов запроса
-----------------------------------------------------------------
*/
function ReplaceKeywords($search, $text)
{
    $search = str_replace('*', '', $search);
    return mb_strlen($search) < 3 ? $text : preg_replace('|(' . preg_quote($search, '/') . ')|siu', '<span style="background-color: #FFFF33">$1</span>', $text);
}

/*
-----------------------------------------------------------------
Принимаем данные, выводим форму поиска
-----------------------------------------------------------------
*/
$search_post = isset($_POST['search']) ? trim($_POST['search']) : FALSE;
$search_get = isset($_GET['search']) ? rawurldecode(trim($_GET['search'])) : FALSE;
$search = $search_post ? $search_post : $search_get;
$search_t = isset($_REQUEST['t']);
echo '<div class="phdr"><a href="' . Router::getUrl(2) . '"><b>' . __('library') . '</b></a> | ' . __('search') . '</div>' .
     '<div class="gmenu"><form action="' . Vars::$URI . '" method="post"><p>' .
     '<input type="text" value="' . ($search ? Validate::checkout($search) : '') . '" name="search" />' .
     '<input type="submit" value="' . __('search') . '" name="submit" /><br />' .
     '<input name="t" type="checkbox" value="1" ' . ($search_t ? 'checked="checked"' : '') . ' />&nbsp;' . __('search_name') .
     '</p></form></div>';

/*
-----------------------------------------------------------------
Проверям на ошибки
-----------------------------------------------------------------
*/
$error = FALSE;
if ($search && (mb_strlen($search) < 2 || mb_strlen($search) > 64))
    $error = __('error_search_length');

if ($search && !$error) {
    /*
    -----------------------------------------------------------------
    Выводим результаты запроса
    -----------------------------------------------------------------
    */
    $array = explode(' ', $search);
    $count = count($array);
    $query = mysql_real_escape_string($search);
    $total = mysql_result(mysql_query("
        SELECT COUNT(*) FROM `lib`
        WHERE MATCH (`" . ($search_t ? 'name' : 'text') . "`) AGAINST ('$query' IN BOOLEAN MODE)
        AND `type` = 'bk'"), 0);
    echo '<div class="phdr">' . __('search_results') . '</div>';
    if ($total > Vars::$USER_SET['page_size'])
        echo '<div class="topmenu">' . Functions::displayPagination(Vars::$URI . '?' . ($search_t ? 't=1&amp;' : '') . 'search=' . urlencode($search) . '&amp;', Vars::$START, $total, Vars::$USER_SET['page_size']) . '</div>';
    if ($total) {
        $req = mysql_query("
            SELECT *, MATCH (`" . ($search_t ? 'name' : 'text') . "`) AGAINST ('$query' IN BOOLEAN MODE) as `rel`
            FROM `lib`
            WHERE MATCH (`" . ($search_t ? 'name' : 'text') . "`) AGAINST ('$query' IN BOOLEAN MODE)
            AND `type` = 'bk'
            ORDER BY `rel` DESC
            " . Vars::db_pagination()
        );
        $i = 0;
        while (($res = mysql_fetch_assoc($req)) !== FALSE) {
            echo $i % 2 ? '<div class="list2">' : '<div class="list1">';
            foreach ($array as $srch) if (($pos = mb_strpos(strtolower($res['text']), strtolower(str_replace('*', '', $srch)))) !== FALSE) break;
            if (!isset($pos) || $pos < 100) $pos = 100;
            $name = $res['name'];
            $text = Validate::checkout(mb_substr($res['text'], ($pos - 100), 400), 1);
            if ($search_t) foreach ($array as $val) $name = ReplaceKeywords($val, $name);
            else foreach ($array as $val) $text = ReplaceKeywords($val, $text);
            echo '<b><a href="' . Router::getUrl(2) . '?id=' . $res['id'] . '">' . $name . '</a></b><br />' . $text .
                 ' <div class="sub"><span class="gray">' . __('added') . ':</span> ' . $res['avtor'] .
                 ' <span class="gray">(' . Functions::displayDate($res['time']) . ')</span><br />' .
                 '<span class="gray">' . __('reads') . ':</span> ' . $res['count'] .
                 '</div></div>';
            ++$i;
        }
    } else {
        echo '<div class="rmenu"><p>' . __('search_results_empty') . '</p></div>';
    }
    echo '<div class="phdr">' . __('total') . ': ' . $total . '</div>';
    if ($total > Vars::$USER_SET['page_size']) {
        echo '<div class="topmenu">' . Functions::displayPagination(Vars::$URI . '?' . ($search_t ? 't=1&amp;' : '') . 'search=' . urlencode($search) . '&amp;', Vars::$START, $total, Vars::$USER_SET['page_size']) . '</div>' .
             '<p><form action="' . Vars::$URI . '?' . ($search_t ? 't=1&amp;' : '') . 'search=' . urlencode($search) . '" method="post">' .
             '<input type="text" name="page" size="2"/>' .
             '<input type="submit" value="' . __('to_page') . ' &gt;&gt;"/>' .
             '</form></p>';
    }
} else {
    if ($error) echo Functions::displayError($error);
    echo '<div class="phdr"><small>' . __('search_help') . '</small></div>';
}
echo '<p>' . ($search ? '<a href="' . Vars::$URI . '">' . __('search_new') . '</a><br />' : '') .
     '<a href="' . Router::getUrl(2) . '">' . __('library') . '</a></p>';