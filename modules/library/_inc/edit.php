<?php

/**
 * @package     JohnCMS
 * @link        http://johncms.com
 * @copyright   Copyright (C) 2008-2012 JohnCMS Community
 * @license     LICENSE.txt (see attached file)
 * @version     VERSION.txt (see attached file)
 * @author      http://johncms.com/about
 */

defined('_IN_JOHNCMS') or die('Error: restricted access');
$url = Router::getUrl(2);

if (Vars::$USER_RIGHTS == 5 || Vars::$USER_RIGHTS >= 6) {
    if ($_GET['id'] == "" || $_GET['id'] == "0") {
        echo "";
        exit;
    }
    $req = mysql_query("SELECT * FROM `lib` WHERE `id` = " . Vars::$ID);
    $ms = mysql_fetch_array($req);
    if (isset($_POST['submit'])) {
        switch ($ms['type']) {
            case "bk":
                ////////////////////////////////////////////////////////////
                // Сохраняем отредактированную статью                     //
                ////////////////////////////////////////////////////////////
                if (empty($_POST['name'])) {
                    echo Functions::displayError(__('error_empty_title'), '<a href="' . $url . '?act=edit&amp;id=' . Vars::$ID . '">' . __('repeat') . '</a>');
                    exit;
                }
                if (empty($_POST['text'])) {
                    echo Functions::displayError(__('error_empty_text'), '<a href="' . $url . '?act=edit&amp;id=' . Vars::$ID . '">' . __('repeat') . '</a>');
                    exit;
                }
                $text = trim($_POST['text']);
                $autor = isset($_POST['autor']) ? Validate::checkout($_POST['autor']) : '';
                $count = isset($_POST['count']) ? abs(intval($_POST['count'])) : '0';
                if (!empty($_POST['anons'])) {
                    $anons = mb_substr(trim($_POST['anons']), 0, 100);
                } else {
                    $anons = mb_substr($text, 0, 100);
                }
                mysql_query("UPDATE `lib` SET
                    `name` = '" . mysql_real_escape_string(mb_substr(trim($_POST['name']), 0, 100)) . "',
                    `announce` = '" . mysql_real_escape_string($anons) . "',
                    `text` = '" . mysql_real_escape_string($text) . "',
                    `avtor` = '" . mysql_real_escape_string($autor) . "',
                    `count` = '$count'
                    WHERE `id` = " . Vars::$ID
                );
                header('location: ' . $url . '?id=' . Vars::$ID);
                break;

            case "cat":
                ////////////////////////////////////////////////////////////
                // Сохраняем отредактированную категорию                  //
                ////////////////////////////////////////////////////////////
                $text = Validate::checkout($_POST['text']);
                if (!empty($_POST['user'])) {
                    $user = intval($_POST['user']);
                } else {
                    $user = 0;
                }
                $mod = intval($_POST['mod']);
                mysql_query("UPDATE `lib` SET
                `text` = '" . mysql_real_escape_string($text) . "',
                `ip` = '" . $mod . "',
                `soft` = '" . $user . "'
                WHERE `id` = " . Vars::$ID);
                header('location: ' . $url . '?id=' . Vars::$ID);
                break;
            default :
                ////////////////////////////////////////////////////////////
                // Сохраняем отредактированный комментарий                //
                ////////////////////////////////////////////////////////////
                $text = Validate::checkout($_POST['text']);
                mysql_query("update `lib` set `text` = '" . mysql_real_escape_string($text) . "' where `id` = " . Vars::$ID);
                header("location: " . $url . "?id=$ms[refid]");
                break;
        }
    } else {
        switch ($ms['type']) {
            case 'bk':
                ////////////////////////////////////////////////////////////
                // Форма редактирования статьи                            //
                ////////////////////////////////////////////////////////////
                echo '<div class="phdr"><b>' . __('edit_article') . '</b></div>' .
                     '<form action="' . $url . '?act=edit&amp;id=' . Vars::$ID . '" method="post">' .
                     '<div class="menu"><p><h3>' . __('title') . '</h3><input type="text" name="name" value="' . htmlentities($ms['name'], ENT_QUOTES, 'UTF-8') . '"/></p>' .
                     '<p><h3>' . __('announce') . '</h3><small>' . __('announce_help') . '</small><br/><input type="text" name="anons" value="' . htmlentities($ms['announce'], ENT_QUOTES, 'UTF-8') . '"/></p>' .
                     '<p><h3>' . __('text') . '</h3><textarea rows="5" name="text">' . htmlentities($ms['text'], ENT_QUOTES, 'UTF-8') . '</textarea></p></div>' .
                     '<div class="rmenu"><p><h3>' . __('author') . '</h3><input type="text" name="autor" value="' . $ms['avtor'] . '"/></p>' .
                     '<p><h3>' . __('reads') . '</h3><input type="text" name="count" value="' . $ms['count'] . '" size="4"/></p></div>' .
                     '<div class="bmenu"><input type="submit" name="submit" value="' . __('save') . '"/></div></form>' .
                     '<p><a href="' . $url . '?id=' . Vars::$ID . '">' . __('back') . '</a></p>';
                break;

            case "cat":
                echo __('edit_category') . "<br/><form action='" . $url . "?act=edit&amp;id=" . Vars::$ID . "' method='post'><input type='text' name='text' value='" . $ms['text'] .
                     "'/><br/>" . __('edit_category_help') . ":<br/><select name='mod'>";
                if ($ms['ip'] == 1) {
                    echo "<option value='1'>" . __('categories') . "</option><option value='0'>" . __('articles') . "</option>";
                } else {
                    echo "<option value='0'>" . __('articles') . "</option><option value='1'>" . __('categories') . "</option>";
                }
                echo "</select><br/>";
                if ($ms['soft'] == 1) {
                    echo __('allow_to_add') . "<br/><input type='checkbox' name='user' value='1' checked='checked' /><br/>";
                } else {
                    echo __('allow_to_add') . "<br/><input type='checkbox' name='user' value='1'/><br/>";
                }
                echo "<input type='submit' name='submit' value='" . __('save') . "'/></form><br/><a href='" . $url . "?id=" . $ms['refid'] . "'>" . __('back') . "</a><br/>";
                break;
        }
    }
} else {
    header("location: " . $url);
}