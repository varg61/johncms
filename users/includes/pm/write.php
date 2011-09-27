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

require('../incfiles/head.php');

/*
-----------------------------------------------------------------
Отправка сообщения
-----------------------------------------------------------------
*/
$data = $pm->get_vars();
$error = false;
if (isset($_POST['submit']) && !isset($data['error']) && ($error = $pm->sent_message($data)) === false) {
    echo '<p>Отправлено</p>';
} else {
    // Выводим форму для написания письма
    echo '<div class="phdr"><b><a href="pm.php">' . $lng_pm['my_mail'] . '</a></b> | Написать письмо</div>' .
         '<div class="gmenu">' . $pm->message_write('pm.php?act=write', $data, $error) . '</div>' .
         '<div class="phdr"><a href="../pages/faq.php?act=trans">Транслит</a> | <a href="../pages/faq.php?act=smileys">Смайлы</a></div>' .
         '<p><a href="' . htmlspecialchars($_SERVER['HTTP_REFERER']) . '">Назад</a></p>';
}