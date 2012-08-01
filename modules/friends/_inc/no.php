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
if ( !Vars::$USER_ID )
{
	Header( 'Location: ' . Vars::$HOME_URL . '/404' );
    exit;
}
$fr = mysql_result(mysql_query("SELECT COUNT(*) FROM `cms_mail_contact` WHERE `access`='2' AND ((`contact_id`='" . Vars::$ID . "' AND `user_id`='" . Vars::$USER_ID . "') OR (`contact_id`='" . Vars::$USER_ID . "' AND `user_id`='" . Vars::$ID . "'))"),
    0);
if ($fr != 2) {
    if (isset($_POST['submit']) && isset($_POST['token']) && isset($_SESSION['token_status']) &&
        $_POST['token'] == $_SESSION['token_status']) {
        $fr_out = mysql_result(mysql_query("SELECT COUNT(*) FROM `cms_mail_contacts` WHERE `access`='2' AND `user_id`='" . Vars::$ID . "' AND `contact_id`='" . Vars::$USER_ID . "'"),
            0);
        if ($fr_out == 0) {
            $tpl->contents = functions::displayError(lng('not_demand_friendship'), '<a href="' . Vars::$HOME_URL . '/profile?user=' . Vars::$ID . '">' . lng('back') . '</a>');
        } else {
			mysql_query("UPDATE `cms_mail_contacts` SET
			`access`='0' WHERE `user_id`='" . Vars::$ID . "' AND `contact_id`='" . Vars::$USER_ID . "'");
			//TODO: Переделать под новую систему оповещения
			//$text = '[url=' . core::$system_set['homeurl'] . '/users/profile.php?user=' . $user_id . ']' . $user['name'] . '[/url] ' . $lng_profile['canceled_you_demand'];
			//mysql_query("INSERT INTO `cms_mail` SET
			//`user_id` = '$user_id',
			//`from_id` = '$id',
			//`text` = '$text',
			//`time` = '" . time() . "',
			//`sys` = '1',
			//`theme` = '{$lng_profile['friendship']}'");
			//$text = '[url=' . core::$system_set['homeurl'] . '/users/profile.php?user=' . $id . ']' . $result['name'] . '[/url] ' . $lng_profile['offers_friends'] . ' [url=' . core::$system_set['homeurl'] . '/users/profile.php?act=friends&do=ok&id=' . $id . ']' . $lng_profile['confirm'] . '[/url] | [url=' . core::$system_set['homeurl'] . '/users/profile.php?act=friends&do=no&id=' . $id . ']' . $lng_profile['decline'] . '[/url]';
			//mysql_query("DELETE FROM `cms_mail` WHERE `user_id` = '$id' AND `from_id` = '$user_id' AND `text`='$text'");
			$tpl->contents = '<div class="rmenu"><p>' . lng('canceled_demand_send') . '</p>
			<p><a href="' . Vars::$HOME_URL . '/contacts">' . lng('back') . '</a></p></div>';
		}
    } else {
        $tpl->urlSelect = Vars::$MODULE_URI . '?act=no&amp;id=' . Vars::$ID;
        $tpl->select = lng('really_canceled_demand');
        $tpl->submit = lng('confirm');
        $tpl->phdr = lng('canceled_demand');
        $tpl->urlBack = Vars::$HOME_URL . '/profile?user=' . Vars::$ID;
        $tpl->token = mt_rand(100, 10000);
        $_SESSION['token_status'] = $tpl->token;
        //Подключаем шаблон модуля select.php
        $tpl->contents = $tpl->includeTpl('select');
    }
} else {
    $tpl->contents = Functions::displayError(lng('already_your_friend'));
}
