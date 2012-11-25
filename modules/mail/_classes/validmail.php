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
Class ValidMail extends Vars
{
    public $id;
    public $error_request;
    public $error_log = array();
    public $nickname;
    public $access = array();
    public $contact;
    public $friends;
    public $archive;
    public $count_mail;
    public $array = array();
    public $error_test;
    private $file_name;
    private $file_size = 0;

    /*
    -----------------------------------------------------------------
    Конструктор класса
    -----------------------------------------------------------------
    */
    public function __construct(array $array, $id = FALSE)
    {
        $this->id = $id;
        $this->array = $array;
    }

    /*
    -----------------------------------------------------------------
    Проверяем запрос к странице
    -----------------------------------------------------------------
    */
    public function request()
    {
        if ($this->id !== FALSE) {
            if ($this->checkRequest() === FALSE) {
                $this->error_request = __('contact_no_select');
                return FALSE;
            } else if ($this->checkId() === FALSE) {
                $this->error_request = __('user_does_not_exist');
                return FALSE;
            } else {
                return TRUE;
            }
        }
    }

    /*
    -----------------------------------------------------------------
    Проверяем выбран ли ID
    -----------------------------------------------------------------
    */
    public function checkRequest()
    {
        if (!empty($this->id)) {
            return TRUE;
        }
        return FALSE;
    }

    /*
    -----------------------------------------------------------------
    Проверяем ID
    -----------------------------------------------------------------
    */
    private function checkId()
    {
        if ($this->valRequest() === FALSE) {
            return FALSE;
        } else {
            $q = mysql_query("SELECT `users`.`id`, `users`.`nickname`, `cms_mail_contacts`.`contact_id`, `cms_mail_contacts`.`banned`, `cms_mail_contacts`.`delete`, `cms_user_settings`.`value`
			FROM `users` 
			LEFT JOIN `cms_mail_contacts` ON
			`users`.`id`=`cms_mail_contacts`.`user_id`
			LEFT JOIN `cms_user_settings` ON
			`users`.`id`=`cms_user_settings`.`user_id` AND `cms_user_settings`.`key`='settings_mail'
			WHERE `users`.`id`='" . $this->id . "' LIMIT 1");
            if (mysql_num_rows($q)) {
                $res = mysql_fetch_assoc($q);
                $this->id = $res['id'];
                $this->nickname = $res['nickname'];
                if ($res['value'])
                    $this->access = unserialize($res['value']);
                if (parent::$USER_RIGHTS == FALSE)
                    if (isset($res['contact_id']) && $res['contact_id'] == parent::$USER_ID && !$res['banned'] && !$res['delete'])
                        $this->contact = TRUE;
                return TRUE;
            }
        }
        return FALSE;
    }

    /*
    -----------------------------------------------------------------
    Проверяем является ли контакт другом
    -----------------------------------------------------------------
    */
    private function checkFriends()
    {
        $friends = mysql_result(mysql_query("SELECT COUNT(*) FROM `cms_mail_contacts` WHERE `access`='2' AND ((`contact_id`='" . $this->id . "' AND `user_id`='" . Vars::$USER_ID . "') OR (`contact_id`='" . Vars::$USER_ID . "' AND `user_id`='" . $this->id . "'))"), 0);
        if ($friends == 2) {
            return TRUE;
        }
        return FALSE;
    }

    /*
    -----------------------------------------------------------------
    Проверяем существует ли логин
    -----------------------------------------------------------------
    */
    function checkLogin($login)
    {
        if ($this->valLogin($login) === FALSE) {
            return FALSE;
        } else {
            $q = mysql_query("SELECT `users`.`id`, `users`.`nickname`, `cms_mail_contacts`.`contact_id`, `cms_mail_contacts`.`banned`, `cms_mail_contacts`.`delete`, `cms_user_settings`.`value`
			FROM `users` 
			LEFT JOIN `cms_mail_contacts` ON
			`users`.`id`=`cms_mail_contacts`.`user_id`
			LEFT JOIN `cms_user_settings` ON
			`users`.`id`=`cms_user_settings`.`user_id` AND `cms_user_settings`.`key`='settings_mail'
			WHERE `users`.`nickname`='" . mysql_real_escape_string($login) . "' LIMIT 1");
            if (mysql_num_rows($q)) {
                $res = mysql_fetch_assoc($q);
                $this->id = $res['id'];
                $this->nickname = $res['nickname'];
                if ($res['value'])
                    $this->access = unserialize($res['value']);
                if (parent::$USER_RIGHTS == FALSE)
                    if (isset($res['contact_id']) && $res['contact_id'] == parent::$USER_ID && !$res['banned'] && !$res['delete'])
                        $this->contact = TRUE;
                return TRUE;
            } else {
                $this->error_log[] = __('user_does_not_exist');
                return FALSE;
            }
        }
    }

    /*
    -----------------------------------------------------------------
    Проверяем логин
    -----------------------------------------------------------------
    */
    function valLogin($var)
    {
        if (empty($var)) {
            $this->error_log[] = __('empty_login');
        } else if (mb_strlen($var) < 2 || mb_strlen($var) > 20) {
            $this->error_log[] = __('error_login');
        } else {
            return TRUE;
        }
        return FALSE;
    }

    /*
    -----------------------------------------------------------------
    Проверяем валидность отправляемыз данных
    -----------------------------------------------------------------
    */
    function validateForm()
    {
        if (isset($_POST['submit']) && $this->id !== FALSE && self::checkCSRF() === TRUE) {
            if($this->checkError() === FALSE)
				return FALSE;
			$this->addMessage();
			return TRUE;
        } else if (isset($_POST['login']) && self::checkCSRF() === TRUE) {
            if ($this->checkLogin($this->array['login']) === FALSE)
                return FALSE;
			if($this->checkError() === FALSE)
				return FALSE;
			$this->addMessage();
			return TRUE;
        } else {
            return FALSE;
        }
    }
	
	function checkError() {
		if ($this->valIgnor() === FALSE)
			return FALSE;
		if ($this->valRequest() === FALSE)
			return FALSE;
		if ($this->valSetting() === FALSE)
			return FALSE;
		if ($this->valText($this->array['text']) === FALSE)
			return FALSE;
		if ($this->valFile() === FALSE)
			return FALSE;
		if ($this->checkFlood() === FALSE)
			return FALSE;
		return TRUE;
	}
    /*
    -----------------------------------------------------------------
    Добавляем сообщение
    -----------------------------------------------------------------
    */
    function addMessage()
    {
        if (empty($this->error_log)) {
            //Обновляем, добавляем контакт
            if ($this->checkContact($this->id) === TRUE) {
                //Отправляем сообщение
                mysql_query("INSERT INTO `cms_mail_messages` SET
				`user_id`='" . parent::$USER_ID . "',
				`contact_id`='" . $this->id . "',
				`text`='" . mysql_real_escape_string($this->array['text']) . "',
				`time`='" . time() . "',
				`filename`='" . $this->file_name . "',
				`filesize`='" . $this->file_size . "'") or die(mysql_error());
                mysql_query("UPDATE `users` SET `lastpost` = '" . time() . "' WHERE `id` = " . parent::$USER_ID);
            }
            Header('Location: ' . Vars::$MODULE_URI . '?act=messages&id=' . $this->id);
            return TRUE;
        }
        return FALSE;
    }
	/*
    -----------------------------------------------------------------
    Проверяем сообщение на флуд
    -----------------------------------------------------------------
    */
	function checkFlood() {
		if (($flood = Functions::antiFlood()) === FALSE)
			return TRUE;
		$this->error_log[] = __('error_flood') . '&#160;' . $flood . '&#160;' . __('seconds');
		return FALSE;
	}
    /*
    -----------------------------------------------------------------
    Проверяем пользователя на игнор
    -----------------------------------------------------------------
    */
    function valIgnor()
    {
        if (!Vars::$USER_RIGHTS && (Functions::checkIgnor($this->id, TRUE) === TRUE)) {
            $this->error_log[] = __('you_banned');
            return FALSE;
        } elseif (Functions::checkIgnor($this->id) === TRUE) {
            $this->error_log[] = __('user_banned');
            return FALSE;
        } else {
            return TRUE;
        }
    }

    /*
    -----------------------------------------------------------------
    Проверяем текст
    -----------------------------------------------------------------
    */
    function valText($var)
    {
        if (empty($var))
            $this->error_log[] = __('empty_message');
        else if (mb_strlen($var) < 2)
            $this->error_log[] = __('error_message');
        else
            return TRUE;
        return FALSE;
    }

    /*
    -----------------------------------------------------------------
    Проверяем на отправку сообщения с учетом настроек
    -----------------------------------------------------------------
    */
    private function valSetting()
    {
        if ($this->access) {
            if ($this->access['access'] > 0  && Vars::$USER_RIGHTS == 0) {
                if ($this->access['access'] == 1 && $this->contact !== TRUE) {
                    $this->error_log[] = __('access_contact');
                    return FALSE;
                } else if ($this->access['access'] == 2 && Functions::checkFriend($this->id, TRUE) != 1  && Vars::$USER_RIGHTS == 0) {
                    $this->error_log[] = __('access_friends');
                    return FALSE;
                }
            }
        }
        return TRUE;
    }

    /*
    -----------------------------------------------------------------
    Проверяем на отправку сообщения самому себе
    -----------------------------------------------------------------
    */
    function valRequest()
    {
        if ($this->id == parent::$USER_ID) {
            $this->error_log[] = __('error_my_message');
            return FALSE;
        }
        return TRUE;
    }

    /*
    -----------------------------------------------------------------
    Проверяем загрузку файла
    -----------------------------------------------------------------
    */
    function valFile()
    {
        if(empty($_FILES)) {
			return TRUE;
		} else {
			//Загружаем файл
			$handle = new UploadMail($_FILES);
			$handle->DIR = FILEPATH . 'users' . DIRECTORY_SEPARATOR . 'pm';
			$handle->MAX_FILE_SIZE = 1024 * Vars::$SYSTEM_SET['filesize'];
			$handle->PREFIX_FILE = TRUE;
			if ($handle->upload() == TRUE) {
				$this->file_name = $handle->FILE_UPLOAD;
				$this->file_size = $handle->INFO['size'];
				return TRUE;
			} else {
				if ($errors = $handle->errors()) {
					$this->error_log[] = $errors;
					return FALSE;
				} else
					return TRUE;
			}
		}
    }

    /*
    -----------------------------------------------------------------
    Добавляем или обновляем контакт
    -----------------------------------------------------------------
    */
    private function checkContact($id)
    {
        if (empty($id)) return FALSE;
        mysql_query("INSERT INTO `cms_mail_contacts` (`user_id`, `contact_id`, `time`)
		VALUES ('$id', '" . parent::$USER_ID . "', '" . time() . "')
		ON DUPLICATE KEY UPDATE `time`='" . time() . "', `archive`='0', `delete`='0'");
        mysql_query("INSERT INTO `cms_mail_contacts` (`user_id`, `contact_id`, `time`)
		VALUES ('" . parent::$USER_ID . "', '$id', '" . time() . "')
		ON DUPLICATE KEY UPDATE `time`='" . time() . "', `archive`='0', `delete`='0'");
        return TRUE;
    }

	/*
    -----------------------------------------------------------------
    Проверяем на предмет CSRF атаки
    -----------------------------------------------------------------
    */
    public static function checkCSRF()
    {
        if (isset($_POST['token']) && isset($_SESSION['token_status']) && $_POST['token'] == $_SESSION['token_status']) {
            unset($_SESSION['token_status']);
            return TRUE;
        }
        return FALSE;
    }
}