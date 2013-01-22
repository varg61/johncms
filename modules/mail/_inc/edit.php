<?php

/**
 * @package     JohnCMS
 * @link        http://johncms.com
 * @copyright   Copyright (C) 2008-2012 JohnCMS Community
 * @license     LICENSE.txt (see attached file)
 * @version     VERSION.txt (see attached file)
 * @author      http://johncms.com/about
 */

defined('_IN_JOHNCMS') or die('Error: restricted access');
//Закрываем прямой доступ к файлу
defined('_IN_JOHNCMS_MAIL') or die('Error: restricted access');
//Закрываем доступ гостям
if (!Vars::$USER_ID) {
    Header('Location: ' . Vars::$HOME_URL . '404');
    exit;
}

$backLink = Router::getUri(2);

if (Vars::$ID) {
    $result = DB::PDO()->query("SELECT * FROM `cms_mail_messages` WHERE `id` = " . Vars::$ID . " AND `user_id` = " . Vars::$USER_ID);
    if ($result->rowCount()) {
        $req = $result->fetch();
        if ($req['delete_out'] != 1 && $req['delete'] != Vars::$USER_ID) {
            if ($req['read'] == 0) {
                $tpl->text = htmlentities($req['text'], ENT_QUOTES, 'UTF-8');
                if ($_POST['submit'] && ValidMail::checkCSRF() === TRUE) {
                    $text = isset($_POST['text']) ? trim($_POST['text']) : '';
                    $error = array();
                    if (empty($text))
                        $error[] = __('empty_message');
                    elseif (mb_strlen($text) < 2)
                        $error[] = __('error_message');
                    if (($flood = Functions::antiFlood()) !== FALSE)
                        $error = __('error_flood') . ' ' . $flood . '&#160;' . __('seconds');
                    if (empty($error)) {
                        //Отправляем сообщение
                        $STH = DB::PDO()->prepare("UPDATE `cms_mail_messages` SET
						`text` = :text
						WHERE `id` = " . Vars::$ID . "
						AND `user_id` = " . Vars::$USER_ID);

                        $STH->bindParam(':text', $text);
                        $STH->execute();
                        $STH = NULL;

                        DB::PDO()->exec("UPDATE `users` SET `lastpost` = '" . time() . "' WHERE `id` = " . Vars::$USER_ID);

                        Header('Location: ' . $backLink . '?act=messages&id=' . $req['contact_id']);
                        exit;
                    } else {
                        $tpl->mail_error = Functions::displayError($error);
                        $tpl->text = htmlentities($text, ENT_QUOTES, 'UTF-8');
                    }
                }
                $tpl->url = $backLink . '?act=edit&amp;id=' . Vars::$ID;
                $tpl->token = mt_rand(100, 10000);
                $_SESSION['token_status'] = $tpl->token;
                $tpl->contents = $tpl->includeTpl('edit');
            } else {
                $tpl->contents = Functions::displayError(__('message_ready_read'), '<a href="' . $backLink . '">' . __('mail') . '</a>');
            }
        } else {
            $tpl->contents = Functions::displayError(__('page_does_not_exist'), '<a href="' . $backLink . '">' . __('mail') . '</a>');
        }
    } else {
        $tpl->contents = Functions::displayError(__('page_does_not_exist'), '<a href="' . $backLink . '">' . __('mail') . '</a>');
    }
} else {
    $tpl->contents = Functions::displayError(__('message_no_select'), '<a href="' . $backLink . '">' . __('mail') . '</a>');
}