<?php

/*
////////////////////////////////////////////////////////////////////////////////
// JohnCMS                Mobile Content Management System                    //
// Project site:          http://johncms.com                                  //
// Support site:          http://gazenwagen.com                               //
////////////////////////////////////////////////////////////////////////////////
// Lead Developer:        Oleg Kasyanov   (AlkatraZ)  alkatraz@gazenwagen.com //
// Development Team:      Eugene Ryabinin (john77)    john77@gazenwagen.com   //
//                        Dmitry Liseenko (FlySelf)   flyself@johncms.com     //
////////////////////////////////////////////////////////////////////////////////
*/

defined('_IN_JOHNCMS') or die('Error: restricted access');

/*
-----------------------------------------------------------------
Статистика
-----------------------------------------------------------------
*/
$textl = htmlspecialchars($user['name']) . ': ' . $lng['statistics'];
require('../../incfiles/head.php');
echo '<div class="phdr"><a href="index.php?id=' . $user['id'] . '"><b>' . $lng['profile'] . '</b></a> | ' . $lng['statistics'] . '</div>' .
    '<div class="user"><p>' . display_user($user, array ('iphide' => 1,)) . '</p></div>' .
    '<div class="list2">' .
    '<p><h3><img src="' . $home . '/images/rate.gif" width="16" height="16" class="left" />&#160;' . $lng['statistics'] . '</h3><ul>';
if ($rights >= 7) {
    if (!$user['preg'] && empty($user['regadm']))
        echo '<li>' . $lng_profile['awaiting_registration'] . '</li>';
    elseif ($user['preg'] && !empty($user['regadm']))
        echo '<li>' . $lng_profile['registration_approved'] . ': ' . $user['regadm'] . '</li>';
    else
        echo '<li>' . $lng_profile['registration_free'] . '</li>';
}
echo '<li><span class="gray">' . ($user['sex'] == 'm' ? $lng_profile['registered_m'] : $lng_profile['registered_w']) . ':</span> ' . date("d.m.Y", $user['datereg']) . '</li>' .
    '<li><span class="gray">' . ($user['sex'] == 'm' ? $lng_profile['stayed_m'] : $lng_profile['stayed_w']) . ':</span> ' . timecount($user['total_on_site']) . '</li>';
$lastvisit = $realtime > $user['lastdate'] + 300 ? date("d.m.Y (H:i)", $user['lastdate']) : false;
if ($lastvisit)
    echo '<li><span class="gray">' . $lng['last_visit'] . ':</span> ' . $lastvisit . '</li>';
echo '</ul></p><p>' .
    '<h3><img src="' . $home . '/images/activity.gif" width="16" height="16" class="left" />&#160;' . $lng_profile['activity'] . '</h3><ul>' .
    '<li><span class="gray">' . $lng['forum'] . ':</span> <a href="index.php?act=activity&amp;id=' . $user['id'] . '">' . $user['postforum'] . '</a></li>' .
    '<li><span class="gray">' . $lng['guestbook'] . ':</span> <a href="index.php?act=activity&amp;mod=guest&amp;id=' . $user['id'] . '">' . $user['postguest'] . '</a></li>' .
    '<li><span class="gray">' . $lng['comments'] . ':</span> ' . $user['komm'] . '</li>' .
    '<li><span class="gray">' . $lng['chat'] . ':</span> ' . $user['postchat'] . '</li>' .
    '<li><span class="gray">' . $lng['quiz'] . ':</span> ' . $user['otvetov'] . '</li>' .
    '<li><span class="gray">' . $lng_profile['game_balance'] . ':</span> ' . $user['balans'] . '</li>' .
    '</ul></p>' .
    '<p><h3><img src="' . $home . '/images/award.png" width="16" height="16" class="left" />&#160;' . $lng_profile['achievements'] . '</h3>';
$num = array (
    100,
    500,
    1000,
    5000
);
$query = array (
    'postforum' => $lng['forum'],
    'postguest' => $lng['guestbook'],
    'komm' => $lng['comments'],
    'postchat' => $lng['chat'],
    'otvetov' => $lng['quiz']
);
echo '<table border="0" cellspacing="0" cellpadding="0"><tr>';
foreach ($num as $val) {
    echo '<td width="28" align="center"><small>' . $val . '</small></td>';
}
echo '<td></td></tr>';
foreach ($query as $key => $val) {
    echo '<tr>';
    foreach ($num as $achieve) {
        echo '<td align="center"><img src="' . $home . '/images/' . ($user[$key] >= $achieve ? 'green' : 'red') . '.gif" alt=""/></td>';
    }
    echo '<td><small><b>' . $val . '</b></small></td></tr>';
}
echo '</table></p></div>' .
    '<div class="phdr"><a href="index.php?id=' . $user['id'] . '">' . $lng['back'] . '</a></div>';
?>