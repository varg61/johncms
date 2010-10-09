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

defined('_IN_JOHNADM') or die('Error: restricted access');

/*
-----------------------------------------------------------------
Принимаем данные, выводим форму поиска
-----------------------------------------------------------------
*/
$search = isset($_POST['search']) ? trim($_POST['search']) : '';
$search = $search ? $search : rawurldecode(trim($_GET['search']));
echo '<div class="phdr"><a href="index.php"><b>' . $lng['admin_panel'] . '</b></a> | ' . $lng['search_nick'] . '</div>';
echo '<form action="index.php?act=search_user" method="post"><div class="gmenu"><p>';
echo '<input type="text" name="search" value="' . functions::checkout($search) . '" />';
echo '<input type="submit" value="' . $lng['search'] . '" name="submit" /><br />';
echo '</p></div></form>';

/*
-----------------------------------------------------------------
Проверям на ошибки
-----------------------------------------------------------------
*/
$error = false;
if (!empty($search) && (mb_strlen($search) < 2 || mb_strlen($search) > 20))
    $error = '<div>' . $lng['error_nicklenght'] . '</div>';
if (preg_match("/[^1-9a-z\-\@\*\(\)\?\!\~\_\=\[\]]+/", functions::rus_lat(mb_strtolower($search))))
    $error .= '<div>' . $lng['error_wrongsymbols'] . '</div>';
if ($search && !$error) {
    /*
    -----------------------------------------------------------------
    Выводим результаты поиска
    -----------------------------------------------------------------
    */
    echo '<div class="phdr">' . $lng['search_results'] . '</div>';
    $search_db = functions::rus_lat(mb_strtolower($search));
    $search_db = strtr($search_db, array (
        '_' => '\\_',
        '%' => '\\%',
        '*' => '%'
    ));
    $search_db = '%' . $search_db . '%';
    $req = mysql_query("SELECT COUNT(*) FROM `users` WHERE `name_lat` LIKE '" . mysql_real_escape_string($search_db) . "'");
    $total = mysql_result($req, 0);
    if ($total > 0) {
        $req = mysql_query("SELECT * FROM `users` WHERE `name_lat` LIKE '" . mysql_real_escape_string($search_db) . "' ORDER BY `name` ASC LIMIT $start, $kmess");
        while ($res = mysql_fetch_array($req)) {
            echo $i % 2 ? '<div class="list2">' : '<div class="list1">';
            echo functions::display_user($res);
            echo '</div>';
            ++$i;
        }
    } else {
        echo '<div class="menu"><p>' . $lng['not_found'] . '</p></div>';
    }
    echo '<div class="phdr">' . $lng['total'] . ': ' . $total . '</div>';
    if ($total > $kmess) {
        // Навигация по страницам
        echo '<p>' . functions::display_pagination('index.php?act=search_user&amp;' . ($search_t ? 't=1&amp;' : '') . 'search=' . rawurlencode($search) . '&amp;', $start, $total, $kmess) . '</p>';
        echo '<p><form action="index.php?act=search_user" method="post"><input type="hidden" name="search" value="' . functions::checkout($search)
            . '" /><input type="text" name="page" size="2"/><input type="submit" value="' . $lng['to_page'] . ' &gt;&gt;"/></form></p>';
    }
    echo '<p><a href="index.php?act=search_user">' . $lng['search_new'] . '</a></p>';
} else {
    // Выводим сообщение об ошибке
    if ($error)
        echo functions::display_error($error);
    // Инструкции для поиска
    echo '<div class="phdr"><small>' . $lng['search_nick_help'] . '</small></div>';
}

?>