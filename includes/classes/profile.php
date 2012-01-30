<?php

/**
 * @package     JohnCMS
 * @link        http://johncms.com
 * @copyright   Copyright (C) 2008-2012 JohnCMS Community
 * @license     LICENSE.txt (see attached file)
 * @version     VERSION.txt (see attached file)
 * @author      http://johncms.com/about
 */

class Profile
{
    public static function show()
    {

    }

    public static function edit($action = '', $user_id = false)
    {
        global $rootpath;
        if (!Vars::$USER_ID || empty($action)) return false;
        $out = '<form action="' . $action . '" method="post">';

        // Выбор Аватара
        $file = 'files/users/avatar/' . Vars::$USER_ID . '.png';
        $out .= '<p><h3>Выберите Аватар</h3>' .
            '<table cellpadding="0" cellspacing="0"><tr>' .
            '<td width="40">';
        if (file_exists(($rootpath . $file))) $out .= '<img src="' . Vars::$HOME_URL . '/' . $file . '" width="32" height="32" alt="" />';
        else $out .= Functions::getImage('empty.png');
        $out .= '</td><td><small>' .
            '<a href="../pages/avatars.php">' . Vars::$LNG['select'] . '</a><br />' .
            '<a href="">' . Vars::$LNG['upload'] . '</a>' .
            '<br /><a href="">' . Vars::$LNG['delete'] . '</a>' .
            '</small></td></tr></table></p>';

        // Имя, Фамилия
        $out .= '<p><h3>' . Vars::$LNG['name_first'] . '</h3>' .
            '<input type="text" name="firstname" maxlength="100"/>' .
            '<h3>' . Vars::$LNG['name_last'] . '</h3>' .
            '<input type="text" name="lastname" maxlength="100"/></p>';

        // Дополнительная информация о себе
        $out .= '<p><h3>' . Vars::$LNG['about'] . '</h3>' .
            '<textarea rows="' . Vars::$USER_SET['field_h'] . '" name="about"></textarea></p>';

        // Связь
        $out .= '<p><h3>E-mail</h3>' .
            '<input type="text" name="email" maxlength="100"/>' .
            '<h3>ICQ</h3>' .
            '<input type="text" name="icq" maxlength="100"/>' .
            '<h3>Skype</h3>' .
            '<input type="text" name="skype" maxlength="100"/></p>';

        // Фотография
        $out .= '<h3>Загрузите фото</h3>';

        $out .= '<p><input type="submit" value="' . Vars::$LNG['save'] . '" name="submit" /></p>';

        $out .= '</form>';
        return $out;
    }
}
