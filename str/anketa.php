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

$headmod = 'anketa';
$textl = 'Анкета';
require_once ('../incfiles/core.php');

if (!$user_id)
{
    header('Location: ../index.php');
    exit;
}

require_once ('../incfiles/head.php');
$user = isset($_GET['user']) ? intval($_GET['user']) : '';

if ($user)
{
    // Если был запрос на юзера, то получаем его данные
    $req = mysql_query("SELECT * FROM `users` WHERE `id` = '$user' LIMIT 1");
    if (mysql_num_rows($req))
    {
        $datauser = mysql_fetch_assoc($req);
    } else
    {
        echo display_error('Такого пользователя не существует');
        require_once ("../incfiles/end.php");
        exit;
    }
}

////////////////////////////////////////////////////////////
// Выводим анкету пользователя                            //
////////////////////////////////////////////////////////////
echo '<div class="phdr"><b>' . ($user ? 'Анкета пользователя' : 'Моя анкета') . '</b></div>';
if ($datauser['dayb'] == $day && $datauser['monthb'] == $mon)
{
    echo '<div class="gmenu">ИМЕНИНЫ!!!</div>';
}
echo '<div class="menu"><img src="../theme/' . $set_user['skin'] . '/images/' . ($datauser['sex'] == 'm' ? 'm' : 'f') . ($datauser['datereg'] > $realtime - 86400 ? '_new.gif" width="20"' : '.gif" width="16"') . ' height="16"/>&nbsp;';
echo '<b>' . $datauser['name'] . '</b> (id: ' . $datauser['id'] . ')';
$ontime = $datauser['lastdate'];
$ontime2 = $ontime + 300;
$preg = $datauser['preg'];
$regadm = $datauser['regadm'];
if ($realtime > $ontime2)
{
    echo '<span class="red"> [Off]</span>';
    if ($datauser['sex'] == "m")
    {
        $lastvisit = 'был: ';
    }
    if ($datauser['sex'] == "zh")
    {
        $lastvisit = 'была: ';
    }
    $lastvisit = $lastvisit . date("d.m.Y (H:i)", $datauser['lastdate']);
} else
{
    echo '<span class="green"> [ON]</span>';
}
if (!empty($datauser['status']))
    echo '<br /><img src="../images/star.gif" alt=""/>&nbsp;<span class="status">' . $datauser['status'] . '</span>';
echo '</div>';
echo '<div class="menu"><u>Логин</u>: <b>' . $datauser['name_lat'] . '</b></div>';
if ($datauser['rights'] != 0)
{
    echo '<div class="menu"><u>Должность</u>: ';
    switch ($datauser['rights'])
    {
        case 1:
            echo 'Киллер';
            break;
        case 2:
            echo 'Модер чата';
            break;
        case 3:
            echo 'Модер форума';
            break;
        case 4:
            echo 'Зам. админа по загрузкам';
            break;
        case 5:
            echo 'Зам. админа по библиотеке';
            break;
        case 6:
            echo 'Супермодератор';
            break;
        case 7:
            echo 'Админ';
            break;
    }
    echo '</div>';
}
if (isset($lastvisit))
    echo '<div class="menu">Последний раз ' . $lastvisit . '</div>';
echo '<div class="bmenu">Личные данные</div>';
echo '<div class="menu"><u>Имя</u>: ' . $datauser['imname'] . '</div>';
if (!empty($datauser['dayb']))
{
    echo '<div class="menu"><u>Дата рождения</u>: ' . $datauser['dayb'] . '&nbsp;' . $mesyac[$datauser['monthb']] . '&nbsp;' . $datauser['yearofbirth'] . '</div>';
}
if (!empty($datauser['live']))
{
    echo '<div class="menu"><u>Город</u>: ' . $datauser['live'] . '</div>';
}
if (!empty($datauser['about']))
{
    echo '<div class="menu"><u>О себе</u>: ' . smileys(tags($datauser['about'])) . '</div>';
}
$req = mysql_query("select * from `gallery` where `type`='al' and `user`=1 and `avtor`='" . $datauser['name'] . "' LIMIT 1;");
if (mysql_num_rows($req) == 1)
{
    $res = mysql_fetch_array($req);
    echo '<div class="gmenu"><a href="../gallery/index.php?id=' . $res['id'] . '">Личный альбом</a></div>';
}
echo '<div class="bmenu">Связь</div>';
if (!empty($datauser['mibile']))
    echo '<div class="menu"><u>Тел. номер</u>: ' . $datauser['mibile'] . '</div>';
if ($datauser['mailact'] == 1)
{
    if (!empty($datauser['mail']))
    {
        echo '<div class="menu"><u>E-mail</u>: ';
        if ($datauser['mailvis'] == 1)
        {
            echo $datauser['mail'] . '</div>';
        } else
        {
            echo 'скрыт</div>';
        }
    }
}
if (!empty($datauser['icq']))
    echo '<div class="menu"><u>ICQ</u>:&nbsp;<img src="http://web.icq.com/whitepages/online?icq=' . $datauser['icq'] . '&amp;img=5" width="12" height="12" alt=""/>&nbsp;' . $datauser['icq'] . '</div> ';
if (!empty($datauser['skype']))
    echo '<div class="menu"><u>Skype</u>:&nbsp;' . $datauser['skype'] . '</div> ';
if (!empty($datauser['jabber']))
    echo '<div class="menu"><u>Jabber</u>:&nbsp;' . $datauser['jabber'] . '</div> ';
if (!empty($datauser['www']))
{
    //$sait = str_replace("http://", "", $datauser['www']);
    echo '<div class="menu"><u>Сайт</u>: ' . tags($datauser['www']) . '</div>';
}
echo '<div class="bmenu">Статистика</div><div class="menu">';
if ($datauser['sex'] == "m")
{
    echo "Зарегистрирован";
}
if ($datauser['sex'] == "zh")
{
    echo "Зарегистрирована";
}
echo ': ' . date("d.m.Y", $datauser['datereg']);
if ($dostadm == "1")
{
    echo '<br />';
    if ($preg == 0 && $regadm == "")
    {
        echo "Ожидает подтверждения регистрации<br/>";
    }
    if ($preg == 0 && $regadm != "")
    {
        echo "Регистрацию отклонил $regadm<br/>";
    }
    if ($preg == 1 && $regadm != "")
    {
        echo "Регистрацию подтвердил $regadm<br/>";
    }
    if ($preg == 1 && $regadm == "")
    {
        echo "Регистрация без подтверждения<br/>";
    }
}
echo '</div><div class="menu">';
if ($datauser['sex'] == "m")
{
    echo 'Всего пробыл';
}
if ($datauser['sex'] == "zh")
{
    echo 'Всего пробыла';
}
echo ' на сайте: ' . timecount($datauser['total_on_site']) . '</div>';

// Если были нарушения, показываем ссылку на их историю
$req = mysql_query("SELECT * FROM `cms_ban_users` WHERE `user_id`='" . $user . "';");
$res = mysql_num_rows($req);
if ($res > 0)
    echo '<div class="rmenu">Нарушений: <a href="anketa.php?act=ban&amp;user=' . $user . '">' . $res . '</a></div>';

echo '<div class="bmenu"><a href="my_stat.php?id=' . $datauser['id'] . '">Активность юзера</a></div><p>';
if (!empty($_SESSION['uid']))
{
    $contacts = mysql_query("select * from `privat` where me='" . $login . "' and cont='" . $datauser['name'] . "';");
    $conts = mysql_num_rows($contacts);
    if ($conts != 1)
    {
        echo "<a href='cont.php?act=edit&amp;id=" . $user . "&amp;add=1'>Добавить в контакты</a><br/>";
    } else
    {
        echo "<a href='cont.php?act=edit&amp;id=" . $user . "'>Удалить из контактов</a><br/>";
    }
    $igns = mysql_query("select * from `privat` where me='" . $login . "' and ignor='" . $datauser['name'] . "';");
    $ignss = mysql_num_rows($igns);
    if ($igns != 1)
    {
        if ($datauser['rights'] == 0 && $datauser['name'] != $nickadmina && $datauser['name'] != $nickadmina)
        {
            echo "<a href='ignor.php?act=edit&amp;id=" . $user . "&amp;add=1'>Добавить в игнор</a><br/>";
        }
    } else
    {
        echo "<a href='ignor.php?act=edit&amp;id=" . $user . "'>Удалить из игнора</a><br/>";
    }
    echo "<a href='pradd.php?act=write&amp;adr=" . $datauser['id'] . "'>Написать в приват</a></p>";
}

if ($dostmod == 1)
{
    echo '<p>IP: ' . long2ip($datauser['ip']) . '<br/>Browser: ' . $datauser['browser'] . '</p><p>';
    if ($datauser['immunity'] == 1)
    {
        echo '[!]&nbsp;Иммунитет<br />';
    } else
    {
        if ($dostsmod == 1)
        {
            echo "<a href='../" . $admp . "/zaban.php?do=ban&amp;id=" . $datauser['id'] . "'>Банить</a><br/>";
        } elseif ($dostfmod == 1 && isset($_GET['fid']))
        {
            $fid = intval($_GET['fid']);
            echo '<a href="../' . $admp . '/zaban.php?do=ban&amp;id=' . $datauser['id'] . '&amp;fid=' . $fid . '">Пнуть из форума</a><br/>';
        }
    }
    if ($dostadm == "1")
    {
        echo "<a href='../" . $admp . "/editusers.php?act=edit&amp;user=" . $datauser['id'] . "'>Редактировать</a><br/><a href='../" . $admp . "/editusers.php?act=del&amp;user=" . $datauser['id'] . "'>Удалить</a><br/>";
    }
    echo '</p>';
}

require_once ("../incfiles/end.php");

?>