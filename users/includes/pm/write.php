<?php

/**
 * @package     JohnCMS
 * @link        http://johncms.com
 * @copyright   Copyright (C) 2008-2011 JohnCMS Community
 * @license     LICENSE.txt (see attached file)
 * @version     VERSION.txt (see attached file)
 * @author      http://johncms.com/about
 */

defined('_IN_JOHNCMS') or die('Error: restricted access');

$headmod = 'pm_write';
require('../incfiles/head.php');

$error = false;

/*
-----------------------------------------------------------------
Получение данных формы
-----------------------------------------------------------------
*/
$rcp_list = isset($_POST['to']) && !empty($_POST['to']) ? explode(',', trim($_POST['to'])) : array();
$subject = isset($_POST['subject']) ? mb_substr(trim($_POST['subject']), 0, 100) : '';
$message = isset($_POST['message']) ? trim($_POST['message']) : '';
$translit = isset($_POST['translit']);
$attach = isset($_POST['attach']);
$draft = isset($_POST['draft']);

/*
-----------------------------------------------------------------
Обработка списка получателей
-----------------------------------------------------------------
*/
$recipients = array();
// Очищаем каждый элемент массива от пробелов
foreach ($rcp_list as $key => $val) $rcp_list[$key] = trim($val);
// Удаляем дубликаты
$rcp_list = array_unique($rcp_list);
$count = 1;
// Обрабатываем массив со списком получателей
foreach ($rcp_list as $val) {
    // Если превышен лимит на к-во получателей, прекращаем обработку
    if ($count > $max_rcp) break;
    // Не учитываем значения с неправильной длиной
    if (mb_strlen($val) < 2 || mb_strlen($val) > 20) continue;
    // Ищем получателя в базе данных
    $req = mysql_query("SELECT `id`, `name` FROM `users` WHERE `name` = '" . mysql_real_escape_string($val) . "' LIMIT 1");
    if (mysql_num_rows($req)) {
        // Если получатель найден, добавляем в массив списка
        $res = mysql_fetch_assoc($req);
        $recipients[] = htmlspecialchars($res['name']);
        ++$count;
    } else {
        // Если получатель не найден, добавляем запись в массив ошибок
        $error['rcp'][] = htmlspecialchars($val);
    }
}

if (isset($_POST['draft'])) {
    /*
    -----------------------------------------------------------------
    Сохранение черновика
    -----------------------------------------------------------------
    */
    echo '<p>Черновик</p>';
} elseif (isset($_POST['submit'])) {
    /*
    -----------------------------------------------------------------
    Отправка сообщения получателем
    -----------------------------------------------------------------
    */
    echo '<p>Отправлено</p>';
} else {
    /*
    -----------------------------------------------------------------
    Форма для написания сообщения
    -----------------------------------------------------------------
    */
    echo '<div class="phdr"><b><a href="pm.php">' . $lng_pm['my_mail'] . '</a></b> | ' . $lng_pm['write_message'] . '</div>' .
         '<div class="gmenu">' .
         '<form name="form" action="pm.php?act=write" method="post"><p>' .
         '<h3>' . $lng_pm['recipients'] . '</h3>';
    if (isset($error['rcp'])) {
        // Выводим ошибки списка получателей
        foreach ($error['rcp'] as $val) echo '<div><a href=""><b>' . $val . '</b></a> <small>' . core::$lng['error_user_not_exist'] . '</small></div>';
    }

    // Задаем визуальные стили элементов формы
    $style_to = !empty($recipients) ? 'style="background-color: #CCFFCC"' : '';
    if (isset($error['rcp'])) $style_to = 'style="background-color: #FFCCCC"';

    echo '<input name="to" value="' . functions::display_menu($recipients, ', ') . '" ' . $style_to . '/>' .
         '<input type="submit" name="add" value="+" /><br />' .
         '<small><span' . (count($recipients) >= $max_rcp ? ' class="red"' : '') . '>' . $lng_pm['max_recipients'] . ': ' . $max_rcp . '</span></small>' .
         '</p><p>' .
         '<h3>' . $lng_pm['subject'] . '</h3>' .
         '<input name="subject" maxlength="200" value="' . htmlentities($subject, ENT_QUOTES, 'UTF-8') . '"/>' .
         '</p><p>' .
         '<h3>' . core::$lng['message'] . '</h3>' .
         (core::$is_mobile ? '' : bbcode::auto_bb('form', 'message')) .
         (isset($error['body']) ? '<span class="red">' . core::$lng['error'] . ': ' . $error['body'] . '</span><br />' : '') .
         '<textarea rows="' . core::$user_set['field_h'] . '" name="message"' . (isset($error['body']) ? ' style="background-color: #FFCCCC"' : '') . '>' . htmlentities($data['message'], ENT_QUOTES, 'UTF-8') . '</textarea>' .
         '</p><p>' .
         (core::$user_set['translit'] ? '<input type="checkbox" name="translit" value="1" />&nbsp;' . core::$lng['translit'] . '<br />' : '') .
         '<input type="checkbox" name="attach" value="1" />&#160;' . core::$lng['add_file'] . '<br />' .
         '</p><p>' .
         '<input type="submit" name="submit" value="' . core::$lng['sent'] . '"/> ' .
         '<input type="submit" name="draft" value="' . $lng_pm['draft'] . '"/>' .
         '</p></form></div>' .
         '<div class="phdr">' . (core::$user_set['translit'] ? '<a href="../pages/faq.php?act=trans">' . core::$lng['translit'] . '</a> | ' : '') . '<a href="../pages/smileys.php">' . core::$lng['smileys'] . '</a></div>' .
         '<p><a href="' . htmlspecialchars($_SERVER['HTTP_REFERER']) . '">' . core::$lng['back'] . '</a></p>';
}