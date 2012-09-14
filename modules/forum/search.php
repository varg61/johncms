<?php

/**
 * @package     JohnCMS
 * @link        http://johncms.com
 * @copyright   Copyright (C) 2008-2012 JohnCMS Community
 * @license     LICENSE.txt (see attached file)
 * @version     VERSION.txt (see attached file)
 * @author      http://johncms.com/about
 */

echo'<p>' . Counters::forumCountNew(1) . '</p>' .
    '<div class="phdr"><a href="' . Vars::$MODULE_URI . '"><b>' . lng('forum') . '</b></a> | ' . lng('search') . '</div>';

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
                Vars::setUserData('forum_search');
                header('Location: ' . Vars::$URI);
            } else {
                echo'<form action="' . Vars::$URI . '?act=reset" method="post">' .
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
        $search_post = isset($_POST['search']) ? trim($_POST['search']) : FALSE;
        $search_get = isset($_GET['search']) ? rawurldecode(trim($_GET['search'])) : FALSE;
        $search = $search_post ? $search_post : $search_get;
        //$search = preg_replace("/[^\w\x7F-\xFF\s]/", " ", $search);
        $search_t = isset($_REQUEST['t']);
        $to_history = FALSE;
        echo'<div class="gmenu">' .
            '<form action="' . Vars::$URI . '" method="post"><p>' .
            '<input type="text" value="' . ($search ? Validate::filterString($search) : '') . '" name="search" />' .
            '<input type="submit" value="' . lng('search') . '" name="submit" /><br />' .
            '<input name="t" type="checkbox" value="1" ' . ($search_t ? 'checked="checked"' : '') . ' />&nbsp;' . lng('search_topic_name') .
            '</p></form>' .
            '</div>';

        /*
        -----------------------------------------------------------------
        Проверям на ошибки
        -----------------------------------------------------------------
        */
        $error = $search && mb_strlen($search) < 4 || mb_strlen($search) > 64 ? TRUE : FALSE;

        if ($search && !$error) {
            /*
            -----------------------------------------------------------------
            Выводим результаты запроса
            -----------------------------------------------------------------
            */
            $array = explode(' ', $search);
            $count = count($array);
            $query = mysql_real_escape_string($search);
			if ($search_t) {
			    // Поиск в названиях тем
                $total = mysql_result(mysql_query("
                    SELECT COUNT(*) FROM `forum`
                    WHERE MATCH (`text`) AGAINST ('$query' IN BOOLEAN MODE)
                    AND `type` = 't'" . (Vars::$USER_RIGHTS >= 7 ? "" : " AND `close` != '1'
                ")), 0);
			} else {
			    // Поиск только в тексте
                $total = mysql_result(mysql_query("
                    SELECT COUNT(*) FROM `forum`, `forum` as `forum2`
                    WHERE MATCH (`forum`.`text`) AGAINST ('$query' IN BOOLEAN MODE)
                    AND `forum`.`type` = 'm'
					AND `forum2`.`id` = `forum`.`refid`
					" . (Vars::$USER_RIGHTS >= 7 ? "" : "AND `forum2`.`close` != '1' AND `forum`.`close` != '1'
				")), 0);
			}
            echo '<div class="phdr">' . lng('search_results') . '</div>';
            if ($total > Vars::$USER_SET['page_size'])
                echo '<div class="topmenu">' . Functions::displayPagination(Vars::$URI . '?' . ($search_t ? 't=1&amp;' : '') . 'search=' . urlencode($search) . '&amp;', Vars::$START, $total, Vars::$USER_SET['page_size']) . '</div>';
            if ($total) {
                $to_history = TRUE;
                if ($search_t) {
                    // Поиск в названиях тем
                    $req = mysql_query("
                        SELECT *, MATCH (`text`) AGAINST ('$query' IN BOOLEAN MODE) as `rel`
                        FROM `forum`
                        WHERE MATCH (`text`) AGAINST ('$query' IN BOOLEAN MODE)
                        AND `type` = '" . ($search_t ? 't' : 'm') . "'
						" . (Vars::$USER_RIGHTS >= 7 ? "" : "AND `close` != '1'") . "
                        ORDER BY `rel` DESC
                        " . Vars::db_pagination()
                    );
				} else {
                    // Поиск только в тексте
				    $req = mysql_query("
                        SELECT `forum`.*, `forum2`.`id` as `id2`, `forum2`.`text` as `text2`,
						MATCH (`forum`.`text`) AGAINST ('$query' IN BOOLEAN MODE) as `rel`
                        FROM `forum`, `forum` as `forum2`
                        WHERE MATCH (`forum`.`text`) AGAINST ('$query' IN BOOLEAN MODE)
                        AND `forum`.`type` = 'm'
						AND `forum2`.`id` = `forum`.`refid`
						" . (Vars::$USER_RIGHTS >= 7 ? "" : "AND `forum2`.`close` != '1' AND `forum`.`close` != '1'") . "
                        ORDER BY `rel` DESC
                        " . Vars::db_pagination()
                    );
				}
                $i = 0;
                while (($res = mysql_fetch_assoc($req)) !== FALSE) {
                    echo $i % 2 ? '<div class="list2">' : '<div class="list1">';
					if ($search_t) {
                        // Поиск в названиях тем
                        $req_p = mysql_query("SELECT `text` FROM `forum` WHERE `refid` = " . $res['id'] . 
						    (Vars::$USER_RIGHTS >= 7 ? "" : "AND `close` != '1'") . " ORDER BY `id` ASC LIMIT 1");
                        $res_p = mysql_fetch_assoc($req_p);
                        $res['text2'] = $res_p['text'];
                    }
                    $text = $search_t ? $res['text2'] : $res['text'];
                    foreach ($array as $srch) if (($pos = mb_strpos(mb_strtolower($res['text']), mb_strtolower(str_replace('*', '', $srch)))) !== FALSE) break;
                    if (!isset($pos) || $pos < 100) $pos = 100;
                    $text = Validate::filterString(mb_substr($text, ($pos - 100), 400), 1);
					$text = preg_replace('#\[c\](.*?)\[/c\]#si', '<div class="quote">\1</div>', $text);
                    if ($search_t) {
                        foreach ($array as $val) {
                            $res['text'] = ReplaceKeywords($val, $res['text']);
                        }
					} else {
                        foreach ($array as $val) {
                            $text = ReplaceKeywords($val, $text);
                        }
                    }
					echo '<b>' . ($search_t ? $res['text'] : $res['text2']) . '</b><br />' .
					    '<a href="../users/profile.php?user=' . $res['user_id'] . '">' . $res['from'] . '</a> ' .
                        ' <span class="gray">(' . Functions::displayDate($res['time']) . ')</span><br/>' . $text;
                    if (mb_strlen($res['text']) > 500)
                        echo'...<a href="' . Vars::$MODULE_URI . '?act=post&amp;id=' . $res['id'] . '">' . lng('read_all') . ' &gt;&gt;</a>';
                    echo'<br /><a href="' . Vars::$MODULE_URI . '?id=' . ($search_t ? $res['id'] : $res['id2']) . '">' . lng('to_topic') . '</a>' . ($search_t ? ''
                        : ' | <a href="' . Vars::$MODULE_URI . '?act=post&amp;id=' . $res['id'] . '">' . lng('to_post') . '</a>');
                    echo '</div>';
                    ++$i;
                }
            } else {
                echo'<div class="rmenu"><p>' . lng('search_results_empty') . '</p></div>';
            }
            echo'<div class="phdr">' . lng('total') . ': ' . $total . '</div>';
        } else {
            if ($error) echo Functions::displayError(lng('error_wrong_lenght'));
            echo'<div class="phdr"><small>' . lng('search_help') . '</small></div>';
        }

        /*
        -----------------------------------------------------------------
        Обрабатываем и показываем историю личных поисковых запросов
        -----------------------------------------------------------------
        */
        if (Vars::$USER_ID) {
            $search_val = mb_strtolower($search);
            if (($history = Vars::getUserData('forum_search')) === FALSE) {
                $history = array();
            }
            // Записываем данные в историю
            if ($to_history && !in_array($search_val, $history)) {
                if (count($history) > 20) {
                    array_shift($history);
                }
                $history[] = $search_val;
                Vars::setUserData('forum_search', $history);
            }
            // Показываем историю поиска
            if (!empty($history)) {
                sort($history);
                $history_list = array();
                foreach ($history as $val) {
                    $history_list[] = '<a href="' . Vars::$URI . '?search=' . urlencode($val) . '">' . htmlspecialchars($val) . '</a>';
                }
                echo'<div class="topmenu">' .
                    '<b>' . lng('search_history') . '</b> <span class="red"><a href="' . Vars::$URI . '?act=reset">[x]</a></span><br />' .
                    Functions::displayMenu($history_list) .
                    '</div>';
            }
        }

        // Постраничная навигация
        if (isset($total) && $total > Vars::$USER_SET['page_size']) {
            echo'<div class="topmenu">' . Functions::displayPagination(Vars::$URI . '?' . ($search_t ? 't=1&amp;' : '') . 'search=' . urlencode($search) . '&amp;', Vars::$START, $total, Vars::$USER_SET['page_size']) . '</div>' .
                '<p><form action="' . Vars::$URI . '?' . ($search_t ? 't=1&amp;' : '') . 'search=' . urlencode($search) . '" method="post">' .
                '<input type="text" name="page" size="2"/>' .
                '<input type="submit" value="' . lng('to_page') . ' &gt;&gt;"/>' .
                '</form></p>';
        }

        echo '<p>' . ($search ? '<a href="' . Vars::$URI . '">' . lng('search_new') . '</a><br />' : '') . '<a href="' . Vars::$MODULE_URI . '">' . lng('forum') . '</a></p>';
}