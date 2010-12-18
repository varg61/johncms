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
if ($install || $update)
    die('ERROR: installation of languages is impossible</body></html>');
switch ($_GET['mod']) {
    case 'install':
        $select = isset($_POST['select']) ? $_POST['select'] : false;
        if ($select) {
            echo '<h3 class="blue">' . $lng['install_languages'] . '</h3>';
            foreach ($select as $var) {
                $req = mysql_query("SELECT * FROM `cms_lng_list` WHERE `iso` = '" . $lng_set[$var]['iso'] . "' LIMIT 1");
                if (mysql_num_rows($req)) {
                    $res = mysql_fetch_assoc($req);
                    // Удаляем выбранный язык
                    mysql_query("DELETE FROM `cms_lng_phrases` WHERE `language_id` = '" . $res['id'] . "'");
                    mysql_query("DELETE FROM `cms_lng_list` WHERE `id` = '" . $res['id'] . "'");
                    mysql_query("OPTIMIZE TABLE `cms_lng_list` , `cms_lng_phrases`");
                    $lng_setup = $lng['updated'];
                } else {
                    $lng_setup = $lng['installed'];
                }
                /*
                -----------------------------------------------------------------
                Добавляем в базу выбранный язык
                -----------------------------------------------------------------
                */
                $attr = serialize(array (
                    'name' => $lng_set[$var]['name'],
                    'author' => $lng_set[$var]['author'],
                    'author_email' => $lng_set[$var]['author_email'],
                    'author_url' => $lng_set[$var]['author_url'],
                    'description' => $lng_set[$var]['description'],
                    'version' => $lng_set[$var]['version']
                ));
                // Добавляем в список
                mysql_query("INSERT INTO `cms_lng_list` SET
                    `iso` = '" . $lng_set[$var]['iso'] . "',
                    `attr` = '" . mysql_real_escape_string($attr) . "'
                ");
                $language_id = mysql_insert_id();
                // Добавляем фразы
                $lng_array = parse_ini_file('languages/' . $lng_set[$var]['filename'] . '.ini', true);
                unset($lng_array['description']); // Удаляем описание языка
                unset($lng_array['install']); // Удаляем фразы инсталлятора
                foreach($lng_array as $module => $phr_array){
                    foreach($phr_array as $keyword => $phrase){
                        mysql_query("INSERT INTO `cms_lng_phrases` SET
                            `language_id` = '$language_id',
                            `module` = '" . mysql_real_escape_string($module) . "',
                            `keyword` = '" . mysql_real_escape_string($keyword) . "',
                            `default` = '" . mysql_real_escape_string($phrase) . "'
                        ");
                    }
                }
                echo '<div>&#160;<span class="green"><b>' . $lng_setup . '</b></span>&#160;' . $lng_set[$var]['name'] . '</div>';
            }
            echo '<p><a href="index.php?act=languages&amp;lng_id=' . $lng_id . '">' . $lng['back'] . '</a><br />' .
                '<a href="index.php?lng_id=' . $lng_id . '">' . $lng['home'] . '</a></p>';
        } else {
            echo $lng['error_select_language'];
            echo '<form action="index.php">' .
                '<input type="hidden" name="act" value="languages">' .
                '<input type="hidden" name="lng_id" value="' . $lng_id . '">' .
                '<p><input type="submit" name="submit" value="' . $lng['back'] . '"></p>' .
                '</form>';
        }
        break;

    default:
        /*
        -----------------------------------------------------------------
        Выбор языков
        -----------------------------------------------------------------
        */
        echo '<form action="index.php?act=languages&amp;mod=install" method="post"><table>' .
            '<tr><td>&nbsp;</td><td style="padding-bottom:4px"><h3 class="blue">' . $lng['select_languages_to_install'] . '</h3><small>' . $lng['language_install_note'] . '</small></td></tr>';
        foreach ($lng_set as $key => $val) {
            echo '<tr>' .
                '<td valign="top"><input type="checkbox" name="select[]" value="' . $key . '"/></td>' .
                '<td><b>' . $val['name'] . '</b>' . (isset($language) && !empty($language) && $language == $val['iso'] ? ' <small class="red">[' . $lng['system'] . ']</small>' : '') . '<br /><small>' . $lng['language_alredy_installed'] . '</small></td>' .
                '</tr>';
        }
        echo '<tr><td>&nbsp;</td><td style="padding-top:6px"><input type="submit" name="submit" value="' . $lng['install'] . '" /></td></tr>' .
            '</table>' .
            '<input type="hidden" name="lng_id" value="' . $lng_id . '" />' .
            '</form>' .
            '<a href="index.php?lng_id=' . $lng_id . '">' . $lng['home'] . '</a>';
}
?>