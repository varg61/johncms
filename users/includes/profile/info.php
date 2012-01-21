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

//TODO: Доработать!

/*
-----------------------------------------------------------------
Подробная информация, контактные данные
-----------------------------------------------------------------
*/
$textl = htmlspecialchars($user['nickname']) . ': ' . Vars::$LNG['information'];
echo '<div class="phdr"><a href="profile.php?user=' . $user['user_id'] . '"><b>' . Vars::$LNG['profile'] . '</b></a> | ' . Vars::$LNG['information'] . '</div>';
if ($user['user_id'] == Vars::$USER_ID || (Vars::$USER_RIGHTS >= 7 && Vars::$USER_RIGHTS > $user['rights']))
    echo '<div class="topmenu"><a href="profile.php?act=edit&amp;user=' . $user['user_id'] . '">' . Vars::$LNG['edit'] . '</a></div>';
echo '<div class="user"><p>' . Functions::displayUser($user, array('iphide' => 1,)) . '</p></div>' .
     '<div class="list2"><p>' .
     '<h3>' . $lng_profile['personal_data'] . '</h3>' .
     '<ul>';
if (file_exists('../files/users/photo/' . $user['user_id'] . '_small.jpg'))
    echo '<a href="../files/users/photo/' . $user['user_id'] . '.jpg"><img src="../files/users/photo/' . $user['user_id'] . '_small.jpg" alt="' . $user['nickname'] . '" border="0" /></a>';
echo '<li><span class="gray">' . $lng_profile['name'] . ':</span> ' . (empty($user['imname']) ? '' : $user['imname']) . '</li>' .
     '<li><span class="gray">' . $lng_profile['birt'] . ':</span> ' . (empty($user['dayb']) ? '' : sprintf("%02d", $user['dayb']) . '.' . sprintf("%02d", $user['monthb']) . '.' . $user['yearofbirth']) . '</li>' .
     '<li><span class="gray">' . $lng_profile['city'] . ':</span> ' . (empty($user['live']) ? '' : $user['live']) . '</li>' .
     '<li><span class="gray">' . $lng_profile['about'] . ':</span> ' . (empty($user['about']) ? '' : '<br />' . Functions::smileys(TextParser::tags($user['about']))) . '</li>' .
     '</ul></p><p>' .
     '<h3>' . $lng_profile['communication'] . '</h3><ul>' .
     '<li><span class="gray">' . $lng_profile['phone_number'] . ':</span> ' . (empty($user['mibile']) ? '' : $user['mibile']) . '</li>' .
     '<li><span class="gray">E-mail:</span> ';
if (!empty($user['mail']) && $user['mailvis'] || Vars::$USER_RIGHTS >= 7 || $user['user_id'] == Vars::$USER_ID) {
    echo $user['mail'] . ($user['mailvis'] ? '' : '<span class="gray"> [' . $lng_profile['hidden'] . ']</span>');
}
echo '</li>' .
     '<li><span class="gray">ICQ:</span> ' . (empty($user['icq']) ? '' : $user['icq']) . '</li>' .
     '<li><span class="gray">Skype:</span> ' . (empty($user['skype']) ? '' : $user['skype']) . '</li>' .
     '<li><span class="gray">Jabber:</span> ' . (empty($user['jabber']) ? '' : $user['jabber']) . '</li>' .
     '<li><span class="gray">' . $lng_profile['site'] . ':</span> ' . (empty($user['www']) ? '' : TextParser::tags($user['www'])) . '</li>' .
     '</ul></p></div>' .
     '<div class="phdr"><a href="profile.php?user=' . $user['user_id'] . '">' . Vars::$LNG['back'] . '</a></div>';