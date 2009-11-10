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

$headmod = 'userban';
$textl = 'Список нарушений';
require_once ('../incfiles/core.php');
require_once ('../incfiles/head.php');

if ($user == $user_id)
{
    switch ($act)
    {
        case 'name':
            echo "<form action='anketa.php?user=" . $user_id . "&amp;act=editname' method='post'>Изменить имя(max. 15):<br/><input type='text' name='nname' value='" . $arr['imname'] .
                "'/><br/><input type='submit'  value='ok'/></form><br/><a href='anketa.php?user=" . $user_id . "'>Назад</a><br/>";
            break;

        case 'editname':
            $var = check(mb_substr(trim($_POST['nname']), 0, 20));
            mysql_query("UPDATE `users` SET `imname` = '$var' WHERE `id` = '$user_id'");
            echo '<p>Принято: ' . $var . '<br/><a href="anketa.php?user=' . $user_id . '">Продолжить</a></p>';
            break;

        case 'par':
            echo '<div class="phdr">Смена пароля</div>';
            echo '<form action="anketa.php?user=' . $user_id . '&amp;act=editpar" method="post">';
            echo '<div class="menu"><u>Старый пароль</u><br/><input type="text" name="par1"/></div>';
            echo '<div class="menu">Новый пароль:<br/><input type="text" name="par2"/><br/>';
            echo 'Подтвердите пароль:<br/><input type="text" name="par3"/><br/>';
            echo '<small>Мин. 3, макс. 10 символов.<br />Разрешены буквы Латинского алфавита и цифры.</small></div>';
            echo '<div class="bmenu"><input type="submit" value="ok"/></div></form>';
            echo "<br/><a href='anketa.php?user=" . $user_id . "'>Назад</a><br/>";
            break;

        case 'editpar':
            $par1 = check(trim($_POST['par1']));
            $par11 = md5(md5($par1));
            $passw = $arr['password'];
            $par2 = check(trim($_POST['par2']));
            $par3 = check(trim($_POST['par3']));
            $par22 = md5(md5($par2));
            if ($par11 !== $passw)
            {
                echo "Неверно указан текущий пароль<br/><a href='anketa.php?act=par&amp;user=" . $user_id . "'>Повторить</a><br/>";
                require_once ("../incfiles/end.php");
                exit;
            }
            if ($par2 !== $par3)
            {
                echo "Вы ошиблись при подтверждении нового пароля<br/><a href='anketa.php?act=par&amp;user=" . $user_id . "'>Повторить</a><br/>";
                require_once ("../incfiles/end.php");
                exit;
            }
            if ($par2 == "")
            {
                echo "Вы не ввели новый пароль<br/><a href='anketa.php?act=par&amp;user=" . $user_id . "'>Повторить</a><br/>";
                require_once ("../incfiles/end.php");
                exit;
            }
            if (preg_match("/[^\da-zA-Z_]+/", $par2))
            {
                echo "Недопустимые символы в новом пароле<br/><a href='anketa.php?act=par&amp;user=" . $user_id . "'>Повторить</a><br/>";
                require_once ("../incfiles/end.php");
                exit;
            }
            if (strlen($par2) < 3 || strlen($par2) > 10)
            {
                echo "Недопустимая длина нового пароля<br/><a href='anketa.php?act=par&amp;user=" . $user_id . "'>Повторить</a><br/>";
                require_once ("../incfiles/end.php");
                exit;
            }
            mysql_query("UPDATE `users` SET `password` = '$par22' WHERE `id` = '$user_id'");
            echo "Пароль изменен,войдите на сайт заново<br/><a href='../in.php'>Вход</a><br/>";
            unset($_SESSION['uid']);
            unset($_SESSION['ups']);
            setcookie('cuid', '');
            setcookie('cups', '');
            break;

        case 'gor':
            echo "<form action='anketa.php?user=" . $user_id . "&amp;act=editgor' method='post'>Изменить город(max. 20):<br/><input type='text' name='ngor' value='" . $arr['live'] .
                "'/><br/><input type='submit' value='ok'/></form><br/><a href='anketa.php?user=" . $user_id . "'>Назад</a><br/>";
            break;

        case 'editgor':
            $var = check(mb_substr(trim($_POST['ngor']), 0, 20));
            mysql_query("UPDATE `users` SET `live` = '$var' WHERE `id` = '$user_id'");
            echo "Принято: $var<br/><a href='anketa.php?user=" . $user_id . "'>Продолжить</a><br/>";
            break;

        case 'inf':
            echo "<form action='anketa.php?user=" . $user_id . "&amp;act=editinf' method='post'>Изменить инфу(max. 500):<br/><textarea cols=\"20\" rows=\"4\" name=\"ninf\">" . $arr['about'] .
                "</textarea><br/><input type='submit' value='ok'/></form><br/><a href='anketa.php?user=" . $user_id . "'>Назад</a><br/>";
            break;

        case 'editinf':
            $var = check(mb_substr(trim($_POST['ninf']), 0, 500));
            mysql_query("UPDATE `users` SET `about` = '$var' WHERE `id` = '$user_id'");
            echo "Принято: $var<br/><a href='anketa.php?user=" . $user_id . "'>Продолжить</a><br/>";
            break;

        case 'icq':
            echo "<form action='anketa.php?user=" . $user_id . "&amp;act=editicq' method='post'>Изменить ICQ:<br/><input type='text' name='nicq' value='" . $arr['icq'] . "'/><br/><input type='submit' value='ok'/></form><br/><a href='anketa.php?user=" .
                $user_id . "'>Назад</a><br/>";
            break;

        case 'editicq':
            $var = intval(substr($_POST['nicq'], 0, 9));
            mysql_query("UPDATE `users` SET `icq` = '$var' WHERE `id` = '$user_id'");
            echo "Принято: $var<br/><a href='anketa.php?user=" . $user_id . "'>Продолжить</a><br/>";
            break;

        case 'skype':
            echo "<form action='anketa.php?user=" . $user_id . "&amp;act=editskype' method='post'>Изменить Skype(max. 30):<br/><input type='text' name='skype' value='" . $arr['skype'] .
                "'/><br/><input type='submit' value='ok'/></form><br/><a href='anketa.php?user=" . $user_id . "'>Назад</a><br/>";
            break;

        case 'editskype':
            $var = check(mb_substr(trim($_POST['skype']), 0, 30));
            mysql_query("UPDATE `users` SET `skype` = '$var' WHERE `id` = '$user_id'");
            echo "Принято: $var<br/><a href='anketa.php?user=" . $user_id . "'>Продолжить</a><br/>";
            break;

        case 'jabber':
            echo "<form action='anketa.php?user=" . $user_id . "&amp;act=editjabber' method='post'>Изменить Jabber(max. 30):<br/><input type='text' name='jabber' value='" . $arr['jabber'] .
                "'/><br/><input type='submit' value='ok'/></form><br/><a href='anketa.php?user=" . $user_id . "'>Назад</a><br/>";
            break;

        case 'editjabber':
            $var = check(mb_substr(trim($_POST['jabber']), 0, 30));
            mysql_query("UPDATE `users` SET `jabber` = '$var' WHERE `id` = '$user_id'");
            echo "Принято: $var<br/><a href='anketa.php?user=" . $user_id . "'>Продолжить</a><br/>";
            break;

        case 'mobila':
            echo "<form action='anketa.php?user=" . $user_id . "&amp;act=editmobila' method='post'>Изменить номер телефона (max.20):<br/><input type='text' name='nmobila' value='" . $arr['mibile'] .
                "' /><br/><input type='submit' value='ok'/></form><br/><a href='anketa.php?user=" . $user_id . "'>Назад</a><br/>";
            break;

        case 'editmobila':
            $var = check(mb_substr(trim($_POST['nmobila']), 0, 20));
            mysql_query("update `users` set `mibile` = '$var' where id='$user_id'");
            echo "Принято: $var<br/><a href='anketa.php?user=" . $user_id . "'>Продолжить</a><br/>";
            break;

        case 'dr':
            echo "<form action='anketa.php?user=" . $user_id . "&amp;act=editdr' method='post'>Изменить дату рождения:<br/><select name='user_day' class='textbox'><option>$arr[dayb]</option>";
            $i = 1;
            while ($i <= 31)
            {
                echo "<option value='" . $i . "'>$i</option>";
                ++$i;
            }
            $mnt = $arr['monthb'];
            echo "</select><select name='user_month' class='textbox'><option value='" . $mnt . "'>$mesyac[$mnt]</option>";
            $i = 1;
            while ($i <= 12)
            {
                echo "<option value='" . $i . "'>$mesyac[$i]</option>";
                ++$i;
            }
            echo "</select><select name='user_year' class='textbox'><option>$arr[yearofbirth]</option>";
            $i = 1950;
            while ($i <= 2000)
            {
                echo "<option value='" . $i . "'>$i</option>";
                ++$i;
            }
            echo "</select><br/><input type='submit' value='ok'/></form><br/><a href='anketa.php?user=" . $user_id . "'>Назад</a><br/>";
            break;

        case 'editdr':
            $user_day = intval($_POST['user_day']);
            $user_month = intval($_POST['user_month']);
            $user_year = intval($_POST['user_year']);
            mysql_query("update `users` set dayb='" . $user_day . "', monthb='" . $user_month . "' ,yearofbirth='" . $user_year . "' where id='" . $_SESSION['uid'] . "';");
            echo "Принято: $user_day $mesyac[$user_month] $user_year<br/><a href='anketa.php?user=" . $user_id . "'>Продолжить</a><br/>";
            break;

        case 'site':
            echo "<form action='anketa.php?user=" . $user_id . "&amp;act=editsite' method='post'>Изменить сайт(max. 50):<br/><input type='text' name='nsite' value='" . (empty($arr['www']) ? 'http://' : $arr['www']) .
                "'/><br/><input type='submit' value='ok'/></form><br/><a href='anketa.php?user=" . $user_id . "'>Назад</a><br/>";
            break;

        case 'editsite':
            $var = check(mb_substr(trim($_POST['nsite']), 0, 50));
            mysql_query("UPDATE `users` SET `www` = '$var' WHERE `id` = '$user_id'");
            echo "Принято: $var<br/><a href='anketa.php?user=" . $user_id . "'>Продолжить</a><br/>";
            break;

        case 'mail':
            if ($arr['mailact'] == 0)
            {
                echo 'Ваш адрес e-mail необходимо<a href="anketa.php?act=activmail&amp;user=' . $user_id . '"> активировать</a><br/>
(<a href="anketa.php?act=helpactiv&amp;user=' . $user_id . '">Зачем это нужно?</a>)<br/>';
            }
            echo "<form action='anketa.php?user=" . $user_id . "&amp;act=editmail' method='post'>Изменить E-mail(max. 50):<br/><input type='text' name='nmail' value='" . $arr['mail'] . "'/><br/>";
            if ($arr['mailact'] == 1)
            {
                switch ($arr['mailvis'])
                {
                    case 1:
                        echo "<input type='checkbox' name='nmailvis' value='0'/>Скрыть<br/>";
                        break;
                    case 0:
                        echo "<input type='checkbox' name='nmailvis' value='1'/>Показать<br/>";
                        break;
                }
            }
            echo "<input type='submit' value='ok'/></form><br/><a href='anketa.php?user=" . $user_id . "'>Назад</a><br/>";
            if ($arr['mailact'] == 0)
            {
                echo "<a href='anketa.php?act=activmail&amp;user=" . $user_id . "&amp;continue'>Продолжить активацию</a><br/>";
            }
            break;

        case 'helpactiv':
            include ("../pages/actmail.$ras_pages");
            echo "<a href='anketa.php?act=mail'>Назад</a><br/>";
            break;

        case 'editmail':
            $nmail = htmlspecialchars($_POST['nmail']);
            if (!eregi("^[a-z0-9\._-]+@[a-z0-9\._-]+\.[a-z]{2,4}\$", $nmail))
            {
                echo "Некорректный формат e-mail адреса!";
                echo "<a href='anketa.php?action=mail'>Повторить</a><br/>";
                require_once ("../incfiles/end.php");
                exit;
            }
            $nmail = mb_substr($nmail, 0, 50);
            $nmailvis = intval($_POST['nmailvis']);
            if ($nmail != $arr['mail'])
            {
                $nmailact = 0;
            } else
            {
                $nmailact = $arr['mailact'];
            }
            mysql_query("UPDATE `users` SET
				`mail`='" . mysql_real_escape_string($nmail) . "',
				`mailvis`='" . $nmailvis . "',
				`mailact`='" . $nmailact . "'
				where `id`='" . $user_id . "';");
            echo "Принято: $nmail<br/><a href='anketa.php?user=" . $user_id . "'>Продолжить</a><br/>";
            break;

        case 'activmail':
            if (isset($_GET['continue']))
            {
                if (isset($_POST['submit']))
                {
                    if (intval($_POST['provact']) == $arr[kod])
                    {

                        mysql_query("update `users` set `mailact`='1' where `id`='" . $user_id . "';");
                        unset($_SESSION['activ']);
                        echo "E-mail адрес успешно активирован<br/>";
                        echo "<a href='anketa.php?user=" . $user_id . "'>В анкету</a><br/>";
                    } else
                    {
                        echo "Неверный код<br/>";
                        echo "<a href='anketa.php?act=activmail&amp;user=" . $user_id . "&amp;continue'>Повторить</a><br/>";
                    }
                } else
                {
                    echo "<form action='anketa.php?user=" . $user_id .
                        "&amp;act=activmail&amp;continue' method='post'>Код активации:<br/><input type='text' name='provact'/><br/><input type='submit' name='submit' value='ok'/></form><br/><a href='anketa.php?user=" . $user_id . "'>Назад</a><br/>";
                }
                require ("../incfiles/end.php");
                exit;
            }
            if ($_SESSION['activ'] != 1)
            {
                require_once ('../incfiles/char.php');
                $mailcode = rand(100000, 999999);
                $subject = "E-mail activation";
                $mail = "Здравствуйте " . $login . "\r\nКод для активации e-mail адреса " . $mailcode . "\r\nТеперь Вы можете продолжить активацию\r\n";
                $subject = utfwin($subject);
                $name = utfwin($name);
                $mail = utfwin($mail);
                $name = convert_cyr_string($name, 'w', 'k');
                $subject = convert_cyr_string($subject, 'w', 'k');
                $mail = convert_cyr_string($mail, 'w', 'k');
                $adds = "From: <" . $emailadmina . ">\n";
                $adds .= "X-sender: <" . $emailadmina . ">\n";
                $adds .= "Content-Type: text/plain; charset=koi8-r\n";
                $adds .= "MIME-Version: 1.0\r\n";
                $adds .= "Content-Transfer-Encoding: 8bit\r\n";
                $adds .= "X-Mailer: PHP v." . phpversion();
                mail($arr['mail'], $subject, $mail, $adds);
                mysql_query("update `users` set `kod`='" . $mailcode . "' where `id`='" . $user_id . "';");
                echo 'Код для активации выслан по указанному адресу<br/>';
                $_SESSION['activ'] = 1;
            } else
            {
                echo "Код для активации уже выслан<br/>";
            }
            echo "<a href='anketa.php?user=" . $user_id . "'>В анкету</a><br/>";
            break;

        default:
            ////////////////////////////////////////////////////////////
            // Личная анкета                                          //
            ////////////////////////////////////////////////////////////
            echo '<div class="phdr">Моя анкета</div>';
            echo '<div class="menu">Ник: <b>' . $login . '</b></div>';
            echo '<div class="menu">Логин: <b>' . $arr['name_lat'] . '</b></div>';
            echo '<div class="menu">ID: <b>' . $arr['id'] . '</b></div>';
            echo '<div class="menu">Зарегистрирован' . ($arr['sex'] == 'm' ? '' : 'а') . ': ' . date("d.m.Y", $arr['datereg']) . '</div>';
            echo '<div class="bmenu">Личные данные</div>';
            echo '<div class="menu"><a href="anketa.php?act=name">Имя:</a> ' . $arr['imname'] . '</div>';
            echo '<div class="menu"><a href="anketa.php?act=dr">Дата рождения:</a> ' . $arr['dayb'] . ' ' . $mesyac[$arr['monthb']] . ' ' . $arr['yearofbirth'] . '</div>';
            echo '<div class="menu"><a href="anketa.php?act=gor">Город:</a> ' . $arr['live'] . '</div>';
            echo '<div class="menu"><a href="anketa.php?act=inf">О себе:</a> ' . smileys(tags($arr['about'])) . '</div>';
            echo '<div class="bmenu">Связь</div>';
            echo '<div class="menu"><a href="anketa.php?act=icq">ICQ:</a> ' . $arr['icq'] . '</div>';
            echo '<div class="menu"><a href="anketa.php?act=skype">Skype:</a> ' . $arr['skype'] . '</div>';
            echo '<div class="menu"><a href="anketa.php?act=jabber">Jabber:</a> ' . $arr['jabber'] . '</div>';
            echo '<div class="menu"><a href="anketa.php?act=mail">E-mail:</a> ' . $arr['mail'] . ($arr['mailact'] == 0 ? '(!)' : '') . '</div>';
            echo '<div class="menu"><a href="anketa.php?act=mobila">Телефон:</a> ' . $arr['mibile'] . '</div>';
            echo '<div class="menu"><a href="anketa.php?act=site">Сайт:</a> ' . tags($arr['www']) . '</div>';
            echo '<div class="bmenu">Всего пробыл' . ($arr['sex'] == 'm' ? '' : 'а') . ' на сайте: ' . timecount($arr['total_on_site']) . '</div>';
            echo '<p>';
            $req = mysql_query("select * from `gallery` where `type`='al' and `user`='1' and `avtor`='" . $arr['name'] . "' LIMIT 1;");
            if (mysql_num_rows($req) != 0)
            {
                $res = mysql_fetch_array($req);
                echo '<a href="../gallery/index.php?id=' . $res['id'] . '">Личный альбом</a><br />';
            }
            if ($dostadm == 1)
            {
                echo "<a href='../" . $admp . "/editusers.php?act=edit&amp;user=" . $user_id . "'>Редактировать анкету</a><br/>";
            }
            echo '<div><a href="anketa.php?act=par&amp;user=' . $user_id . '">Сменить пароль</a></div>';
            echo '</p>';
            require_once ("../incfiles/end.php");
            exit;
            break;
    }
}

require_once ('../incfiles/end.php');

?>