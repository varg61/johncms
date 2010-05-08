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

////////////////////////////////////////////////////////////
// Удаление выбранных постов с форума                     //
////////////////////////////////////////////////////////////
if ($rights == 3 || $rights >= 6) {
    require_once ("../incfiles/head.php");
    if (isset ($_GET['yes'])) {
        $dc = $_SESSION['dc'];
        $prd = $_SESSION['prd'];
        foreach ($dc as $delid) {
            mysql_query("UPDATE `forum` SET  `close` = '1', `close_who` = '$login' WHERE `id`='" . intval($delid) . "';");
        }
        echo "Отмеченные посты удалены<br/><a href='" . $prd . "'>Назад</a><br/>";
    }
    else {
        if (empty ($_POST['delch'])) {
            echo '<p>Вы ничего не выбрали для удаления<br/><a href="' . htmlspecialchars(getenv("HTTP_REFERER")) . '">Назад</a></p>';
            require_once ("../incfiles/end.php");
            exit;
        }
        foreach ($_POST['delch'] as $v) {
            $dc[] = intval($v);
        }
        $_SESSION['dc'] = $dc;
        $_SESSION['prd'] = htmlspecialchars(getenv("HTTP_REFERER"));
        echo '<p>Вы уверены в удалении постов?<br/><a href="index.php?act=massdel&amp;yes">Да</a> | <a href="' . htmlspecialchars(getenv("HTTP_REFERER")) . '">Нет</a></p>';
    }
}

?>