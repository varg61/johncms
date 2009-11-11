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

$headmod = 'mystat';
$textl = 'Личная статистика';
require_once ('../incfiles/core.php');
require_once ('../incfiles/head.php');

$user = $id ? $id : $user_id;
if (!$user_id)
{
    header('Location: ../index.php');
    exit;
}

$req = mysql_query("SELECT * FROM `users` WHERE `id` = '$user' LIMIT 1");
if (mysql_num_rows($req))
{
    $res = mysql_fetch_assoc($req);
    echo '<div class="phdr"><b>Статистика</b></div>';
    echo '<div class="menu"><p><h3><img src="../images/rate.gif" width="16" height="16" class="left" />&nbsp;' . ($id ? 'А' : 'Моя а') . 'ктивность</h3><ul>';
    echo '<li>Сообщений в Форуме: ' . $res['postforum'] . '</li>';
    //TODO: Дописать статистику по гостевой
    echo '<li>Сообщений в Гостевой: ' . $res['postguest'] . '</li>';
    echo '<li>Сообщений в Чате: ' . $res['postchat'] . '</li>';
    echo '<li>Ответов в Викторине: ' . $res['otvetov'] . '</li>';
    echo '<li>Игровой баланс: ' . $res['balans'] . '</li>';
    echo '<li>Комментариев: ' . $res['komm'] . '</li>';
    echo '</ul></p></div>';
    // Если были нарушения, то показываем их
    if ($total = mysql_result(mysql_query("SELECT COUNT(*) FROM `cms_ban_users` WHERE `user_id` = '$user'"), 0))
        echo '<div class="rmenu">Нарушения: <a href="anketa.php?act=ban&amp;user=' . $user . '">' . $total . '</a></div>';
    echo '<div class="phdr"><a href="users_top.php">Топ 10 активности</a></div>';
} else
{
    echo display_error('Такого пользователя нет');
}
echo '<p><a href="../index.php?mod=cab">В кабинет</a></p>';

require_once ('../incfiles/end.php');

?>