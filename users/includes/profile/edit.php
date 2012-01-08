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

$textl = htmlspecialchars($user['nickname']) . ': ' . $lng_profile['profile_edit'];
require_once('../includes/head.php');

/*
-----------------------------------------------------------------
Проверяем права доступа для редактирования Профиля
-----------------------------------------------------------------
*/
if ($user['user_id'] != Vars::$USER_ID && (Vars::$USER_RIGHTS < 7 || $user['rights'] > Vars::$USER_RIGHTS)) {
    echo Functions::displayError($lng_profile['error_rights']);
    require_once('../includes/end.php');
    exit;
}

/*
-----------------------------------------------------------------
Сброс настроек
-----------------------------------------------------------------
*/
if (Vars::$USER_RIGHTS >= 7 && Vars::$USER_RIGHTS > $user['rights'] && Vars::$ACT == 'reset') {
    mysql_query("UPDATE `users` SET `set_user` = '', `set_forum` = '', `set_chat` = '' WHERE `id` = '" . $user['user_id'] . "'");
    echo '<div class="gmenu"><p>' . Vars::$LNG['settings_default'] . '<br /><a href="profile.php?user=' . $user['user_id'] . '">' . Vars::$LNG['to_form'] . '</a></p></div>';
    require_once('../includes/end.php');
    exit;
}
echo '<div class="phdr"><a href="profile.php?user=' . $user['user_id'] . '"><b>' . ($user['user_id'] != Vars::$USER_ID ? Vars::$LNG['profile'] : $lng_profile['my_profile']) . '</b></a> | ' . Vars::$LNG['edit'] . '</div>';
if (isset($_GET['delavatar'])) {
    /*
    -----------------------------------------------------------------
    Удаляем аватар
    -----------------------------------------------------------------
    */
    @unlink('../files/users/avatar/' . $user['user_id'] . '.png');
    echo '<div class="rmenu">' . $lng_profile['avatar_deleted'] . '</div>';
} elseif (isset($_GET['delphoto'])) {
    /*
    -----------------------------------------------------------------
    Удаляем фото
    -----------------------------------------------------------------
    */
    @unlink('../files/users/photo/' . $user['user_id'] . '.jpg');
    @unlink('../files/users/photo/' . $user['user_id'] . '_small.jpg');
    echo '<div class="rmenu">' . $lng_profile['photo_deleted'] . '</div>';
} elseif (isset($_POST['submit'])) {
    /*
    -----------------------------------------------------------------
    Принимаем данные из формы, проверяем и записываем в базу
    -----------------------------------------------------------------
    */
    //TODO: Доработать!
//    $error = array ();
//    $user['imname'] = isset($_POST['imname']) ? Validate::filterString(mb_substr($_POST['imname'], 0, 25)) : '';
//    $user['live'] = isset($_POST['live']) ? Validate::filterString(mb_substr($_POST['live'], 0, 50)) : '';
//    $user['dayb'] = isset($_POST['dayb']) ? intval($_POST['dayb']) : 0;
//    $user['monthb'] = isset($_POST['monthb']) ? intval($_POST['monthb']) : 0;
//    $user['yearofbirth'] = isset($_POST['yearofbirth']) ? intval($_POST['yearofbirth']) : 0;
//    $user['about'] = isset($_POST['about']) ? Validate::filterString(mb_substr($_POST['about'], 0, 500)) : '';
//    $user['mibile'] = isset($_POST['mibile']) ? Validate::filterString(mb_substr($_POST['mibile'], 0, 40)) : '';
//    $user['mail'] = isset($_POST['mail']) ? Validate::filterString(mb_substr($_POST['mail'], 0, 40)) : '';
//    $user['mailvis'] = isset($_POST['mailvis']) ? 1 : 0;
//    $user['icq'] = isset($_POST['icq']) ? intval($_POST['icq']) : 0;
//    $user['skype'] = isset($_POST['skype']) ? Validate::filterString(mb_substr($_POST['skype'], 0, 40)) : '';
//    $user['jabber'] = isset($_POST['jabber']) ? Validate::filterString(mb_substr($_POST['jabber'], 0, 40)) : '';
//    $user['www'] = isset($_POST['www']) ? Validate::filterString(mb_substr($_POST['www'], 0, 40)) : '';
//    // Данные юзера (для Администраторов)
//    $user['nickname'] = isset($_POST['name']) ? Validate::filterString(mb_substr($_POST['name'], 0, 20)) : $user['nickname'];
//    $user['status'] = isset($_POST['status']) ? Validate::filterString(mb_substr($_POST['status'], 0, 50)) : '';
//    $user['karma_off'] = isset($_POST['karma_off']);
//    $user['sex'] = isset($_POST['sex']) && $_POST['sex'] == 'm' ? 'm' : 'zh';
//    $user['rights'] = isset($_POST['rights']) ? abs(intval($_POST['rights'])) : $user['rights'];
//    // Проводим необходимые проверки
//    //TODO: Доработать!
//    if($user['rights'] > Vars::$user_rights || $user['rights'] > 9 || $user['rights'] < 0)
//        $user['rights'] = 0;
//    if (Vars::$user_rights >= 7) {
//        if (mb_strlen($user['name']) < 2 || mb_strlen($user['name']) > 20)
//            $error[] = $lng_profile['error_nick_lenght'];
//        $lat_nick = Functions::rus_lat(mb_strtolower($user['name']));
//        if (preg_match("/[^0-9a-z\-\@\*\(\)\?\!\~\_\=\[\]]+/", $lat_nick))
//            $error[] = $lng_profile['error_nick_symbols'];
//    }
//    if ($user['dayb'] || $user['monthb'] || $user['yearofbirth']) {
//        if ($user['dayb'] < 1 || $user['dayb'] > 31 || $user['monthb'] < 1 || $user['monthb'] > 12)
//            $error[] = $lng_profile['error_birth'];
//    }
//    if ($user['icq'] && ($user['icq'] < 10000 || $user['icq'] > 999999999))
//        $error[] = $lng_profile['error_icq'];
//    if (!$error) {
//        mysql_query("UPDATE `users` SET
//            `imname` = '" . $user['imname'] . "',
//            `live` = '" . $user['live'] . "',
//            `dayb` = '" . $user['dayb'] . "',
//            `monthb` = '" . $user['monthb'] . "',
//            `yearofbirth` = '" . $user['yearofbirth'] . "',
//            `about` = '" . $user['about'] . "',
//            `mibile` = '" . $user['mibile'] . "',
//            `mail` = '" . $user['mail'] . "',
//            `mailvis` = '" . $user['mailvis'] . "',
//            `icq` = '" . $user['icq'] . "',
//            `skype` = '" . $user['skype'] . "',
//            `jabber` = '" . $user['jabber'] . "',
//            `www` = '" . $user['www'] . "'
//            WHERE `id` = '" . $user['user_id'] . "'
//        ");
//        if (Vars::$user_rights >= 7) {
//            mysql_query("UPDATE `users` SET
//                `name` = '" . $user['name'] . "',
//                `status` = '" . $user['status'] . "',
//                `karma_off` = '" . $user['karma_off'] . "',
//                `sex` = '" . $user['sex'] . "',
//                `rights` = '" . $user['rights'] . "'
//                WHERE `id` = '" . $user['user_id'] . "'
//            ");
//        }
//        echo '<div class="gmenu">' . $lng_profile['data_saved'] . '</div>';
//    } else {
//        echo Functions::display_error($error);
//    }
}

/*
-----------------------------------------------------------------
Форма редактирования анкеты пользователя
-----------------------------------------------------------------
*/
echo '<form action="profile.php?act=edit&amp;user=' . $user['user_id'] . '" method="post">' .
    '<div class="gmenu"><p>' .
    '<h3>ID: ' . $user['user_id'] . '</h3>';
if (Vars::$USER_RIGHTS >= 7) {
    echo Vars::$LNG['nick'] . ': (' . $lng_profile['nick_lenght'] . ')<br /><input type="text" value="' . $user['name'] . '" name="name" /><br />' .
        Vars::$LNG['status'] . ': (' . $lng_profile['status_lenght'] . ')<br /><input type="text" value="' . $user['status'] . '" name="status" /><br />';
} else {
    echo '<span class="gray">' . Vars::$LNG['nick'] . ':</span> <b>' . $user['name'] . '</b><br />' .
        '<span class="gray">' . Vars::$LNG['status'] . ':</span> ' . $user['status'] . '<br />';
}
echo '</p><p>' . Vars::$LNG['avatar'] . ':<br />';
$link = '';
if (file_exists(('../files/users/avatar/' . $user['user_id'] . '.png'))) {
    echo '<img src="../files/users/avatar/' . $user['user_id'] . '.png" width="32" height="32" alt="' . $user['name'] . '" /><br />';
    $link = ' | <a href="profile.php?act=edit&amp;user=' . $user['user_id'] . '&amp;delavatar">' . Vars::$LNG['delete'] . '</a>';
}
echo '<small><a href="profile.php?act=images&amp;mod=avatar&amp;user=' . $user['user_id'] . '">' . $lng_profile['upload'] . '</a>';
if($user['user_id'] == Vars::$USER_ID)
    echo ' | <a href="../pages/faq.php?act=avatars">' . Vars::$LNG['select'] . '</a>';
echo $link . '</small></p>';
echo '<p>' . $lng_profile['photo'] . ':<br />';
$link = '';
if (file_exists(('../files/users/photo/' . $user['user_id'] . '_small.jpg'))) {
    echo '<a href="../files/users/photo/' . $user['user_id'] . '.jpg"><img src="../../files/users/photo/' . $user['user_id'] . '_small.jpg" alt="' . $user['name'] . '" border="0" /></a><br />';
    $link = ' | <a href="profile.php?act=edit&amp;user=' . $user['user_id'] . '&amp;delphoto">' . Vars::$LNG['delete'] . '</a>';
}
echo '<small><a href="profile.php?act=images&amp;mod=up_photo&amp;user=' . $user['user_id'] . '">' . $lng_profile['upload'] . '</a>' . $link . '</small><br />' .
    '</p></div>' .
    '<div class="menu">' .
    '<p><h3>' . $lng_profile['personal_data'] . '</h3>' .
    $lng_profile['name'] . ':<br /><input type="text" value="' . $user['imname'] . '" name="imname" /></p>' .
    '<p>' . $lng_profile['birth_date'] . '<br />' .
    '<input type="text" value="' . $user['dayb'] . '" size="2" maxlength="2" name="dayb" />.' .
    '<input type="text" value="' . $user['monthb'] . '" size="2" maxlength="2" name="monthb" />.' .
    '<input type="text" value="' . $user['yearofbirth'] . '" size="4" maxlength="4" name="yearofbirth" /></p>' .
    '<p>' . $lng_profile['city'] . ':<br /><input type="text" value="' . $user['live'] . '" name="live" /></p>' .
    '<p>' . $lng_profile['about'] . ':<br /><textarea rows="' . Vars::$USER_SET['field_h'] . '" name="about">' . str_replace('<br />', "\r\n", $user['about']) . '</textarea></p>' .
    '<p><h3>' . $lng_profile['communication'] . '</h3>' .
    $lng_profile['phone_number'] . ':<br /><input type="text" value="' . $user['mibile'] . '" name="mibile" /><br />' .
    '</p><p>E-mail:<br /><small>' . $lng_profile['email_warning'] . '</small><br />' .
    '<input type="text" value="' . $user['mail'] . '" name="mail" /><br />' .
    '<input name="mailvis" type="checkbox" value="1" ' . ($user['mailvis'] ? 'checked="checked"' : '') . ' />&#160;' . $lng_profile['show_in_profile'] . '</p>' .
    '<p>ICQ:<br /><input type="text" value="' . $user['icq'] . '" name="icq" size="10" maxlength="10" /></p>' .
    '<p>Skype:<br /><input type="text" value="' . $user['skype'] . '" name="skype" /></p>' .
    '<p>Jabber:<br /><input type="text" value="' . $user['jabber'] . '" name="jabber" /></p>' .
    '<p>' . $lng_profile['site'] . ':<br /><input type="text" value="' . $user['www'] . '" name="www" /></p>' .
    '</div>';
// Административные функции
if (Vars::$USER_RIGHTS >= 7) {
    echo '<div class="rmenu"><p><h3>' . Vars::$LNG['settings'] . '</h3><ul>';
    if (Vars::$USER_RIGHTS == 9) {
        echo '<li><input name="karma_off" type="checkbox" value="1" ' . ($user['karma_off'] ? 'checked="checked"' : '') . ' />&#160;<span class="red"><b>' . $lng_profile['deny_karma'] . '</b></span></li>';
    }
    echo '<li><a href="profile.php?act=password&amp;user=' . $user['user_id'] . '">' . Vars::$LNG['change_password'] . '</a></li>';
    if(Vars::$USER_RIGHTS > $user['rights'])
        echo '<li><a href="profile.php?act=reset&amp;user=' . $user['user_id'] . '">' . Vars::$LNG['reset_settings'] . '</a></li>';
    echo '<li>' . $lng_profile['specify_sex'] . ':<br />' .
        '<input type="radio" value="m" name="sex" ' . ($user['sex'] == 'm' ? 'checked="checked"' : '') . '/>&#160;' . $lng_profile['sex_m'] . '<br />' .
        '<input type="radio" value="zh" name="sex" ' . ($user['sex'] == 'zh' ? 'checked="checked"' : '') . '/>&#160;' . $lng_profile['sex_w'] . '</li>' .
        '</ul></p>';
    if ($user['user_id'] != Vars::$USER_ID) {
        echo '<p><h3>' . $lng_profile['rank'] . '</h3><ul>' .
            '<input type="radio" value="0" name="rights" ' . (!$user['rights'] ? 'checked="checked"' : '') . '/>&#160;<b>' . $lng_profile['rank_0'] . '</b><br />' .
            '<input type="radio" value="3" name="rights" ' . ($user['rights'] == 3 ? 'checked="checked"' : '') . '/>&#160;' . $lng_profile['rank_3'] . '<br />' .
            '<input type="radio" value="4" name="rights" ' . ($user['rights'] == 4 ? 'checked="checked"' : '') . '/>&#160;' . $lng_profile['rank_4'] . '<br />' .
            '<input type="radio" value="5" name="rights" ' . ($user['rights'] == 5 ? 'checked="checked"' : '') . '/>&#160;' . $lng_profile['rank_5'] . '<br />' .
            '<input type="radio" value="6" name="rights" ' . ($user['rights'] == 6 ? 'checked="checked"' : '') . '/>&#160;' . $lng_profile['rank_6'] . '<br />';
        if (Vars::$USER_RIGHTS == 9) {
            echo '<input type="radio" value="7" name="rights" ' . ($user['rights'] == 7 ? 'checked="checked"' : '') . '/>&#160;' . $lng_profile['rank_7'] . '<br />' .
                '<input type="radio" value="9" name="rights" ' . ($user['rights'] == 9 ? 'checked="checked"' : '') . '/>&#160;<span class="red"><b>' . $lng_profile['rank_9'] . '</b></span><br />';
        }
        echo '</ul></p>';
    }
    echo '</div>';
}
echo '<div class="gmenu"><input type="submit" value="' . Vars::$LNG['save'] . '" name="submit" /></div>' .
    '</form>' .
    '<div class="phdr"><a href="profile.php?user=' . $user['user_id'] . '">' . Vars::$LNG['to_form'] . '</a></div>';
?>