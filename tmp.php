<?php

/*
-----------------------------------------------------------------
Сброс настроек
-----------------------------------------------------------------
*/
if (Vars::$USER_RIGHTS >= 7 && Vars::$USER_RIGHTS > $user['rights'] && Vars::$ACT == 'reset') {
    mysql_query("UPDATE `users` SET `set_user` = '', `set_forum` = '', `set_chat` = '' WHERE `id` = '" . $user['user_id'] . "'");
    echo '<div class="gmenu"><p>' . Vars::$LNG['settings_default'] . '<br /><a href="profile.php?user=' . $user['user_id'] . '">' . Vars::$LNG['to_form'] . '</a></p></div>';
    exit;
}
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

// Административные функции
if (Vars::$USER_RIGHTS >= 7) {
    echo '<div class="rmenu"><p><h3>' . Vars::$LNG['settings'] . '</h3><ul>';
    if (Vars::$USER_RIGHTS == 9) {
        echo '<li><input name="karma_off" type="checkbox" value="1" ' . ($user['karma_off'] ? 'checked="checked"' : '') . ' />&#160;<span class="red"><b>' . $lng['deny_karma'] . '</b></span></li>';
    }
    echo '<li><a href="profile.php?act=password&amp;user=' . $user['user_id'] . '">' . Vars::$LNG['change_password'] . '</a></li>';
    if(Vars::$USER_RIGHTS > $user['rights'])
        echo '<li><a href="profile.php?act=reset&amp;user=' . $user['user_id'] . '">' . Vars::$LNG['reset_settings'] . '</a></li>';
    echo '<li>' . $lng['specify_sex'] . ':<br />' .
        '<input type="radio" value="m" name="sex" ' . ($user['sex'] == 'm' ? 'checked="checked"' : '') . '/>&#160;' . $lng['sex_m'] . '<br />' .
        '<input type="radio" value="zh" name="sex" ' . ($user['sex'] == 'zh' ? 'checked="checked"' : '') . '/>&#160;' . $lng['sex_w'] . '</li>' .
        '</ul></p>';
    if ($user['user_id'] != Vars::$USER_ID) {
        echo '<p><h3>' . $lng['rank'] . '</h3><ul>' .
            '<input type="radio" value="0" name="rights" ' . (!$user['rights'] ? 'checked="checked"' : '') . '/>&#160;<b>' . $lng['rank_0'] . '</b><br />' .
            '<input type="radio" value="3" name="rights" ' . ($user['rights'] == 3 ? 'checked="checked"' : '') . '/>&#160;' . $lng['rank_3'] . '<br />' .
            '<input type="radio" value="4" name="rights" ' . ($user['rights'] == 4 ? 'checked="checked"' : '') . '/>&#160;' . $lng['rank_4'] . '<br />' .
            '<input type="radio" value="5" name="rights" ' . ($user['rights'] == 5 ? 'checked="checked"' : '') . '/>&#160;' . $lng['rank_5'] . '<br />' .
            '<input type="radio" value="6" name="rights" ' . ($user['rights'] == 6 ? 'checked="checked"' : '') . '/>&#160;' . $lng['rank_6'] . '<br />';
        if (Vars::$USER_RIGHTS == 9) {
            echo '<input type="radio" value="7" name="rights" ' . ($user['rights'] == 7 ? 'checked="checked"' : '') . '/>&#160;' . $lng['rank_7'] . '<br />' .
                '<input type="radio" value="9" name="rights" ' . ($user['rights'] == 9 ? 'checked="checked"' : '') . '/>&#160;<span class="red"><b>' . $lng['rank_9'] . '</b></span><br />';
        }