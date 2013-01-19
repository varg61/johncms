<?php

if (Vars::$USER_RIGHTS >= 6 && Vars::$ID) {
    if (isset($_POST['submit'])) {
        mysql_query("UPDATE `guest` SET
                    `admin` = '" . mysql_real_escape_string(Vars::$USER_NICKNAME) . "',
                    `otvet` = '" . mysql_real_escape_string(mb_substr($_POST['otv'], 0, 5000)) . "',
                    `otime` = '" . time() . "'
                    WHERE `id` = " . Vars::$ID
        );
        header("Location: " . $url . ($mod ? '?mod=adm' : ''));
    } else {
        echo '<div class="phdr"><a href="' . $url . ($mod ? '?mod=adm' : '') . '"><b>' . __('guestbook') . '</b></a> | ' . __('reply') . '</div>';
        $req = mysql_query("SELECT * FROM `guest` WHERE `id` = " . Vars::$ID);
        $result = mysql_fetch_assoc($req);
        echo'<div class="menu">' .
            '<div class="quote"><b>' . $result['nickname'] . '</b>' .
            '<br />' . Validate::checkout($result['text']) . '</div>' .
            '<form name="form" action="' . $url . '?act=reply&amp;id=' . Vars::$ID . ($mod ? '&amp;mod=adm' : '') . '" method="post">' .
            '<p><h3>' . __('reply') . '</h3>' . TextParser::autoBB('form', 'otv') .
            '<textarea rows="' . Vars::$USER_SET['field_h'] . '" name="otv">' . Validate::checkout($result['otvet']) . '</textarea></p>' .
            '<p><input type="submit" name="submit" value="' . __('reply') . '"/></p>' .
            '</form></div>' .
            '<div class="phdr"><a href="faq.php?act=trans">' . __('translit') . '</a> | <a href="faq.php?act=smilies">' . __('smilies') . '</a></div>' .
            '<p><a href="' . $url . ($mod ? '?mod=adm' : '') . '">' . __('back') . '</a></p>';
    }
}