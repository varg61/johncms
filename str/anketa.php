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
require_once ('../incfiles/head.php');

$tti = $realtime;
if ($user_id)
{
    $user = isset($_GET['user']) ? intval($_GET['user']) : $user_id;
    $q = @mysql_query("select * from `users` where id='" . $user . "';");
    $arr = @mysql_fetch_array($q);
    $arr2 = mysql_num_rows($q);
    if ($arr2 == 0)
    {
        echo "Пользователя с таким id не существует!<br/>";
        require_once ("../incfiles/end.php");
        exit;
    }
    ////////////////////////////////////////////////////////////
    // Выводим анкету пользователя                            //
    ////////////////////////////////////////////////////////////
    if ($act == "")
    {
        echo '<div class="phdr">Анкета</div><div class="menu">';
        if ($arr['dayb'] == $day && $arr['monthb'] == $mon)
        {
            echo '<div class="gmenu">ИМЕНИНЫ!!!</div>';
        }
        echo '<img src="../theme/' . $set_user['skin'] . '/images/' . ($arr['sex'] == 'm' ? 'm' : 'f') . ($arr['datereg'] > $realtime - 86400 ? '_new.gif" width="20"' : '.gif" width="16"') . ' height="16"/>&nbsp;';
        echo '<b>' . $arr['name'] . '</b> (id: ' . $arr['id'] . ')';
        $ontime = $arr['lastdate'];
        $ontime2 = $ontime + 300;
        $preg = $arr['preg'];
        $regadm = $arr['regadm'];
        if ($realtime > $ontime2)
        {
            echo '<span class="red"> [Off]</span>';
            if ($arr['sex'] == "m")
            {
                $lastvisit = 'был: ';
            }
            if ($arr['sex'] == "zh")
            {
                $lastvisit = 'была: ';
            }
            $lastvisit = $lastvisit . date("d.m.Y (H:i)", $arr['lastdate']);
        } else
        {
            echo '<span class="green"> [ON]</span>';
        }
        if (!empty($arr['status']))
            echo '<br /><img src="../images/star.gif" alt=""/>&nbsp;<span class="status">' . $arr['status'] . '</span>';
        echo '</div>';
        echo '<div class="menu"><u>Логин</u>: <b>' . $arr['name_lat'] . '</b></div>';
        if ($arr['rights'] != 0)
        {
            echo '<div class="menu"><u>Должность</u>: ';
            switch ($arr['rights'])
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
        echo '<div class="menu"><u>Имя</u>: ' . $arr['imname'] . '</div>';
        if (!empty($arr['dayb']))
        {
            echo '<div class="menu"><u>Дата рождения</u>: ' . $arr['dayb'] . '&nbsp;' . $mesyac[$arr['monthb']] . '&nbsp;' . $arr['yearofbirth'] . '</div>';
        }
        if (!empty($arr['live']))
        {
            echo '<div class="menu"><u>Город</u>: ' . $arr['live'] . '</div>';
        }
        if (!empty($arr['about']))
        {
            echo '<div class="menu"><u>О себе</u>: ' . smileys(tags($arr['about'])) . '</div>';
        }
        $req = mysql_query("select * from `gallery` where `type`='al' and `user`=1 and `avtor`='" . $arr['name'] . "' LIMIT 1;");
        if (mysql_num_rows($req) == 1)
        {
            $res = mysql_fetch_array($req);
            echo '<div class="gmenu"><a href="../gallery/index.php?id=' . $res['id'] . '">Личный альбом</a></div>';
        }
        echo '<div class="bmenu">Связь</div>';
        if (!empty($arr['mibile']))
            echo '<div class="menu"><u>Тел. номер</u>: ' . $arr['mibile'] . '</div>';
        if ($arr['mailact'] == 1)
        {
            if (!empty($arr['mail']))
            {
                echo '<div class="menu"><u>E-mail</u>: ';
                if ($arr['mailvis'] == 1)
                {
                    echo $arr['mail'] . '</div>';
                } else
                {
                    echo 'скрыт</div>';
                }
            }
        }
        if (!empty($arr['icq']))
            echo '<div class="menu"><u>ICQ</u>:&nbsp;<img src="http://web.icq.com/whitepages/online?icq=' . $arr['icq'] . '&amp;img=5" width="12" height="12" alt=""/>&nbsp;' . $arr['icq'] . '</div> ';
        if (!empty($arr['skype']))
            echo '<div class="menu"><u>Skype</u>:&nbsp;' . $arr['skype'] . '</div> ';
        if (!empty($arr['jabber']))
            echo '<div class="menu"><u>Jabber</u>:&nbsp;' . $arr['jabber'] . '</div> ';
        if (!empty($arr['www']))
        {
            //$sait = str_replace("http://", "", $arr['www']);
            echo '<div class="menu"><u>Сайт</u>: ' . tags($arr['www']) . '</div>';
        }
        echo '<div class="bmenu">Статистика</div><div class="menu">';
        if ($arr['sex'] == "m")
        {
            echo "Зарегистрирован";
        }
        if ($arr['sex'] == "zh")
        {
            echo "Зарегистрирована";
        }
        echo ': ' . date("d.m.Y", $arr['datereg']);
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
        if ($arr['sex'] == "m")
        {
            echo 'Всего пробыл';
        }
        if ($arr['sex'] == "zh")
        {
            echo 'Всего пробыла';
        }
        echo ' на сайте: ' . timecount($arr['total_on_site']) . '</div>';

        // Если были нарушения, показываем ссылку на их историю
        $req = mysql_query("SELECT * FROM `cms_ban_users` WHERE `user_id`='" . $user . "';");
        $res = mysql_num_rows($req);
        if ($res > 0)
            echo '<div class="rmenu">Нарушений: <a href="anketa.php?act=ban&amp;user=' . $user . '">' . $res . '</a></div>';

        echo '<div class="bmenu"><a href="anketa.php?act=statistic&amp;user=' . $arr['id'] . '">Активность юзера</a></div><p>';
        if (!empty($_SESSION['uid']))
        {
            $contacts = mysql_query("select * from `privat` where me='" . $login . "' and cont='" . $arr['name'] . "';");
            $conts = mysql_num_rows($contacts);
            if ($conts != 1)
            {
                echo "<a href='cont.php?act=edit&amp;id=" . $user . "&amp;add=1'>Добавить в контакты</a><br/>";
            } else
            {
                echo "<a href='cont.php?act=edit&amp;id=" . $user . "'>Удалить из контактов</a><br/>";
            }
            $igns = mysql_query("select * from `privat` where me='" . $login . "' and ignor='" . $arr['name'] . "';");
            $ignss = mysql_num_rows($igns);
            if ($igns != 1)
            {
                if ($arr['rights'] == 0 && $arr['name'] != $nickadmina && $arr['name'] != $nickadmina)
                {
                    echo "<a href='ignor.php?act=edit&amp;id=" . $user . "&amp;add=1'>Добавить в игнор</a><br/>";
                }
            } else
            {
                echo "<a href='ignor.php?act=edit&amp;id=" . $user . "'>Удалить из игнора</a><br/>";
            }
            echo "<a href='pradd.php?act=write&amp;adr=" . $arr['id'] . "'>Написать в приват</a></p>";
        }

        if ($dostmod == 1)
        {
            echo '<p>IP: ' . long2ip($arr['ip']) . '<br/>Browser: ' . $arr['browser'] . '</p><p>';
            if ($arr['immunity'] == 1)
            {
                echo '[!]&nbsp;Иммунитет<br />';
            } else
            {
                if ($dostsmod == 1)
                {
                    echo "<a href='../" . $admp . "/zaban.php?do=ban&amp;id=" . $arr['id'] . "'>Банить</a><br/>";
                } elseif ($dostfmod == 1 && isset($_GET['fid']))
                {
                    $fid = intval($_GET['fid']);
                    echo '<a href="../' . $admp . '/zaban.php?do=ban&amp;id=' . $arr['id'] . '&amp;fid=' . $fid . '">Пнуть из форума</a><br/>';
                }
            }
            if ($dostadm == "1")
            {
                echo "<a href='../" . $admp . "/editusers.php?act=edit&amp;user=" . $arr['id'] . "'>Редактировать</a><br/><a href='../" . $admp . "/editusers.php?act=del&amp;user=" . $arr['id'] . "'>Удалить</a><br/>";
            }
            echo '</p>';
        }
    }
} else
{
    echo "Вы не авторизованы!<br/>";
}

require_once ("../incfiles/end.php");

?>