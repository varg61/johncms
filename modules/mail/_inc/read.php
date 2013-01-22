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
defined('_IN_JOHNCMS_MAIL') or die('Error: restricted access');

$backLink = Router::getUri(2);

if (Vars::$ID) {
    $result = DB::PDO()->query("SELECT * FROM `cms_mail_messages` WHERE `id`='" . Vars::$ID . "'")->fetch();
    if ($result) {
        if ($result['delete_in'] != Vars::$USER_ID && $result['delete_out'] != Vars::$USER_ID) {
            if ($result['user_id'] == Vars::$USER_ID) {
                $id = $result['contact_id'];
                $tpl->pref = __('pref_out');
                $tpl->back = 'outmess';
            } else {
                $id = $result['user_id'];
                $tpl->pref = __('pref_in');
                $tpl->back = 'inmess';
            }
            if ($result['read'] == 0 && $result['contact_id'] == Vars::$USER_ID)
                DB::PDO()->exec("UPDATE `cms_mail_messages` SET `read`='1' WHERE `contact_id`='" . Vars::$USER_ID . "' AND `id`='{$result['id']}'");
            $row = DB::PDO()->query("SELECT * FROM `users` WHERE `id`='$id'")->fetch();
            $text = Validate::checkout($result['text'], 1, 1);
            if (Vars::$USER_SET['smilies'])
                $text = Functions::smilies($text, $result['rights'] >= 1 ? 1 : 0);
            $tpl->contact_login = $row['nickname'];
            $tpl->user_id = $id;
            $tpl->users_id = $result['user_id'];
            $tpl->read = $result['read'];
            $tpl->text = $text;
            $tpl->file = $result['filename'] ? Functions::getImage(UploadMail::fileicon($result['filename']),
                '', 'style="margin: 0 0 -4px 0;"') . '&#160;<a href="' . $backLink . '?act=load&amp;id=' . $result['id'] . '">' . $result['filename'] .
                '</a> (' . UploadMail::formatsize($result['filesize']) . ')(' . $result['filecount'] . ')' : '';
            $tpl->time_message = Functions::displayDate($result['time']);


            //Подключаем шаблон read.php
            $tpl->link = $backLink;
            $tpl->contents = $tpl->includeTpl('read');
        } else {
            $tpl->contents = Functions::displayError(__('page_does_not_exist'), '<a href="' . Vars::$HOME_URL . 'contacts/">' . __('contacts') . '</a><br />
			<a href="' . $backLink . '">' . __('mail') . '</a>');
        }
    } else {
        $tpl->contents = Functions::displayError(__('page_does_not_exist'), '<a href="' . Vars::$HOME_URL . 'contacts/">' . __('contacts') . '</a><br />
		<a href="' . $backLink . '">' . __('mail') . '</a>');
    }
} else {
    $tpl->contents = Functions::displayError(__('message_no_select'), '<a href="' . Vars::$HOME_URL . 'contacts/">' . __('contacts') . '</a><br />
	<a href="' . $backLink . '">' . __('mail') . '</a>');
}