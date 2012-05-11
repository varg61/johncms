<?php

/**
 * @package     JohnCMS
 * @link        http://johncms.com
 * @copyright   Copyright (C) 2008-2012 JohnCMS Community
 * @license     LICENSE.txt (see attached file)
 * @version     VERSION.txt (see attached file)
 * @author      http://johncms.com/about
 */

defined( '_IN_JOHNCMS' ) or die( 'Error: restricted access' );
//Закрываем прямой доступ к файлу
defined( '_IN_JOHNCMS_MAIL' ) or die( 'Error: restricted access' );
Class ValidMail extends Vars {
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
	private $file_size;
	
	/*
    -----------------------------------------------------------------
    Конструктор класса
    -----------------------------------------------------------------
    */
	public function __construct(array $array, $id = false) {
		$this->id = $id;
		$this->array = $array;
	}
	
	/*
    -----------------------------------------------------------------
    Проверяем запрос к странице
    -----------------------------------------------------------------
    */
	public function request() {
		if($this->id !== false) {
			if($this->checkRequest() === false) {
				$this->error_request = lng( 'contact_no_select' );
				return false;
			} else if($this->checkId() === false) {
				$this->error_request = lng( 'user_does_not_exist' );
				return false;
			} else {
				return true;
			}
		}
	}
	/*
    -----------------------------------------------------------------
    Проверяем выбран ли ID
    -----------------------------------------------------------------
    */
	public function checkRequest() {
		if(!empty($this->id)) {
			return true;
		}
		return false;
	}
	
	/*
    -----------------------------------------------------------------
    Проверяем ID
    -----------------------------------------------------------------
    */
	private function checkId() {
		if($this->valRequest() === false) {
			return false;
		} else {
			$q = mysql_query( "SELECT `users`.`id`, `users`.`nickname`, `cms_mail_contacts`.`contact_id`, `cms_mail_contacts`.`banned`, `cms_mail_contacts`.`delete`, `cms_user_settings`.`value`
			FROM `users` 
			LEFT JOIN `cms_mail_contacts` ON
			`users`.`id`=`cms_mail_contacts`.`user_id`
			LEFT JOIN `cms_user_settings` ON
			`users`.`id`=`cms_user_settings`.`user_id` AND `cms_user_settings`.`key`='settings_mail'
			WHERE `users`.`id`='" . $this->id . "' LIMIT 1" );
			if ( mysql_num_rows( $q ) )
			{
				$res = mysql_fetch_assoc( $q );
				$this->id = $res['id'];
				$this->nickname = $res['nickname'];
				if($res['value'])
					$this->access = unserialize($res['value']);
				if(parent::$USER_RIGHTS == false)
					if(isset($res['contact_id']) && $res['contact_id'] == parent::$USER_ID && !$res['banned'] && !$res['delete'])
						$this->contact = true;
				return true;
			}
		}
		return false;
	}
	
	/*
    -----------------------------------------------------------------
    Проверяем является ли контакт другом
    -----------------------------------------------------------------
    */
	private function checkFriends() {
		$friends = mysql_result(mysql_query("SELECT COUNT(*) FROM `cms_mail_contact` WHERE `access`='2' AND ((`contact_id`='" . $this->id . "' AND `user_id`='" . Vars::$USER_ID . "') OR (`contact_id`='" . Vars::$USER_ID . "' AND `user_id`='" . $this->id . "'))"), 0);  
		if($friends == 2) {
			return true;
		}
		return false;
	}
	/*
    -----------------------------------------------------------------
    Проверяем существует ли логин
    -----------------------------------------------------------------
    */
	function checkLogin($login) {
		if($this->valLogin($login) === false) {
			return false;
		} else {
			$q = mysql_query( "SELECT `users`.`id`, `users`.`nickname`, `cms_mail_contacts`.`contact_id`, `cms_mail_contacts`.`banned`, `cms_mail_contacts`.`delete`, `cms_user_settings`.`value`
			FROM `users` 
			LEFT JOIN `cms_mail_contacts` ON
			`users`.`id`=`cms_mail_contacts`.`user_id`
			LEFT JOIN `cms_user_settings` ON
			`users`.`id`=`cms_user_settings`.`user_id` AND `cms_user_settings`.`key`='settings_mail'
			WHERE `users`.`nickname`='" . mysql_real_escape_string( $login ) . "' LIMIT 1" );
			if ( mysql_num_rows( $q ) )
			{
				$res = mysql_fetch_assoc( $q );
				$this->id = $res['id'];
				$this->nickname = $res['nickname'];
				if($res['value'])
					$this->access = unserialize($res['value']);
				if(parent::$USER_RIGHTS == false)
					if(isset($res['contact_id']) && $res['contact_id'] == parent::$USER_ID && !$res['banned'] && !$res['delete'])
						$this->contact = true;
				return true;
			} else {
				$this->error_log[] = lng( 'user_does_not_exist' );
				return false;
			}
		}
	}
	
	/*
    -----------------------------------------------------------------
    Проверяем логин
    -----------------------------------------------------------------
    */
	function valLogin($var) {
		if(empty($var)) {
			$this->error_log[] = lng( 'empty_login' );
		} else if (mb_strlen($var) < 2 || mb_strlen($var) > 20) {
            $this->error_log[] = lng('error_login');
        } else {
            return true;
        }
		return false;
	}
	/*
    -----------------------------------------------------------------
    Проверяем валидность отправляемыз данных
    -----------------------------------------------------------------
    */
	function validateForm() {
		if (isset($_POST['submit']) && $this->id !== false && self::checkCSRF() === true) {
			if($this->valIgnor() === false)
				return false;
			if($this->valRequest() === false)
				return false;
			if($this->valSetting() === false)
				return false;
			if($this->valText($this->array['text']) === false)
				return false;
			if($this->valFile() === false) 
				return false;
			$this->addMessage();
			return true;
		} else if(isset($_POST['login']) && self::checkCSRF() === true) {
			if($this->checkLogin($this->array['login']) === false)
				return false;
			if($this->valIgnor() === false)
				return false;
			if($this->valRequest() === false)
				return false;
			if($this->valSetting() === false)
				return false;
			if($this->valText($this->array['text']) === false)
				return false;
			if($this->valFile() === false) 
				return false;
			$this->addMessage();
			return true;
		} else {
			return false;
		}
	}
	
	/*
    -----------------------------------------------------------------
    Добавляем сообщение
    -----------------------------------------------------------------
    */
	function addMessage() {
		if(empty($this->error_log)) {
			//Обновляем, добавляем контакт
			if($this->checkContact( $this->id ) === true) {
				//Отправляем сообщение
				mysql_query( "INSERT INTO `cms_mail_messages` SET
				`user_id`='" . parent::$USER_ID . "',
				`contact_id`='" . $this->id . "',
				`text`='" . mysql_real_escape_string( $this->array['text'] ) . "',
				`time`='" . time() . "',
				`filename`='" . $this->file_name . "',
				`filesize`='" . $this->file_size . "'" );
				mysql_query( "UPDATE `users` SET `lastpost` = '" . time() . "' WHERE `id` = " . parent::$USER_ID );
			}
			Header ( 'Location: ' . Vars::$MODULE_URI . '?act=messages&id=' . $this->id );
			return true;
		}
		return false;
	}
	
	/*
    -----------------------------------------------------------------
    Проверяем пользователя на игнор
    -----------------------------------------------------------------
    */
    function valIgnor()
    {
        if (!Vars::$USER_RIGHTS && (Functions::checkIgnor($this->id, true) === true) )
        {
            $this->error_log[] = lng( 'you_banned' );
			return false;
        } elseif(Functions::checkIgnor($this->id) === true) {
			$this->error_log[] = lng( 'user_banned' );
			return false;
		} else 
        {
			return true;
        }
    }
	
	/*
    -----------------------------------------------------------------
    Проверяем текст
    -----------------------------------------------------------------
    */
	function valText($var) {
		if ( empty( $var ) )
			$this->error_log[] = lng( 'empty_message' );
		else if ( mb_strlen( $var ) < 2 )
			$this->error_log[] = lng( 'error_message' );
		else 
			return true;
		return false;
	}
	
	/*
    -----------------------------------------------------------------
    Проверяем на отправку сообщения с учетом настроек
    -----------------------------------------------------------------
    */
	private function valSetting() {
		if($this->access) {
			if($this->access['access'] > 0) {
				if($this->access['access'] == 1 && $this->contact !== true) {
					$this->error_log[] = lng('access_contact');
					return false;
				} else if($this->access['access'] == 2 && $this->checkFriends() !== true) {
					$this->error_log[] = lng('access_friends');
					return false;
				}
			}
		}
		return true;
	}
	
	/*
    -----------------------------------------------------------------
    Проверяем на отправку сообщения самому себе
    -----------------------------------------------------------------
    */
	function valRequest() {
		if($this->id == parent::$USER_ID) {
			$this->error_log[] = lng( 'error_my_message' );
			return false;
		}
		return true;
	}
	
	/*
    -----------------------------------------------------------------
    Проверяем загрузку файла
    -----------------------------------------------------------------
    */
	function valFile() {
		//Загружаем файл
		$handle = new UploadMail( $_FILES );
		$handle->DIR = FILEPATH . 'users' . DIRECTORY_SEPARATOR . 'pm';
		$handle->MAX_FILE_SIZE = 1024 * Vars::$SYSTEM_SET['flsz'];
		$handle->PREFIX_FILE = true;
		if ( $handle->upload() == true )
		{
			$this->file_name = $handle->FILE_UPLOAD;
			$this->file_size = $handle->INFO['size'];
			return true;
		} else {
			if ( $errors = $handle->errors() ) {
				$this->error_log[] = $errors;
				return false;
			} else 
				return true;
		}
	}
	
	/*
    -----------------------------------------------------------------
    Добавляем или обновляем контакт
    -----------------------------------------------------------------
    */
    private function checkContact( $id )
    {
		if(empty($id)) return false;
		mysql_query( "INSERT INTO `cms_mail_contacts` (`user_id`, `contact_id`, `time`)
		VALUES ('$id', '" . parent::$USER_ID . "', '" . time() . "')
		ON DUPLICATE KEY UPDATE `time`='" . time() . "', `archive`='0', `delete`='0'");
		mysql_query( "INSERT INTO `cms_mail_contacts` (`user_id`, `contact_id`, `time`)
		VALUES ('" . parent::$USER_ID . "', '$id', '" . time() . "')
		ON DUPLICATE KEY UPDATE `time`='" . time() . "', `archive`='0', `delete`='0'");
		return true;
    }
	
	
	public static function checkCSRF() {
		if (isset($_POST['token']) && isset($_SESSION['token_status'])&& $_POST['token'] == $_SESSION['token_status']) {
			unset($_SESSION['token_status']);
			return true;
		}
		return false;
	}
}