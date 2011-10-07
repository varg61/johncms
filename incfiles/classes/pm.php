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

class pm
{
    /*
    -----------------------------------------------------------------
    Получение данных формы
    -----------------------------------------------------------------
    */
    public static function get_vars()
    {
        global $lng_pm;

        // Формируем список получателей
        $rcp_string = isset($_POST['to']) ? trim($_POST['to']) : '';
        $rcp_list = isset($_POST['tolist']) ? $_POST['tolist'] : array();
        $rcp_array = !empty($rcp_string) ? explode(',', $rcp_string) : array();
        $recipients = array_merge($rcp_list, $rcp_array);
        foreach ($recipients as $key => $val) $recipients[$key] = trim($val);

        // Обрабатываем запросы на удаление из списка получателей
        $rcp_del = isset($_POST['todel']) && is_array($_POST['todel']) ? $_POST['todel'] : array();
        $recipients = array_diff($recipients, $rcp_del);

        // Формируем возвращаемые методом данные
        $out['rcp_list'] = array();
        $out['subject'] = isset($_POST['subject']) ? mb_substr(trim($_POST['subject']), 0, 100) : '';
        $out['message'] = isset($_POST['message']) ? trim($_POST['message']) : '';
        $out['attach'] = isset($_POST['attach']);
        $out['translit'] = isset($_POST['translit']);
        $out['draft'] = isset($_POST['draft']);

        // Проверяем список получателей
        if (!empty($recipients)) {
            $count = 1;
            foreach ($recipients as $val) {
                if ($count > 2) break; //TODO: Поставить в зависимость от настроек в админке
                if (($user = self::get_user($val)) !== false) {
                    $out['recipients'][] = $user['name'];
                } else {
                    $out['error']['rcp'][] = '&#160;<b style="text-decoration: line-through;">' . $val . '</b> <small>' . core::$lng['error_user_not_exist'] . '</small>';
                    continue;
                }
                ++$count;
            }
        }

        // Проверяем наличие получателей
        if(count($out['recipients']) < 1) $out['error']['rcp_empty'] = $lng_pm['error_recipients'];

        // Проверяем длину сообщения
        if(mb_strlen($out['message']) < 4) $out['error']['msg'] = $lng_pm['error_short_message'];

        // Возвращаем данные
        return $out;
    }

    /*
    -----------------------------------------------------------------
    Отправка сообщения
    -----------------------------------------------------------------
    */
    public function sent_message($data)
    {
        global $lng_pm;
        $error = false;

        //TODO: Проверяем на повтор сообщений

        //TODO: Проверяем на флуд

        //TODO: Проверяем на игнор у получателя

        // Записываем сообщение в базу
        mysql_query("INSERT INTO `cms_pm` SET
           `pm_time` = '" . time() . "',
           `pm_subject` = '" . mysql_real_escape_string($data['subject']) . "',
           `pm_body` = '" . mysql_real_escape_string($data['message']) . "',
           `pm_sender_id` = '" . core::$user_id . "',
           `pm_sender_name` = '" . core::$user_data['name'] . "',
           `pm_sender_ip` = '" . core::$ip . "',
           `pm_sender_ip_via_proxy` = '" . core::$ip_via_proxy . "',
           `pm_sender_ua` = '" . mysql_real_escape_string(core::$user_agent) . "'
       ");
        return $error;
    }

    /*
    -----------------------------------------------------------------
    Поиск юзера по Нику
    -----------------------------------------------------------------
    */
    private static function get_user($name = '')
    {
        if(mb_strlen($name) < 2 || mb_strlen($name) > 20) return false;
        $req = mysql_query("SELECT `id`, `name` FROM `users` WHERE `name` = '" . mysql_real_escape_string($name) . "' LIMIT 1");
        if (mysql_num_rows($req)) return mysql_fetch_assoc($req);
        else return false;
    }
}