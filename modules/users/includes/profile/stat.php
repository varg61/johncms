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

//TODO: Доработать под новую статистику!

/*
-----------------------------------------------------------------
Статистика
-----------------------------------------------------------------
*/
$textl = htmlspecialchars($user['nickname']) . ': ' . Vars::$LNG['statistics'];
echo'<div class="phdr"><a href="profile.php?user=' . $user['user_id'] . '"><b>' . Vars::$LNG['profile'] . '</b></a> | ' . Vars::$LNG['statistics'] . '</div>' .
    '<div class="user"><p>' . Functions::displayUser($user, array('iphide' => 1,)) . '</p></div>' .
    '<div class="list2">' .
    '<p><h3>' . Functions::getImage('rating.png', '', 'class="left"') . '&#160;' . Vars::$LNG['statistics'] . '</h3><ul>';
if (Vars::$USER_RIGHTS >= 7) {
    if (!$user['preg'] && empty($user['regadm']))
        echo'<li>' . $lng_profile['awaiting_registration'] . '</li>';
    elseif ($user['preg'] && !empty($user['regadm']))
        echo'<li>' . $lng_profile['registration_approved'] . ': ' . $user['regadm'] . '</li>';
    else
        echo'<li>' . $lng_profile['registration_free'] . '</li>';
}
$lastvisit = time() > $user['last_visit'] + 300 ? date("d.m.Y (H:i)", $user['last_visit']) : false;
if ($lastvisit) echo '<li><span class="gray">' . Vars::$LNG['last_visit'] . ':</span> ' . $lastvisit . '</li>';
echo'<li><span class="gray">' . ($user['sex'] == 'm' ? $lng_profile['registered_m'] : $lng_profile['registered_w']) . ':</span> ' . date("d.m.Y (H:i)", $user['join_date']) . '</li>' .
    '<li><span class="gray">' . ($user['sex'] == 'm' ? $lng_profile['stayed_m'] : $lng_profile['stayed_w']) . ':</span> ' . Functions::timeCount($user['total_on_site']) . '</li>';
echo'</ul></p><p>' .
    '<h3>' . Functions::getImage('user_edit.png', '', 'class="left"') . '&#160;' . $lng_profile['activity'] . '</h3><ul>' .
    '<li><span class="gray">' . Vars::$LNG['forum'] . ':</span> <a href="profile.php?act=activity&amp;user=' . $user['user_id'] . '">' . $user['postforum'] . '</a></li>' .
    '<li><span class="gray">' . Vars::$LNG['guestbook'] . ':</span> <a href="profile.php?act=activity&amp;mod=guest&amp;user=' . $user['user_id'] . '">' . $user['postguest'] . '</a></li>' .
    '<li><span class="gray">' . Vars::$LNG['comments'] . ':</span> ' . $user['komm'] . '</li>' .
    '</ul></p>' .
    '<p><h3>' . Functions::getImage('award.png', '', 'class="left"') . '&#160;' . $lng_profile['achievements'] . '</h3>';
$num = array(
    50,
    100,
    500,
    1000,
    5000
);
$query = array(
    'postforum' => Vars::$LNG['forum'],
    'postguest' => Vars::$LNG['guestbook'],
    'komm' => Vars::$LNG['comments']
);
echo '<table border="0" cellspacing="0" cellpadding="0"><tr>';
foreach ($num as $val) {
    echo'<td width="28" align="center"><small>' . $val . '</small></td>';
}
echo'<td></td></tr>';
foreach ($query as $key => $val) {
    echo '<tr>';
    foreach ($num as $achieve) {
        echo'<td align="center">' . Functions::getImage(($user[$key] >= $achieve ? 'green' : 'red') . '.png') . '</td>';
    }
    echo'<td><small><b>' . $val . '</b></small></td></tr>';
}
echo'</table></p></div>' .
    '<div class="phdr"><a href="profile.php?user=' . $user['user_id'] . '">' . Vars::$LNG['back'] . '</a></div>';