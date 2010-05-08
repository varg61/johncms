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

if (isset($_POST['submit'])) {
    if (!empty($_POST['simvol'])) {
        $simvol = intval($_POST['simvol']);
    }
    $_SESSION['symb'] = $simvol;
    echo "На время текущей сессии <br/>принято количество символов на страницу: $simvol <br/>";
} else {
    echo "<form action='?act=symb' method='post'>
    Выберите количество символов на страницу:<br/><select name='simvol'>";
    if (!empty($_SESSION['symb'])) {
        $realr = $_SESSION['symb'];
        echo "<option value='" . $realr . "'>" . $realr . "</option>";
    }
    echo "<option value='500'>500</option>
    <option value='1000'>1000</option>
    <option value='2000'>2000</option>
    <option value='3000'>3000</option>
    <option value='4000'>4000</option>
    </select><br/>
    <input type='submit' name='submit' value='ok'/></form>";
}
echo "&#187;<a href='?'>К категориям</a><br/>";

?>