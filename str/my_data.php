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

if ($id && $dostadm && $act == 'reset')
{
    // Сброс настроек
    mysql_query("UPDATE `users` SET `set_user` = '', `set_forum` = '', `set_chat` = '' WHERE `id` = '" . $user['id'] . "'");
    echo '<div class="gmenu"><p>Для пользователя <b>' . $user['name'] . '</b> установлены настройки по умолчанию<br /><a href="anketa.php?id=' . $user['id'] . '">В анкету</a></p></div>';
    require_once ('../incfiles/end.php');
    exit;
}
//TODO: Добавить имя того, кого редактируем
echo '<div class="phdr"><b>Редактирование ' . ($id && $id != $user_id ? '' : 'личной ') . 'анкеты</b></div>';
if (isset($_POST['submit']))
{
    $error = array();
    // Данные юзера
    $user['imname'] = isset($_POST['imname']) ? check(mb_substr($_POST['imname'], 0, 25)) : '';
    $user['live'] = isset($_POST['live']) ? check(mb_substr($_POST['live'], 0, 50)) : '';
    $user['dayb'] = isset($_POST['dayb']) ? intval($_POST['dayb']) : 0;
    $user['monthb'] = isset($_POST['monthb']) ? intval($_POST['monthb']) : 0;
    $user['yearofbirth'] = isset($_POST['yearofbirth']) ? intval($_POST['yearofbirth']) : 0;
    $user['about'] = isset($_POST['about']) ? check(mb_substr($_POST['about'], 0, 500)) : '';
    $user['mibile'] = isset($_POST['mibile']) ? check(mb_substr($_POST['mibile'], 0, 40)) : '';
    $user['mail'] = isset($_POST['mail']) ? check(mb_substr($_POST['mail'], 0, 40)) : '';
    $user['mailvis'] = isset($_POST['mailvis']) ? 1 : 0;
    $user['icq'] = isset($_POST['icq']) ? intval($_POST['icq']) : 0;
    $user['skype'] = isset($_POST['skype']) ? check(mb_substr($_POST['skype'], 0, 40)) : '';
    $user['jabber'] = isset($_POST['jabber']) ? check(mb_substr($_POST['jabber'], 0, 40)) : '';
    $user['www'] = isset($_POST['www']) ? check(mb_substr($_POST['www'], 0, 40)) : '';
    // Данные юзера (для Администраторов)
    $user['immunity'] = isset($_POST['immunity']) ? 1 : 0;
    $user['sex'] = isset($_POST['sex']) && $_POST['sex'] == 'm' ? 'm' : 'zh';
    $user['rights'] = isset($_POST['rights']) ? abs(intval($_POST['rights'])) : 0;
    if ($user['id'] == $user_id) // Запрещаем менять самому себе должность
        $user['rights'] = $datauser['rights'];
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
        `mailvis` = '" . $user['mailvis'] . "',
        `icq` = '" . $user['icq'] . "',
        `skype` = '" . $user['skype'] . "',
        `jabber` = '" . $user['jabber'] . "',
        `www` = '" . $user['www'] . "'
        WHERE `id` = '" . $user['id'] . "' LIMIT 1");
        if ($dostadm)
        {
            mysql_query("UPDATE `users` SET
            `immunity` = '" . $user['immunity'] . "',
            `sex` = '" . $user['sex'] . "',
            `rights` = '" . $user['rights'] . "'
            WHERE `id` = '" . $user['id'] . "' LIMIT 1");
        }
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
echo '<li><u>E-mail</u><br />Внимание! Правильно указывайте свой адрес электронной почты!<br />Именно на него будет высылаться Ваш пароль.<br />';
echo '<input type="text" value="' . $user['mail'] . '" name="mail" /><br />';
echo '<input name="mailvis" type="checkbox" value="1" ' . ($user['mailvis'] ? 'checked="checked"' : '') . ' />&nbsp;Показывать в Анкете</li>';
echo '<li><u>ICQ</u><br /><input type="text" value="' . $user['icq'] . '" name="icq" size="10" maxlength="10" /></li>';
echo '<li><u>Skype</u><br /><input type="text" value="' . $user['skype'] . '" name="skype" /></li>';
echo '<li><u>Jabber</u><br /><input type="text" value="' . $user['jabber'] . '" name="jabber" /></li>';
echo '<li><u>Сайт</u><br /><input type="text" value="' . $user['www'] . '" name="www" /></li>';
echo '</ul></p></div>';
// Административные функции
if ($dostadm)
{
    echo '<div class="rmenu"><p><h3><img src="../images/settings.png" width="16" height="16" class="left" />&nbsp;Настройки</h3><ul>';
    if ($dostsadm)
        echo '<li><input name="immunity" type="checkbox" value="1" ' . ($user['immunity'] ? 'checked="checked"' : '') . ' />&nbsp;<span class="green"><b>Иммунитет</b></span></li>';
    echo '<li><a href="my_pass.php?id=' . $user['id'] . '">Сменить пароль</a></li>';
    echo '<li><a href="my_data.php?act=reset&amp;id=' . $user['id'] . '">Сбросить настройки</a></li>';
    echo '<li>Укажите пол:<br />';
    echo '<input type="radio" value="m" name="sex" ' . ($user['sex'] == 'm' ? 'checked="checked"' : '') . '/>&nbsp;Мужской<br />';
    echo '<input type="radio" value="zh" name="sex" ' . ($user['sex'] == 'zh' ? 'checked="checked"' : '') . '/>&nbsp;Женский</li>';
    echo '</ul></p>';
    if ($user['id'] != $user_id)
    {
        echo '<p><h3><img src="../images/admin.png" width="16" height="16" class="left" />&nbsp;Должность на сайте</h3><ul>';
        echo '<input type="radio" value="0" name="rights" ' . (!$user['rights'] ? 'checked="checked"' : '') . '/>&nbsp;<b>Обычный юзер</b><br />';
        echo '<input type="radio" value="1" name="rights" ' . ($user['rights'] == 1 ? 'checked="checked"' : '') . '/>&nbsp;Киллер<br />';
        echo '<input type="radio" value="2" name="rights" ' . ($user['rights'] == 2 ? 'checked="checked"' : '') . '/>&nbsp;Модер чата<br />';
        echo '<input type="radio" value="3" name="rights" ' . ($user['rights'] == 3 ? 'checked="checked"' : '') . '/>&nbsp;Модер форума<br />';
        echo '<input type="radio" value="4" name="rights" ' . ($user['rights'] == 4 ? 'checked="checked"' : '') . '/>&nbsp;Модер по загрузкам<br />';
        echo '<input type="radio" value="5" name="rights" ' . ($user['rights'] == 5 ? 'checked="checked"' : '') . '/>&nbsp;Модер библиотеки<br />';
        echo '<input type="radio" value="6" name="rights" ' . ($user['rights'] == 6 ? 'checked="checked"' : '') . '/>&nbsp;Супермодератор<br />';
        if ($dostsadm)
        {
            echo '<input type="radio" value="7" name="rights" ' . ($user['rights'] == 7 ? 'checked="checked"' : '') . '/>&nbsp;Администратор<br />';
            echo '<input type="radio" value="9" name="rights" ' . ($user['rights'] == 9 ? 'checked="checked"' : '') . '/>&nbsp;<span class="red"><b>Супервизор</b></span><br />';
        }
        echo '</ul></p>';
    }
    echo '</div>';
}
echo '<div class="gmenu"><input type="submit" value="Сохранить" name="submit" /></div>';
echo '</form>';
echo '<div class="phdr">&nbsp;</div>';
echo '<p><a href="anketa.php' . ($id ? '?id=' . $id : '') . '">В анкету</a></p>';

require_once ('../incfiles/end.php');

?>