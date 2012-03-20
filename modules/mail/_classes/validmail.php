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
	public $banned;
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
			$q = mysql_query( "SELECT `users`.`id`, `users`.`nickname`, `cms_mail_contacts`.`banned`
			FROM `users` 
			LEFT JOIN `cms_mail_contacts` ON
			`users`.`id`=`cms_mail_contacts`.`user_id`
			WHERE `users`.`id`='" . $this->id . "' LIMIT 1" );
			if ( mysql_num_rows( $q ) )
			{
				$res = mysql_fetch_assoc( $q );
				$this->id = $res['id'];
				$this->nickname = $res['nickname'];
				$this->banned = $res['banned'];
				return true;
			}
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
			$q = mysql_query( "SELECT `users`.`id`, `users`.`nickname`, `cms_mail_contacts`.`banned`
			FROM `users` 
			LEFT JOIN `cms_mail_contacts` ON
			`users`.`id`=`cms_mail_contacts`.`user_id`
			WHERE `users`.`nickname`='" . mysql_real_escape_string( $login ) . "' LIMIT 1" );
			if ( mysql_num_rows( $q ) )
			{
				$res = mysql_fetch_assoc( $q );
				$this->id = $res['id'];
				$this->nickname = $res['nickname'];
				if(parent::$USER_RIGHTS == false)
					$this->banned = $res['banned'];
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
		if (isset($_POST['submit']) && $this->id !== false) {
			if($this->valIgnor() === false)
				return false;
			if($this->valRequest() === false)
				return false;
			if($this->valText($this->array['text']) === false)
				return false;
			if($this->valFile() === false) 
				return false;
			$this->addMessage();
			return true;
		} else if(isset($_POST['login'])) {
			if($this->checkLogin($this->array['login']) === false)
				return false;
			if($this->valIgnor() === false)
				return false;
			if($this->valRequest() === false)
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
			//Отправляем сообщение
			$this->checkContact( $this->id );
			mysql_query( "INSERT INTO `cms_mail_messages` SET
			`user_id`='" . parent::$USER_ID . "',
			`contact_id`='" . $this->id . "',
			`text`='" . mysql_real_escape_string( $this->array['text'] ) . "',
			`time`='" . time() . "',
			`filename`='" . $this->file_name . "',
			`filesize`='" . $this->file_size . "'" );
			$this->countPlus( $this->id, parent::$USER_ID );
			mysql_query( "UPDATE `users` SET `lastpost` = '" . time() . "' WHERE `id` = " . parent::$USER_ID );
			Header( 'Location: ' . Vars::$MODULE_URI . '?act=messages&id=' . $this->id );
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
        if (!Vars::$USER_RIGHTS && $this->banned == true )
        {
            $this->error_log[] = lng( 'you_banned' );
			return false;
        } elseif(Mail::ignor($this->id) === true) {
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
		else if ( mb_strlen( $var ) < 2 || mb_strlen( $var ) > 5000 )
			$this->error_log[] = lng( 'error_message' );
		else 
			return true;
		return false;
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
		$handle->DIR = ROOTPATH . 'files/' . MAILDIR;
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
        $count = mysql_result( mysql_query( "SELECT COUNT(*) 
        FROM `cms_mail_contacts` 
        WHERE `user_id`='$id' 
        AND `contact_id`='" . parent::$USER_ID . "'" ), 0 );
        $total = mysql_result( mysql_query( "SELECT COUNT(*) 
        FROM `cms_mail_contacts` 
        WHERE `user_id`='" . parent::$USER_ID . "' 
        AND `contact_id`='$id'" ), 0 );
        if ( $total )
        {
            mysql_query( "UPDATE `cms_mail_contacts` SET
			`time`='" . time() . "' 
            WHERE `user_id`='" . parent::$USER_ID . "' 
            AND `contact_id`='$id'" );
            if ( $count )
            {
                mysql_query( "UPDATE `cms_mail_contacts` SET
				`time`='" . time() . "' 
                WHERE `user_id`='$id' 
                AND `contact_id`='" . parent::$USER_ID . "'" );
            } else
            {
                mysql_query( "INSERT INTO `cms_mail_contacts` SET
				`user_id`='$id',
				`contact_id`='" . parent::$USER_ID . "',
				`time`='" . time() . "'" );
            }
        } else
        {
            mysql_query( "INSERT INTO `cms_mail_contacts` SET
			`user_id`='" . parent::$USER_ID . "',
			`contact_id`='$id',
			`time`='" . time() . "'" );
            if ( $count )
            {
                mysql_query( "UPDATE `cms_mail_contacts` SET
				`time`='" . time() . "' 
                WHERE `user_id`='$id' 
                AND `contact_id`='" . parent::$USER_ID . "'" );
            } else
            {
                mysql_query( "INSERT INTO `cms_mail_contacts` SET
				`user_id`='$id',
				`contact_id`='" . parent::$USER_ID . "',
				`time`='" . time() . "'" );
            }
        }
    }
	
	/*
    -----------------------------------------------------------------
    Добавляем +1 к счетчику
    -----------------------------------------------------------------
    */
    private function countPlus( $id, $user_id )
    {
        if ( $id == null )
            return false;
        mysql_query( "UPDATE `cms_mail_contacts` SET
		`count_out`=`count_out`+1,
        `time`='" . time() . "', 
        `archive`='0', 
        `delete`='0' 
        WHERE `user_id`='$user_id' 
        AND `contact_id`='$id'" );
        mysql_query( "UPDATE `cms_mail_contacts` SET
        `count_in`=`count_in`+1, 
        `time`='" . time() . "', 
        `archive`='0', 
        `delete`='0' 
        WHERE `user_id`='$id' 
        AND `contact_id`='$user_id'" );
		return;
    }
	
	
}