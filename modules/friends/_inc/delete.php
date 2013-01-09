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
defined('_IN_JOHNCMS_FRIENDS') or die('Error: restricted access');
//Закрываем доступ гостям
if (!Vars::$USER_ID) {
    Header('Location: ' . Vars::$HOME_URL . '404');
    exit;
}
$fr = mysql_result(mysql_query("SELECT COUNT(*) FROM `cms_mail_contacts` WHERE `access`='2' AND ((`contact_id`='" .
    Vars::$ID . "' AND `user_id`='" . Vars::$USER_ID . "') OR (`contact_id`='" .
    Vars::$USER_ID . "' AND `user_id`='" . Vars::$ID . "'))"), 0);
if ($fr == 2) {
    if (isset($_POST['submit'])) {
        mysql_query("UPDATE `cms_mail_contacts` SET
					`access`='0', `friends`='0' WHERE `user_id`='" . Vars::$ID .
            "' AND `contact_id`='" . Vars::$USER_ID . "'");
        mysql_query("UPDATE `cms_mail_contacts` SET
					`access`='0', `friends`='0' WHERE `user_id`='" . Vars::$USER_ID .
            "' AND `contact_id`='" . Vars::$ID . "'");
        //TODO: Переделать под новую систему оповещения
        //$text = '[url=' . core::$system_set['homeurl'] . '/users/profile.php?user=' . Vars::$USER_ID . ']' . $user['name'] . '[/url] ' . $lng_profile['deleted_you_friends'];
        //mysql_query("INSERT INTO `cms_mail_messages` SET
        //`user_id` = '$user_id',
        //`contact_id` = '$id',
        //`text` = '$text',
        //`time` = '" . time() . "',
        //`sys` = '1',
        //`them` = '{$lng_profile['friendship']}'");
        $tpl->contents = '<div class="rmenu"><p>' . __('you_deleted_friends') . '</p>
		<p><a href="' . Router::getUrl(2) . '">' . __('friends') . '</a></p>
		</div>';
    } else {
        $tpl->urlSelect = Router::getUrl(2) . '?act=delete&amp;id=' . Vars::$ID;
        $tpl->select = __('really_deleted_friends');
        $tpl->submit = __('confirm');
        $tpl->phdr = __('deleted_friends');
        //TODO: Переделать ссылку
        $tpl->urlBack = Vars::$HOME_URL . '/profile?user=' . Vars::$ID;
        $tpl->token = mt_rand(100, 10000);
        $_SESSION['token_status'] = $tpl->token;
        //Подключаем шаблон модуля select.php
        $tpl->contents = $tpl->includeTpl('select');
    }
} else {
    $tpl->contents = Functions::displayError(__('removing_not_possible'));
}
