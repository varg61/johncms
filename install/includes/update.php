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

defined('INSTALL') or die('Error: restricted access');

switch ($_GET['mod']) {
    default:
        echo '<b class="gray">' . $lng['step'] . ': <span style="font-size: x-large; color: #FF0000">1</span> - 2 - 3 - 4</b>';
        echo $lng['update_warning'] . '<hr />' . $lng['update_final_notice'];
        echo '<hr />Вы уверены? У Вас есть резервная копия базы данных?<br /><a href="update.php?do=step1">Начать обновление</a>';
}

?>
