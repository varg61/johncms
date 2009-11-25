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
$textl = 'Редактирование Анкеты';
require_once ('../incfiles/core.php');
require_once ('../incfiles/head.php');

if (!$user_id)
{
    display_error('Только для зарегистрированных посетителей');
    require_once ('../incfiles/end.php');
    exit;
}

if ($id && $id != $user_id && $dostadm)
{
    // Если был запрос на юзера, то получаем его данные
    $req = mysql_query("SELECT * FROM `users` WHERE `id` = '$id' LIMIT 1");
    if (mysql_num_rows($req))
    {
        $user = mysql_fetch_assoc($req);
        if ($user['rights'] > $datauser['rights'])
        {
            // Если не хватает прав, выводим ошибку
            echo display_error('Вы не можете редактировать анкету старшего Вас по должности');
            require_once ('../incfiles/end.php');
            exit;
        }
    } else
    {
        echo display_error('Такого пользователя не существует');
        require_once ('../incfiles/end.php');
        exit;
    }
} else
{
    $id = false;
    $user = $datauser;
}
//TODO: Добавить имя того, кого редактируем
echo '<div class="phdr"><b>Редактирование ' . ($id && $id != $user_id ? '' : 'личной ') . 'анкеты</b></div>';
if (isset($_POST['submit']))
{
    $error = array();
    $user['imname'] = isset($_POST['imname']) ? check(mb_substr($_POST['imname'], 0, 25)) : '';
    $user['live'] = isset($_POST['live']) ? check(mb_substr($_POST['live'], 0, 50)) : '';
    $user['dayb'] = isset($_POST['dayb']) ? intval($_POST['dayb']) : 0;
    $user['monthb'] = isset($_POST['monthb']) ? intval($_POST['monthb']) : 0;
    $user['yearofbirth'] = isset($_POST['yearofbirth']) ? intval($_POST['yearofbirth']) : 0;
    $user['about'] = isset($_POST['about']) ? check(mb_substr($_POST['about'], 0, 500)) : '';
    $user['mibile'] = isset($_POST['mibile']) ? check(mb_substr($_POST['mibile'], 0, 40)) : '';
    //TODO: Разобраться с проверкой майла
    $user['mail'] = isset($_POST['mail']) ? check(mb_substr($_POST['mail'], 0, 40)) : '';
    $user['icq'] = isset($_POST['icq']) ? intval($_POST['icq']) : 0;
    $user['skype'] = isset($_POST['skype']) ? check(mb_substr($_POST['skype'], 0, 40)) : '';
    $user['jabber'] = isset($_POST['jabber']) ? check(mb_substr($_POST['jabber'], 0, 40)) : '';
    $user['www'] = isset($_POST['www']) ? check(mb_substr($_POST['www'], 0, 40)) : '';
    if ($user['dayb'] || $user['monthb'] || $user['yearofbirth'])
    {
        if ($user['dayb'] < 1 || $user['dayb'] > 31 || $user['monthb'] < 1 || $user['monthb'] > 12)
            $error[] = 'Дата рождения указана неправильно';
    }
    if ($user['icq'] && ($user['icq'] < 10000 || $user['icq'] > 999999999))
        $error[] = 'Номер ICQ должен состоять минимум из 5 цифр и максимум из 10';
    if (!$error)
    {
        mysql_query("UPDATE `users` SET
        `imname` = '" . $user['imname'] . "',
        `live` = '" . $user['live'] . "',
        `dayb` = '" . $user['dayb'] . "',
        `monthb` = '" . $user['monthb'] . "',
        `yearofbirth` = '" . $user['yearofbirth'] . "',
        `about` = '" . $user['about'] . "',
        `mibile` = '" . $user['mibile'] . "',
        `mail` = '" . $user['mail'] . "',
        `icq` = '" . $user['icq'] . "',
        `skype` = '" . $user['skype'] . "',
        `jabber` = '" . $user['jabber'] . "',
        `www` = '" . $user['www'] . "'
        WHERE `id` = '" . $user['id'] . "'");
        echo '<div class="gmenu">Данные сохранены</div>';
    } else
    {
        echo display_error($error);
    }
}
echo '<form action="my_data.php?id=' . $user['id'] . '" method="post"><div class="menu">';
// Личные данные
echo '<p><h3><img src="../images/contacts.png" width="16" height="16" class="left" />&nbsp;Личные данные</h3><ul>';
echo '<li><u>Имя</u><br /><input type="text" value="' . $user['imname'] . '" name="imname" /></li>';
echo '<li><u>Дата рождения</u> (д.м.г)<br />';
echo '<input type="text" value="' . $user['dayb'] . '" size="2" maxlength="2" name="dayb" />.';
echo '<input type="text" value="' . $user['monthb'] . '" size="2" maxlength="2" name="monthb" />.';
echo '<input type="text" value="' . $user['yearofbirth'] . '" size="4" maxlength="4" name="yearofbirth" /></li>';
echo '<li><u>Город</u><br /><input type="text" value="' . $user['live'] . '" name="live" /></li>';
echo '<li><u>О себе</u><br /><textarea cols="20" rows="4" name="about">' . str_replace('<br />', "\r\n", $user['about']) . '</textarea></li>';
echo '</ul></p>';
// Связь
echo '<p><h3><img src="../images/mail.png" width="16" height="16" class="left" />&nbsp;Связь</h3><ul>';
echo '<li><u>Тел. номер</u><br /><input type="text" value="' . $user['mibile'] . '" name="mibile" /></li>';
echo '<li><u>E-mail</u><br /><input type="text" value="' . $user['mail'] . '" name="mail" /></li>';
echo '<li><u>ICQ</u><br /><input type="text" value="' . $user['icq'] . '" name="icq" size="10" maxlength="10" /></li>';
echo '<li><u>Skype</u><br /><input type="text" value="' . $user['skype'] . '" name="skype" /></li>';
echo '<li><u>Jabber</u><br /><input type="text" value="' . $user['jabber'] . '" name="jabber" /></li>';
echo '<li><u>Сайт</u><br /><input type="text" value="' . $user['www'] . '" name="www" /></li>';
echo '</ul></p></div>';
// Административные функции
if ($dostadm)
{
    echo '<div class="rmenu">';
    echo '</div>';
}
echo '<div class="gmenu"><input type="submit" value="Сохранить" name="submit" /></div>';
echo '</form>';
echo '<div class="phdr">&nbsp;</div>';
echo '<p><a href="anketa.php' . ($id ? '?id=' . $id : '') . '">В анкету</a></p>';
require_once ('../incfiles/end.php');

if ($user111)
{
    switch ($act)
    {
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

?>