<?php

/**
 * @package     JohnCMS
 * @link        http://johncms.com
 * @copyright   Copyright (C) 2008-2012 JohnCMS Community
 * @license     LICENSE.txt (see attached file)
 * @version     VERSION.txt (see attached file)
 * @author      http://johncms.com/about
 */

echo '<div class="phdr"><a href="index.php"><b>' . lng('forum') . '</b></a> | ' . lng('search') . '</div>';

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

switch (Vars::$ACT) {
    case 'reset':
        /*
        -----------------------------------------------------------------
        Очищаем историю личных поисковых запросов
        -----------------------------------------------------------------
        */
        if (Vars::$USER_ID) {
            if (isset($_POST['submit'])) {
                settings::user_data_put('forum_search');
                header('Location: ' . Vars::$URI);
            } else {
                echo '<form action="' . Vars::$URI . '?act=reset" method="post">' .
                     '<div class="rmenu">' .
                     '<p>' . lng('search_history_reset') . '</p>' .
                     '<p><input type="submit" name="submit" value="' . lng('clear') . '" /></p>' .
                     '<p><a href="' . Vars::$URI . '">' . lng('cancel') . '</a></p>' .
                     '</div>' .
                     '</form>';
            }
        }
        break;

    default:
        /*
        -----------------------------------------------------------------
        Принимаем данные, выводим форму поиска
        -----------------------------------------------------------------
        */
        $search_post = isset($_POST['search']) ? trim($_POST['search']) : false;
        $search_get = isset($_GET['search']) ? rawurldecode(trim($_GET['search'])) : false;
        $search = $search_post ? $search_post : $search_get;
        //$search = preg_replace("/[^\w\x7F-\xFF\s]/", " ", $search);
        $search_t = isset($_REQUEST['t']);
        $to_history = false;
        echo '<div class="gmenu"><form action="' . Vars::$URI . '" method="post"><p>' .
             '<input type="text" value="' . ($search ? Validate::filterString($search) : '') . '" name="search" />' .
             '<input type="submit" value="' . lng('search') . '" name="submit" /><br />' .
             '<input name="t" type="checkbox" value="1" ' . ($search_t ? 'checked="checked"' : '') . ' />&nbsp;' . lng('search_topic_name') .
             '</p></form></div>';

        /*
        -----------------------------------------------------------------
        Проверям на ошибки
        -----------------------------------------------------------------
        */
        $error = $search && mb_strlen($search) < 4 || mb_strlen($search) > 64 ? true : false;

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
                SELECT COUNT(*) FROM `forum`
                WHERE MATCH (`text`) AGAINST ('$query' IN BOOLEAN MODE)
                AND `type` = '" . ($search_t ? 't' : 'm') . "'" . (Vars::$USER_RIGHTS >= 7 ? "" : " AND `close` != '1'
            ")), 0);
            echo '<div class="phdr">' . lng('search_results') . '</div>';
            if ($total > Vars::$USER_SET['page_size'])
                echo '<div class="topmenu">' . Functions::displayPagination(Vars::$URI . '?' . ($search_t ? 't=1&amp;' : '') . 'search=' . urlencode($search) . '&amp;', Vars::$START, $total, Vars::$USER_SET['page_size']) . '</div>';
            if ($total) {
                $to_history = true;
                $req = mysql_query("
                    SELECT *, MATCH (`text`) AGAINST ('$query' IN BOOLEAN MODE) as `rel`
                    FROM `forum`
                    WHERE MATCH (`text`) AGAINST ('$query' IN BOOLEAN MODE)
                    AND `type` = '" . ($search_t ? 't' : 'm') . "'
                    ORDER BY `rel` DESC
                    " . Vars::db_pagination()
                );
                $i = 0;
                while (($res = mysql_fetch_assoc($req)) !== false) {
                    echo $i % 2 ? '<div class="list2">' : '<div class="list1">';
                    if (!$search_t) {
                        // Поиск только в тексте
                        $req_t = mysql_query("SELECT `id`,`text` FROM `forum` WHERE `id` = '" . $res['refid'] . "'");
                        $res_t = mysql_fetch_assoc($req_t);
                        echo '<b>' . $res_t['text'] . '</b><br />';
                    } else {
                        // Поиск в названиях тем
                        $req_p = mysql_query("SELECT `text` FROM `forum` WHERE `refid` = '" . $res['id'] . "' ORDER BY `id` ASC LIMIT 1");
                        $res_p = mysql_fetch_assoc($req_p);
                        foreach ($array as $val) {
                            $res['text'] = ReplaceKeywords($val, $res['text']);
                        }
                        echo '<b>' . $res['text'] . '</b><br />';
                    }
                    echo '<a href="../users/profile.php?user=' . $res['user_id'] . '">' . $res['from'] . '</a> ';
                    echo ' <span class="gray">(' . Functions::displayDate($res['time']) . ')</span><br/>';
                    $text = $search_t ? $res_p['text'] : $res['text'];
                    foreach ($array as $srch) if (($pos = mb_strpos(strtolower($res['text']), strtolower(str_replace('*', '', $srch)))) !== false) break;
                    if (!isset($pos) || $pos < 100) $pos = 100;
                    $text = preg_replace('#\[c\](.*?)\[/c\]#si', '<div class="quote">\1</div>', $text);
                    $text = Validate::filterString(mb_substr($text, ($pos - 100), 400), 1);
                    if (!$search_t) {
                        foreach ($array as $val) {
                            $text = ReplaceKeywords($val, $text);
                        }
                    }
                    echo $text;
                    if (mb_strlen($res['text']) > 500)
                        echo '...<a href="index.php?act=post&amp;id=' . $res['id'] . '">' . lng('read_all') . ' &gt;&gt;</a>';
                    echo '<br /><a href="index.php?id=' . ($search_t ? $res['id'] : $res_t['id']) . '">' . lng('to_topic') . '</a>' . ($search_t ? ''
                            : ' | <a href="index.php?act=post&amp;id=' . $res['id'] . '">' . lng('to_post') . '</a>');
                    echo '</div>';
                    ++$i;
                }
            } else {
                echo '<div class="rmenu"><p>' . lng('search_results_empty') . '</p></div>';
            }
            echo '<div class="phdr">' . lng('total') . ': ' . $total . '</div>';
        } else {
            if ($error) echo Functions::displayError(lng('error_wrong_lenght'));
            echo '<div class="phdr"><small>' . lng('search_help') . '</small></div>';
        }

        /*
        -----------------------------------------------------------------
        Обрабатываем и показываем историю личных поисковых запросов
        -----------------------------------------------------------------
        */
        if (Vars::$USER_ID) {
            $search_val = mb_strtolower($search);
            if (($history = Vars::getUserData('forum_search')) === false) $history = array();
            // Записываем данные в историю
            if ($to_history && !in_array($search_val, $history)) {
                if (count($history) > 20) array_shift($history);
                $history[] = $search_val;
                settings::user_data_put('forum_search', $history);
            }
            // Показываем историю поиска
            if (!empty($history)) {
                sort($history);
                foreach ($history as $val) $history_list[] = '<a href="' . Vars::$URI . '?search=' . urlencode($val) . '">' . htmlspecialchars($val) . '</a>';
                echo '<div class="topmenu">' .
                     '<b>' . lng('search_history') . '</b> <span class="red"><a href="' . Vars::$URI . '?act=reset">[x]</a></span><br />' .
                     Functions::displayMenu($history_list) .
                     '</div>';
            }
        }

        // Постраничная навигация
        if (isset($total) && $total > Vars::$USER_SET['page_size']) {
            echo '<div class="topmenu">' . Functions::displayPagination(Vars::$URI . '?' . ($search_t ? 't=1&amp;' : '') . 'search=' . urlencode($search) . '&amp;', Vars::$START, $total, Vars::$USER_SET['page_size']) . '</div>' .
                 '<p><form action="' . Vars::$URI . '?' . ($search_t ? 't=1&amp;' : '') . 'search=' . urlencode($search) . '" method="post">' .
                 '<input type="text" name="page" size="2"/>' .
                 '<input type="submit" value="' . lng('to_page') . ' &gt;&gt;"/>' .
                 '</form></p>';
        }

        echo '<p>' . ($search ? '<a href="' . Vars::$URI . '">' . lng('search_new') . '</a><br />' : '') . '<a href="index.php">' . lng('forum') . '</a></p>';
}