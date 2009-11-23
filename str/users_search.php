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

define('_IN_JOHNCMS', 1);

$textl = 'Поиск пользователя';
require_once ("../incfiles/core.php");
require_once ("../incfiles/head.php");

////////////////////////////////////////////////////////////
// Принимаем данные, выводим форму поиска                 //
////////////////////////////////////////////////////////////
$search = isset($_POST['search']) ? trim($_POST['search']) : '';
$search = $search ? $search : rawurldecode(trim($_GET['search']));
$search_t = isset($_REQUEST['t']) ? 1 : 0;

echo '<div class="phdr"><b>Поиск пользователя</b></div>';
echo '<form action="users_search.php" method="post"><div class="gmenu"><p>';
echo 'Кого ищем?<br /><input type="text" name="search" value="' . checkout($search) . '" />';
echo '<input type="submit" value="Поиск" name="submit" /><br />';
echo '<input name="t" type="checkbox" value="1" ' . ($search_t ? 'checked="checked"' : '') . ' />&nbsp;Строгий поиск<br/>';
echo '</p></div></form>';

////////////////////////////////////////////////////////////
// Проверям на ошибки                                     //
////////////////////////////////////////////////////////////
$error = false;
if (!empty($search) && (mb_strlen($search) < 2 || mb_strlen($search) > 15))
    $error = '<div>Недопустимая длина Ника. Разрешено минимум 2 и максимум 15 символов.</div>';
if (preg_match("/[^1-9a-z\-\@\*\(\)\?\!\~\_\=\[\]]+/", rus_lat(mb_strtolower($search))))
    $error .= '<div>Недопустимые символы</div>';

if ($search && !$error)
{
    ////////////////////////////////////////////////////////////
    // Выводим результаты поиска                              //
    ////////////////////////////////////////////////////////////
    echo '<div class="phdr">Результаты запроса</div>';
    $search_db = mysql_real_escape_string(rus_lat(mb_strtolower($search)));
    $search_db = strtr($search_db, array('_' => '\\_', '%' => '\\%', '*' => '%'));
    if (!$search_t)
        $search_db = '%' . $search_db . '%'; // Если задан нестрогий поиск
    $req = mysql_query("SELECT COUNT(*) FROM `users` WHERE `name_lat` LIKE '" . $search_db . "'");
    $total = mysql_result($req, 0);
    if ($total > 0)
    {
        $req = mysql_query("SELECT * FROM `users` WHERE `name_lat` LIKE '" . $search_db . "' ORDER BY `name` ASC LIMIT " . $start . "," . $kmess);
        while ($res = mysql_fetch_array($req))
        {
            echo ($i % 2) ? '<div class="list2">' : '<div class="list1">';
            echo show_user($res, 1, ($dostsmod ? 1 : 0));
            echo '</div>';
            ++$i;
        }
    } else
    {
        echo '<div class="menu"><p>По Вашему запросу ничего не найдено</p></div>';
    }
    echo '<div class="phdr">Всего найдено: ' . $total . '</div>';
    if ($total > $kmess)
    {
        // Навигация по страницам
        echo '<p>' . pagenav('users_search.php?' . ($search_t ? 't=1&amp;' : '') . 'search=' . rawurlencode($search) . '&amp;', $start, $total, $kmess) . '</p>';
        echo '<p><form action="users_search.php" method="get"><input type="text" name="page" size="2"/><input type="submit" value="К странице &gt;&gt;"/></form></p>';
    }
    echo '<p><a href="users_search.php">Новый поиск</a></p>';
} else
{
    // Выводим сообщение об ошибке
    if ($error)
        echo '<div class="rmenu"><p>ОШИБКА!<br />' . $error . '</p></div>';
    // Инструкции для поиска
    echo '<div class="phdr"><small>';
    echo 'Поиск идет по Нику пользователя (NickName) и нечувствителен к регистру букв. То есть, <b>UsEr</b> и <b>user</b> для поиска равноценны.';
    echo '<br />Если включен строгий поиск, то будет найдено только полное совпадение, иначе будет искаться любое совпадение внутри Ников.';
    echo '<br />В поиске допустимо использовать знак маски *';
    echo '<br />Запрос на поиск транслитерируется, то есть, чтоб найти, к примеру, ник ДИМА, Вы можете в запросе написать dima, результат будет один и тот же.';
    echo '</small></div>';
}

require_once ("../incfiles/end.php");

?>