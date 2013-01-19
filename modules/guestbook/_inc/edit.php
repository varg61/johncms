<?php

if (Vars::$USER_RIGHTS >= 6 && Vars::$ID) {
    if (isset($_POST['submit'])) {
        $req = mysql_query("SELECT `edit_count` FROM `guest` WHERE `id` = " . Vars::$ID);
        $result = mysql_fetch_array($req);
        $edit_count = $result['edit_count'] + 1;
        $msg = mb_substr($_POST['msg'], 0, 5000);
        mysql_query("UPDATE `guest` SET
                    `text` = '" . mysql_real_escape_string($msg) . "',
                    `edit_who` = '" . mysql_real_escape_string(Vars::$USER_NICKNAME) . "',
                    `edit_time` = '" . time() . "',
                    `edit_count` = '$edit_count'
                    WHERE `id` = " . Vars::$ID
        );
        header("location: " . $url);
    } else {
        $req = mysql_query("SELECT * FROM `guest` WHERE `id` = " . Vars::$ID);
        $result = mysql_fetch_assoc($req);
        $text = htmlentities($result['text'], ENT_QUOTES, 'UTF-8');
        echo '<div class="phdr"><a href="' . $url . '"><b>' . __('guestbook') . '</b></a> | ' . __('edit') . '</div>' .
            '<div class="rmenu">' .
            '<form action="' . $url . '?act=edit&amp;id=' . Vars::$ID . ($mod ? '&amp;mod=adm' : '') . '" method="post">' .
            '<p><b>' . __('author') . ':</b> ' . $result['nickname'] . '</p>' .
            '<p><textarea rows="' . Vars::$USER_SET['field_h'] . '" name="msg">' . $text . '</textarea></p>' .
            '<p><input type="submit" name="submit" value="' . __('save') . '"/></p>' .
            '</form></div>' .
            '<div class="phdr"><a href="faq.php?act=trans">' . __('translit') . '</a> | <a href="faq.php?act=smilies">' . __('smilies') . '</a></div>' .
            '<p><a href="' . $url . '">' . __('back') . '</a></p>';
    }
}
