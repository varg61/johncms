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
    case 'install':
        $select = isset($_POST['select']) ? $_POST['select'] : false;
        if ($select) {
            foreach($select as $var){
                echo $var . '<br />';
            }
        }
        else {
            echo 'ERROR: wrong data';
        }
        break;

    case 'final': break;

    default:
        echo '<form action="index.php?act=languages&amp;mod=install" method="post"><table>' .
            '<tr><td>&nbsp;</td><td style="padding-bottom:4px"><h3>' . $lng['select_languages_to_install'] . '</h3><small>' . $lng['language_install_note'] . '</small></td></tr>';
        foreach ($lng_array as $key => $val) {
            echo '<tr><td valign="top"><input type="checkbox" name="select[]" value="' . $key . '" ' . ($key == $language ? 'checked="checked" disabled="disabled"' : '') . ' /></td>' .
                '<td>' . ($key == $language ? '<b>' . $val . '</b>' : $val) . '<br /><small>' . $lng['language_alredy_installed'] . '</small></td></tr>';
        }
        echo '<tr><td>&nbsp;</td><td style="padding-top:6px"><input type="submit" name="submit" value="' . $lng['install'] . '" /></td></tr>' .
        '</table>' .
        '<input type="hidden" name="lng" value="' . $language . '" />' .
        '</form>';
}
?>