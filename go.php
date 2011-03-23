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

define('_IN_JOHNCMS', 1);
$rootpath = '';
require('incfiles/core.php');
$url = isset($_REQUEST['url']) ? strip_tags(html_entity_decode(base64_decode(trim($_REQUEST['url'])))) : false;
if($url){
    /*
    -----------------------------------------------------------------
    Редирект по ссылкам в текстах, обработанным функцией tags()
    -----------------------------------------------------------------
    */
    if(isset($_POST['submit'])){
        header('Location: ' . $url);
    } else {
        require('incfiles/head.php');
        echo '<div class="phdr"><b>' . $lng['external_link'] . '</b></div>' .
            '<div class="rmenu">' .
            '<form action="go.php?url=' . base64_encode($url) . '" method="post">' .
            '<p>' . $lng['redirect_1'] . ':<br /><span class="red">' . functions::checkout($url) . '</span></p>' .
            '<p>' . $lng['redirect_2'] . '.<br />' .
            $lng['redirect_3'] . ' <span class="green">' . $set['homeurl'] . '</span> ' . $lng['redirect_4'] . '.</p>' .
            '<p><input type="submit" name="submit" value="' . $lng['redirect_5'] . '" /></p>' .
            '</form></div>' .
            '<div class="phdr"><a href="' . htmlspecialchars($_SERVER['HTTP_REFERER']) . '">' . $lng['back'] . '</a></div>';
        require('incfiles/end.php');
    }
} elseif ($id) {
    /*
    -----------------------------------------------------------------
    Редирект по рекламной ссылке
    -----------------------------------------------------------------
    */
    $req = mysql_query("SELECT * FROM `cms_ads` WHERE `id` = '$id'");
    if (mysql_num_rows($req)) {
        $res = mysql_fetch_assoc($req);
        $count_link = $res['count'] + 1;
        mysql_query("UPDATE `cms_ads` SET `count` = '$count_link'  WHERE `id` = '$id'");
        header('Location: ' . $res['link']);
    } else {
        header("Location: http://johncms.com/index.php?act=404");
    }
} else {
    /*
    -----------------------------------------------------------------
    Редирект по "быстрому переходу"
    -----------------------------------------------------------------
    */
    $adres = trim($_POST['adres']);
    switch ($adres) {
        case 'forum':
            header('location: ' . $set['homeurl'] . '/forum/index.php');
            break;

        case 'lib':
            header('location: ' . $set['homeurl'] . '/library/index.php');
            break;

        case 'down':
            header('location: ' . $set['homeurl'] . '/download/index.php');
            break;

        case 'gallery':
            header('location: ' . $set['homeurl'] . '/gallery/index.php');
            break;

        case 'news':
            header('location: ' . $set['homeurl'] . '/news/index.php');
            break;

        case 'guest':
            header('location: ' . $set['homeurl'] . '/guestbook/index.php');
            break;
            
        case 'gazen':
            header('location: http://gazenwagen.com');
            break;

        default :
            header('location: http://johncms.com');
            break;
    }
}
?>