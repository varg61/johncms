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
        $fr_out = DB::PDO()->query("SELECT COUNT(*) FROM `cms_mail_contacts` WHERE `access`='2' AND `user_id`='" . Vars::$USER_ID . "' AND `contact_id`='" . Vars::$ID . "'")->fetchColumn();
        if ($fr_out) {
            //TODO: Переделать ссылку
            $tpl->contents = Functions::displayError(__('already_demand'), '<a href="' . Vars::$HOME_URL . '/profile?user=' . Vars::$ID . '">' . __('back') . '</a>');
        } else {
            $fr_in = DB::PDO()->query("SELECT COUNT(*) FROM `cms_mail_contacts` WHERE `access`='2' AND `contact_id`='" . Vars::$USER_ID . "' AND `user_id`='" . Vars::$ID . "'")->fetchColumn();
            if ($fr_in) {
                //TODO: Переделать ссылку
                $tpl->contents = Functions::displayError(__('offer_already'), '<a href="' . Vars::$HOME_URL . '/profile?user=' . Vars::$ID . '">' . __('back') . '</a>');
            } else {
                DB::PDO()->exec("INSERT INTO `cms_mail_contacts` (`user_id`, `contact_id`, `access`, `time`)
        		VALUES ('" . Vars::$USER_ID . "', '" . Vars::$ID . "', '2', '" . time() . "')
        		ON DUPLICATE KEY UPDATE `access`='2', `time`='" . time() . "', `delete`='0'");
                //TODO: Переделать под новую систему оповещения
                //$text = '[url=' . Vars::$HOME_URL . '/profile?user=' . Vars::$USER_ID . ']' . Vars::$USER_NICKNAME . '[/url] ' . lng('offers_friends') . '\r\n[url=' . Vars::$MODULE_URI . '?act=ok&id=' . Vars::$USER_ID . ']' . lng('confirm') . '[/url] | [url=' . Vars::$MODULE_URI . '?act=no&id=' . Vars::$USER_ID . ']' . lng('decline') . '[/url]';
                //mysql_query("INSERT INTO `cms_mail_messages` SET
                //`user_id` = '" . Vars::$USER_ID . "',
                //`contact_id` = '" . Vars::$ID . "',
                //`text` = '$text',
                //`time` = '" . time() . "',
                //`sys` = '1',
                //`theme` = '" . lng('friendship') . "'");
                //TODO: Переделать ссылку
                $tpl->contents = '<div class="rmenu"><p>' . __('demand_friends_sent') . '</p>
                <p><a href="' . Vars::$HOME_URL . '/profile?user=' . Vars::$ID . '">' . __('back') . '</a></p>
                </div>';
            }
        }
    } else {
        $tpl->urlSelect = Router::getUri(2) . '?act=add&amp;id=' . Vars::$ID;
        $tpl->select = __('confirm_offer_friendship');
        $tpl->submit = __('confirm');
        $tpl->phdr = __('offer_friendship');
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