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

/*
-----------------------------------------------------------------
Отправка сообщения
-----------------------------------------------------------------
*/
$error = false;
$data = $pm::get_vars();
if (isset($_POST['submit']) && !isset($data['error']) && ($error = $pm->sent_message($data)) === false) {
    echo '<p>Отправлено</p>';
} else {
    // Выводим форму для написания письма
    echo '<div class="phdr"><b><a href="pm.php">' . $lng_pm['my_mail'] . '</a></b> | ' . $lng_pm['write_message'] . '</div>' .
         '<div class="gmenu">' .
         '<form name="form" action="pm.php?act=write" method="post"><p>' .
         '<h3>' . $lng_pm['recipients'] . '</h3>';
    if (!empty($data['rcp_list'])) {
        //sort($data['rcp_list']);
        foreach ($data['rcp_list'] as $val) {
            $recipient = htmlspecialchars($val);
            echo '<div><input type="checkbox" name="todel[]" value="' . $val . '"/>&nbsp;' . $val . '</div>' .
                 '<input type="hidden" name="tolist[]" value="' . $val . '" />';
        }
    }
    if (!empty($data['error']['rcp'])) foreach ($data['error']['rcp'] as $val) echo '<div>' . $val . '</div>';
    echo (isset($error['recipient']) ? '<p class="red">' . core::$lng['error'] . ': ' . $error['recipient'] . '</p>' : '') .
         '<input name="to" size="15" maxlength="100" ' . (count($data['rcp_list']) >= $max_rcp ? 'disabled="disabled"' : '') . (isset($error['recipient']) ? ' style="background-color: #FFCCCC"' : '') . '/>' .
         '<input type="submit" name="add" value="+/-" /><br />' .
         '<small><span' . (count($data['rcp_list']) >= $max_rcp ? ' class="red"' : '') . '>' . $lng_pm['max_recipients'] . ': ' . $max_rcp . '</span></small>' .
         '</p><p>' .
         '<h3>' . $lng_pm['subject'] . '</h3>' .
         '<input name="subject" maxlength="200" value="' . htmlentities($data['subject'], ENT_QUOTES, 'UTF-8') . '"/>' .
         '</p><p>' .
         '<h3>' . core::$lng['message'] . '</h3>' .
         (core::$is_mobile ? '' : bbcode::auto_bb('form', 'message')) .
         (isset($error['body']) ? '<span class="red">' . core::$lng['error'] . ': ' . $error['body'] . '</span><br />' : '') .
         '<textarea rows="' . core::$user_set['field_h'] . '" name="message"' . (isset($error['body']) ? ' style="background-color: #FFCCCC"' : '') . '>' . htmlentities($data['message'], ENT_QUOTES, 'UTF-8') . '</textarea>' .
         '</p><p>' .
         '<input type="checkbox" name="attach" value="1" />&#160;' . core::$lng['add_file'] . '<br />' .
         (core::$user_set['translit'] ? '<input type="checkbox" name="translit" value="1" />&nbsp;' . core::$lng['translit'] . '<br />' : '') .
         '<input type="checkbox" name="draft" value="1" /> <b>' . $lng_pm['draft'] . '</b>' .
         '</p><p>' .
         '<input type="submit" name="submit" value="' . core::$lng['sent'] . '"/>' .
         '</p></form></div>' .
         '<div class="phdr">' . (core::$user_set['translit'] ? '<a href="../pages/faq.php?act=trans">' . core::$lng['translit'] . '</a> | ' : '') . '<a href="../pages/faq.php?act=smileys">' . core::$lng['smileys'] . '</a></div>' .
         '<p><a href="' . htmlspecialchars($_SERVER['HTTP_REFERER']) . '">' . core::$lng['back'] . '</a></p>';
}