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
// Принимаем и проверяем условия для поиска               //
////////////////////////////////////////////////////////////
$error = false;
$search = '';
$term = 1;
if (isset($_GET['reset']))
{
    // Сброс формы поиска
    unset($_SESSION['search']);
    unset($_SESSION['term']);
} elseif (isset($_POST['search']) && isset($_POST['submit']))
{
    // Принимаем данные из формы
    $search = isset($_POST['search']) ? trim($_POST['search']) : '';
    $term = isset($_POST['term']) ? 1 : 0;
    if (!empty($search) && (mb_strlen($search) < 2 || mb_strlen($search) > 15))
        $error = '<div>Недопустимая длина Ника. Разрешено минимум 2 и максимум 15 символов.</div>';
    if (preg_match("/[^1-9a-z\-\@\*\(\)\?\!\~\_\=\[\]]+/", rus_lat(mb_strtolower($search))))
        $error .= '<div>Недопустимые символы</div>';
    if (!$error && !empty($search))
    {
        $_SESSION['search'] = $search;
        $_SESSION['term'] = $term;
    }
} elseif (isset($_SESSION['search']))
{
    // Принимаем данные из сессии
    $search = $_SESSION['search'];
    $term = $_SESSION['term'];
}

echo '<div class="phdr"><b>Поиск пользователя</b></div>';
echo '<form action="user_search.php" method="post"><div class="gmenu"><p>';
echo 'Кого ищем?<br /><input type="text" name="search" value="' . htmlentities($search, ENT_QUOTES, 'UTF-8') . '" />';
echo '<input type="submit" value="Поиск" name="submit" /><br />';
echo '<input name="term" type="checkbox" value="1" ' . ($term ? 'checked="checked"' : '') . ' />&nbsp;Строгий поиск<br/>';
echo '</p></div></form>';

if ($error)
{
    ////////////////////////////////////////////////////////////
    // Если есть ошибки, выводим предупреждения               //
    ////////////////////////////////////////////////////////////
    unset($_SESSION['search']);
    unset($_SESSION['term']);
    echo '<div class="rmenu"><p>ОШИБКА!<br />' . $error . '</p></div>';
    require_once ("../incfiles/end.php");
    exit;
}

if (isset($_SESSION['search']))
{
    ////////////////////////////////////////////////////////////
    // Поиск и вывод результатов                              //
    ////////////////////////////////////////////////////////////
    echo '<div class="phdr">Результаты запроса</div>';
    $search = mysql_real_escape_string(rus_lat(mb_strtolower($search)));
    $search = strtr($search, array('_' => '\\_', '%' => '\\%', '*' => '%'));
    if (!$term)
        $search = '%' . $search . '%'; // Если задан нестрогий поиск
    $req = mysql_query("SELECT COUNT(*) FROM `users` WHERE `name_lat` LIKE '" . $search . "'");
    $total = mysql_result($req, 0);
    if ($total > 0)
    {
        $req = mysql_query("SELECT * FROM `users` WHERE `name_lat` LIKE '" . $search . "' ORDER BY `name` ASC LIMIT " . $start . "," . $kmess);
        while ($res = mysql_fetch_array($req))
        {
            echo is_integer($i / 2) ? '<div class="list1">' : '<div class="list2">';
            echo $res['datereg'] > $realtime - 86400 ? '<img src="../images/add.gif" alt=""/>&nbsp;' : '';
            echo '<img src="../images/' . ($res['sex'] == 'm' ? 'm' : 'f') . '.gif" alt=""/>&nbsp;';
            if (!empty($user_id) && ($user_id != $res['id']))
            {
                echo '<a href="anketa.php?user=' . $res['id'] . '"><b>' . $res['name'] . '</b></a> ';
            } else
            {
                echo '<b>' . $res['name'] . '</b>';
            }
            switch ($res['rights'])
            {
                case 7:
                    echo ' Adm ';
                    break;
                case 6:
                    echo ' Smd ';
                    break;
                case 2:
                    echo ' Mod ';
                    break;
                case 1:
                    echo ' Kil ';
                    break;
            }
            $ontime = $res['lastdate'] + 300;
            if ($realtime > $ontime)
            {
                echo '<font color="#FF0000"> [Off]</font>';
            } else
            {
                echo '<font color="#00AA00"> [ON]</font>';
            }
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
        echo '<p>' . pagenav('user_search.php?', $start, $total, $kmess) . '</p>';
        echo '<p><form action="user_search.php" method="get"><input type="text" name="page" size="2"/><input type="submit" value="К странице &gt;&gt;"/></form></p>';
    }
    echo '<p><a href="user_search.php?reset">Новый поиск</a></p>';
} else
{
    echo '<div class="bmenu"><small>';
    echo 'Поиск идет по Нику пользователя (NickName) и нечувствителен к регистру букв. То есть, <b>UsEr</b> и <b>user</b> для поиска равноценны.';
    echo '<br />Если включен строгий поиск, то будет найдено только полное совпадение, иначе будет искаться любое совпадение внутри Ников.';
    echo '<br />В поиске допустимо использовать знак маски *';
    echo '<br />Запрос на поиск транслитерируется, то есть, чтоб найти, к примеру, ник ДИМА, Вы можете в запросе написать dima, результат будет один и тот же.';
    echo '</small></div>';
}

require_once ("../incfiles/end.php");

?>