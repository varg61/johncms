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
    private $user_id;
    private $max_recipients;

    function __construct($arg = array())
    {
        $this->user_id = isset($arg['user_id']) ? intval($arg['user_id']) : core::$user_id;
        $this->max_recipients = 3; //TODO: Задать из настроек
    }

    /*
    -----------------------------------------------------------------
    Форма для написания сообщения
    -----------------------------------------------------------------
    */
    public function message_write($action = '', $vars = array(), $error = array())
    {
        global $lng_pm;
        $out = '<form name="form" action="' . $action . '" method="post"><p>' .
               '<h3>' . $lng_pm['recipients'] . '</h3>';
        if (!empty($vars['rcp_list'])) {
            sort($vars['rcp_list']);
            foreach ($vars['rcp_list'] as $val) {
                $val = htmlspecialchars(trim($val));
                $out .= '<div><input type="checkbox" name="todel[]" value="' . $val . '"/>&nbsp;' . $val . '</div>' .
                        '<input type="hidden" name="tolist[]" value="' . $val . '" />';
            }
        }
        if (!empty($vars['error'])) foreach ($vars['error'] as $val) $out .= '<p>' . $val . '</p>';
        $out .= (isset($error['recipient']) ? '<p class="red">' . core::$lng['error'] . ': ' . $error['recipient'] . '</p>' : '') .
                '<input name="to" size="15" maxlength="100" ' . (count($vars['rcp_list']) >= $this->max_recipients ? 'disabled="disabled"' : '') . (isset($error['recipient']) ? ' style="background-color: #FFCCCC"' : '') . '/>' .
                '<input type="submit" name="add" value="+/-" /><br />' .
                '<small><span' . (count($vars['rcp_list']) >= $this->max_recipients ? ' class="red"' : '') . '>Макс. число получателей: ' . $this->max_recipients . '</span></small>' .
                '</p><p><h3>' . $lng_pm['subject'] . '</h3>' .
                '<input name="subject" maxlength="200" value="' . htmlentities($vars['subject'], ENT_QUOTES, 'UTF-8') . '"/>' .
                '</p><p><h3>' . core::$lng['message'] . '</h3>' .
                (core::$is_mobile ? '' : bbcode::auto_bb('form', 'message')) .
                (isset($error['body']) ? '<span class="red">' . core::$lng['error'] . ': ' . $error['body'] . '</span><br />' : '') .
                '<textarea rows="' . core::$user_set['field_h'] . '" name="message"' . (isset($error['body']) ? ' style="background-color: #FFCCCC"' : '') . '>' . htmlentities($vars['message'], ENT_QUOTES, 'UTF-8') . '</textarea>' .
                '</p><p><input type="checkbox" name="attach" value="1" />&#160;' . core::$lng['add_file'] . '<br />' .
                (core::$user_set['translit'] ? '<input type="checkbox" name="translit" value="1" />&nbsp;' . core::$lng['translit'] . '<br />' : '') .
                '<input type="checkbox" name="draft" value="1" /> <b>Черновик</b></p>' .
                '<p><input type="submit" name="submit" value="' . core::$lng['sent'] . '"/></p>' .
                '</form>';
        return $out;
    }

    /*
    -----------------------------------------------------------------
    Отправка сообщения
    -----------------------------------------------------------------
    */
    public function sent_message($data)
    {
        $error = false;
        $to = array();
        // Проверяем наличие получателей
        if (empty($data['rcp_list']) && !$data['draft']) $error['recipient'] = 'Нет получателей';

        // Проверяем наличие сообщения
        if (mb_strlen($data['message']) < 4) $error['body'] = 'Слишком короткое сообщение';

        //TODO: Проверяем на повтор сообщений

        //TODO: Проверяем на флуд

        // Проверяем наличие получателей
        foreach ($data['rcp_list'] as $recipient) {
            if (($user = $this->get_user($recipient)) !== false) {
                // Если пользователь есть
                $to[] = $user['name'];
            } else {
                // Если пользователя нет
                $error = true;
                break;
            }
        }

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
    Получение данных формы отправки PM сообщения
    -----------------------------------------------------------------
    Возвращаемые данные:
    $out['error'] - (ARRAY) сообщения об ошибках
    $out['rcp_list'] - (ARRAY) список получателей

    $out['subject'] - (TEXT) тема сообщения
    $out['message'] - (TEXT) текст сообщения

    $out['attach'] - (BOOLEAN) прикрепить файл
    $out['draft'] - (BOOLEAN) отправить в черновики
    $out['translit'] - (BOOLEAN) транслитерация
    -----------------------------------------------------------------
    */
    public function get_vars()
    {
        $recipient = isset($_POST['to']) ? trim($_POST['to']) : '';
        $out['rcp_list'] = isset($_POST['tolist']) ? $_POST['tolist'] : array();
        $rcp_del = isset($_POST['todel']) ? $_POST['todel'] : array();
        $out['subject'] = isset($_POST['subject']) ? trim($_POST['subject']) : '';
        $out['message'] = isset($_POST['message']) ? trim($_POST['message']) : '';
        $out['attach'] = isset($_POST['attach']);
        $out['translit'] = isset($_POST['translit']);
        $out['draft'] = isset($_POST['draft']);
        $count_rcp = count($out['rcp_list']);
        if (!empty($recipient)) {
            $array = explode(',', $recipient);
            foreach ($array as $val) {
                $val = trim($val);
                if ($count_rcp < $this->max_recipients && mb_strlen($val) > 1 && mb_strlen($val) < 20 && !in_array($val, $out['rcp_list'])) {
                    if (($user = $this->get_user($val)) !== false) $out['rcp_list'][] = $user['name'];
                    else $out['error'][] = '&#160;<b style="text-decoration: line-through;">' . $val . '</b> <small>' . core::$lng['error_user_not_exist'] . '</small>';
                }
                ++$count_rcp;
            }
        }
        if (!empty($rcp_del) && is_array($rcp_del)) {
            foreach ($rcp_del as $val) {
                $key = array_search(trim($val), $out['rcp_list']);
                unset($out['rcp_list'][$key]);
            }
        }
        return $out;
    }

    /*
    -----------------------------------------------------------------
    Поиск юзера по Нику
    -----------------------------------------------------------------
    */
    private function get_user($name = '')
    {
        $req = mysql_query("SELECT `id`, `name` FROM `users` WHERE `name` = '" . mysql_real_escape_string($name) . "' LIMIT 1");
        if (mysql_num_rows($req)) {
            return mysql_fetch_assoc($req);
        } else {
            return false;
        }
    }
}