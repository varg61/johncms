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

defined('_IN_JOHNADM') or die('Error: restricted access');
//TODO: Разобраться с правами доступа и проверкой переменных
echo '<div class="phdr"><a href="index.php"><b>' . $lng['admin_panel'] . '</b></a> | ' . $lng['reg_approve'] . '</div>';
switch ($mod) {
    case 'approve':
        /*
        -----------------------------------------------------------------
        Подтверждаем регистрацию выбранного пользователя
        -----------------------------------------------------------------
        */
        if(!$id){
            echo display_error($lng['wrong_data']);
            require('../incfiles/end.php');
            exit;
        }
        @mysql_query("UPDATE `users` SET `preg` = '1', `regadm` = '$login' WHERE `id` = '$id' LIMIT 1");
        echo '<div class="menu"><p>' . $lng['reg_approved'] . '<br /><a href="index.php?act=usr_reg">' . $lng['continue'] . '</a></p></div>';
        break;

    case 'massapprove':
        /*
        -----------------------------------------------------------------
        Подтверждение всех регистраций
        -----------------------------------------------------------------
        */
        mysql_query("UPDATE `users` SET `preg` = '1', `regadm` = '$login' WHERE `preg` = '0'");    
        echo '<div class="menu"><p>' . $lng['reg_approved'] . '<br /><a href="index.php?act=usr_reg">' . $lng['continue'] . '</a></p></div>';
        break;
    
    case 'del':
        /*
        -----------------------------------------------------------------
        Удаляем выбранного пользователя
        -----------------------------------------------------------------
        */
        if(!$id){
            echo display_error($lng['wrong_data']);
            require('../incfiles/end.php');
            exit;
        }
        $req = mysql_query("SELECT `id` FROM `users` WHERE `id` = '$id' AND `preg` = '0' LIMIT 1");
        if(mysql_num_rows($req)){
            mysql_query("DELETE FROM `users` WHERE `id` = '$id' LIMIT 1");
            mysql_query("DELETE FROM `cms_users_iphistory` WHERE `user_id` = '$id' LIMIT 1");
        }
        echo '<div class="menu"><p>' . $lng['user_deleted'] . '<br /><a href="index.php?act=usr_reg">' . $lng['continue'] . '</a></p></div>';
        break;
    
    case 'massdel':
        /*
        -----------------------------------------------------------------
        Удаление всех регистраций
        -----------------------------------------------------------------
        */
        $req = mysql_query("SELECT `id` FROM `users` WHERE `preg` = '0'");
        while($res = mysql_fetch_assoc($req)){
            mysql_query("DELETE FROM `cms_users_iphistory` WHERE `user_id` = '" . $res['id'] . "'");
        }
        mysql_query("DELETE FROM `users` WHERE `preg` = '0'");
        mysql_query("OPTIMIZE TABLE `cms_users_iphistory` , `users`");
        echo '<div class="menu"><p>' . $lng['reg_deleted_all'] . '<br /><a href="index.php?act=usr_reg">' . $lng['continue'] . '</a></p></div>';
        break;
    
    default:
        /*
        -----------------------------------------------------------------
        Выводим список пользователей, ожидающих подтверждения регистрации
        -----------------------------------------------------------------
        */
        $total = mysql_result(mysql_query("SELECT COUNT(*) FROM `users` WHERE `preg` = '0'"), 0);
        if ($total) {
            $req = mysql_query("SELECT * FROM `users` WHERE `preg` = '0' ORDER BY `id` DESC LIMIT $start,$kmess");
            while ($res = mysql_fetch_assoc($req)) {
                $link = '<a href="index.php?act=usr_reg&amp;mod=approve&amp;id=' . $res['id'] . '">' . $lng['approve'] . '</a> | ';
                $link .= '<a href="index.php?act=usr_reg&amp;mod=del&amp;id=' . $res['id'] . '">' . $lng['delete'] . '</a> | ';
                $link .= '<a href="">' . $lng['reg_del_ip'] . '</a>';
                echo $i % 2 ? '<div class="list2">' : '<div class="list1">';
                echo show_user($res, array('header' => '<b>ID:' . $res['id'] . '</b>', 'sub' => $link));
                echo '</div>';
                ++$i;
            }
        } else {
            echo '<div class="menu"><p>' . $lng['list_empty'] . '</p></div>';
        }
        echo '<div class="phdr">' . $lng['total'] . ': ' . $total . '</div>';
        if ($total > $kmess) {
            echo '<p>' . pagenav('index.php?act=usr_reg&amp;', $start, $total, $kmess) . '</p>';
            echo '<p><form action="index.php?act=usr_reg" method="post"><input type="text" name="page" size="2"/><input type="submit" value="' . $lng['to_page'] . ' &gt;&gt;"/></form></p>';
        }
        echo '<p>';
        if($total)
            echo '<a href="index.php?act=usr_reg&amp;mod=massapprove">' . $lng['reg_approve_all'] . '</a><br /><a href="index.php?act=usr_reg&amp;mod=massdel">' . $lng['reg_del_all'] . '</a><br />';
        echo '<a href="index.php">' . $lng['admin_panel'] . '</a></p>';
}

?>