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
$fr = DB::PDO()->query("SELECT COUNT(*) FROM `cms_mail_contacts` WHERE `access`='2' AND ((`contact_id`='" . Vars::$ID . "' AND `user_id`='" . Vars::$USER_ID . "') OR (`contact_id`='" . Vars::$USER_ID . "' AND `user_id`='" . Vars::$ID . "'))")->fetchColumn();
if ($fr != 2) {
    if (isset($_POST['submit']) && isset($_POST['token']) && isset($_SESSION['token_status']) && $_POST['token'] == $_SESSION['token_status']) {
        $fr_out = DB::PDO()->query("SELECT COUNT(*) FROM `cms_mail_contacts` WHERE `access`='2' AND `user_id`='" . Vars::$ID . "' AND `contact_id`='" . Vars::$USER_ID . "'")->fetchColumn();
        if ($fr_out == 0) {
            echo functions::displayError(__('error_wrong_data'));
            //TODO: Переделать ссылку
            echo '<div class="bmenu"><a href="' . Vars::$HOME_URL . '/profile?user=' . Vars::$ID . '">' . __('back') . '</a></div>';
            exit;
        }

        DB::PDO()->exec("INSERT INTO `cms_mail_contacts` SET
		`user_id`='" . Vars::$USER_ID . "', 
		`contact_id`='" . Vars::$ID . "', 
		`time`='" . time() . "',
		`access`='2', 
		`friends`='1'
        ON DUPLICATE KEY UPDATE 
		`time`='" . time() . "',
		`access`='2', 
		`friends`='1'");
        DB::PDO()->exec("UPDATE `cms_mail_contacts` SET
		`time`='" . time() . "', `access`='2', `friends`='1' WHERE `user_id`='" . Vars::$ID . "' AND `contact_id`='" . Vars::$USER_ID . "'");
        //TODO: Переделать под новую систему оповещения

        //$text = '[url=' . Vars::$HOME_URL . '/profile?user=' . Vars::$ID . ']' . $user['name'] . '[/url] ' . lng('complied_friends');
        //mysql_query("INSERT INTO `cms_mail` SET
        //`user_id` = '" . Vars::$USER_ID . "',
        //`from_id` = '" . Vars::$ID . "',
        //`text` = '$text',
        //`time` = '" . time() . "',
        //`sys` = '1',
        //`them` = '" . lng('friendship') . "'");
        //$text = '[url=' . core::$system_set['homeurl'] . '/users/profile.php?user=' . $id . ']' . $result['name'] . '[/url] ' . $lng_profile['offers_friends'] . ' [url=' . core::$system_set['homeurl'] . '/users/profile.php?act=friends&do=ok&id=' . $id . ']' . $lng_profile['confirm'] . '[/url] | [url=' . core::$system_set['homeurl'] . '/users/profile.php?act=friends&do=no&id=' . $id . ']' . $lng_profile['decline'] . '[/url]';
        //mysql_query("DELETE FROM `cms_mail` WHERE `user_id` = '$id' AND `from_id` = '$user_id' AND `text`='$text'");
        //TODO: Переделать ссылку
        $tpl->contents = '<div class="rmenu"><p>' . __('demands_confirm') . '</p>
        <p><a href="' . Vars::$HOME_URL . '/profile?user=' . Vars::$ID . '">' . __('back') . '</a></p></div>';
    } else {
        $tpl->urlSelect = Router::getUri(2) . '?act=ok&amp;id=' . Vars::$ID;
        $tpl->select = __('really_demand_confirm');
        $tpl->submit = __('confirm');
        $tpl->phdr = __('demand_confirm');
        //TODO: Переделать ссылку
        $tpl->urlBack = Vars::$HOME_URL . '/profile?user=' . Vars::$ID;
        $tpl->token = mt_rand(100, 10000);
        $_SESSION['token_status'] = $tpl->token;
        //Подключаем шаблон модуля select.php
        $tpl->contents = $tpl->includeTpl('select');
    }
} else {
    $tpl->contents = Functions::displayError(__('user_already_friend'));
}