<?php

if (Vars::$USER_RIGHTS >= 6 && Vars::$ID) {
    if (isset($_GET['yes'])) {
        mysql_query("DELETE FROM `guest` WHERE `id` = " . Vars::$ID);
        header("Location: " . $url . ($mod ? '?mod=adm' : ''));
    } else {
        echo'<div class="phdr"><a href="' . $url . '"><b>' . __('guestbook') . '</b></a> | ' . __('delete_message') . '</div>' .
            '<div class="rmenu"><p>' . __('delete_confirmation') . '?<br/>' .
            '<a href="' . $url . '?act=delpost&amp;id=' . Vars::$ID . ($mod ? '&amp;mod=adm' : '') . '&amp;yes">' . __('delete') . '</a> | ' .
            '<a href="' . $url . ($mod ? '?mod=adm' : '') . '">' . __('cancel') . '</a></p></div>';
    }
}
