<?

/**
 * @package     JohnCMS
 * @link        http://johncms.com
 * @copyright   Copyright (C) 2008-2012 JohnCMS Community
 * @license     LICENSE.txt (see attached file)
 * @version     VERSION.txt (see attached file)
 * @author      http://johncms.com/about
 */

// Проверяем права доступа
if (Vars::$USER_RIGHTS < 7) {
    echo Functions::displayError(lng('access_forbidden'));
    exit;
}

$tpl = Template::getInstance();

switch (Vars::$ACT) {
    case 'edit':
        /*
        -----------------------------------------------------------------
        Добавляем / редактируем ссылку
        -----------------------------------------------------------------
        */
        echo '<div class="phdr"><a href="' . Vars::$URI . '"><b>' . lng('advertisement') . '</b></a> | ' . (Vars::$ID ? lng('link_edit') : lng('link_add')) . '</div>';
        if (Vars::$ID) {
            // Если ссылка редактироется, запрашиваем ее данные в базе
            $req = mysql_query("SELECT * FROM `cms_ads` WHERE `id` = " . Vars::$ID);
            if (mysql_num_rows($req)) {
                $res = mysql_fetch_assoc($req);
            } else {
                echo Functions::displayError(lng('error_wrong_data'), '<a href="' . Vars::$URI . '">' . lng('back') . '</a>');
                exit;
            }
        } else {
            $res = array(
                'link'       => 'http://',
                'show'       => 0,
                'name'       => '',
                'color'      => '',
                'count_link' => 0,
                'day'        => 7,
                'view'       => 0,
                'type'       => 0,
                'layout'     => 0,
                'bold'       => 0,
                'italic'     => 0,
                'underline'  => 0,
            );
        }
        if (isset($_POST['submit'])
            && isset($_POST['form_token'])
            && isset($_SESSION['form_token'])
            && $_POST['form_token'] == $_SESSION['form_token']
        ) {
            $link = isset($_POST['link']) ? mysql_real_escape_string(trim($_POST['link'])) : '';
            $name = isset($_POST['name']) ? mysql_real_escape_string(trim($_POST['name'])) : '';
            $bold = isset($_POST['bold']);
            $italic = isset($_POST['italic']);
            $underline = isset($_POST['underline']);
            $show = isset($_POST['show']);
            $view = isset($_POST['view']) ? abs(intval($_POST['view'])) : 0;
            $day = isset($_POST['day']) ? abs(intval($_POST['day'])) : 0;
            $count = isset($_POST['count']) ? abs(intval($_POST['count'])) : 0;
            $day = isset($_POST['day']) ? abs(intval($_POST['day'])) : 0;
            $layout = isset($_POST['layout']) ? abs(intval($_POST['layout'])) : 0;
            $type = isset($_POST['type']) ? intval($_POST['type']) : 0;
            $mesto = isset($_POST['mesto']) ? abs(intval($_POST['mesto'])) : 0;
            $color = isset($_POST['color']) ? mb_substr(trim($_POST['color']), 0, 6) : '';
            $error = array();
            if (!$link || !$name)
                $error[] = lng('error_empty_fields');
            if ($type > 3 || $type < 0)
                $type = 0;
            if (!$mesto) {
                $total = mysql_result(mysql_query("SELECT COUNT(*) FROM `cms_ads` WHERE `mesto` = '" . $mesto . "' AND `type` = '" . $type . "'"), 0);
                if ($total != 0)
                    $error[] = lng('links_place_occupied');
            }
            if ($color) {
                if (preg_match("/[^\da-fA-F_]+/", $color))
                    $error[] = lng('error_wrong_symbols');
                if (strlen($color) < 6)
                    $error[] = lng('error_color');
            }
            if ($error) {
                echo Functions::displayError($error, '<a href="' . Vars::$URI . '?from=addlink">' . lng('back') . '</a>');
                exit;
            }
            if (Vars::$ID) {
                // Обновляем ссылку после редактирования
                mysql_query("UPDATE `cms_ads` SET
                    `type` = '$type',
                    `view` = '$view',
                    `link` = '$link',
                    `name` = '$name',
                    `color` = '$color',
                    `count_link` = '$count',
                    `day` = '$day',
                    `layout` = '$layout',
                    `bold` = '$bold',
                    `show` = '$show',
                    `italic` = '$italic',
                    `underline` = '$underline'
                    WHERE `id` = " . Vars::$ID);
            } else {
                // Добавляем новую ссылку
                $req = mysql_query("SELECT `mesto` FROM `cms_ads` ORDER BY `mesto` DESC LIMIT 1");
                if (mysql_num_rows($req) > 0) {
                    $res = mysql_fetch_array($req);
                    $mesto = $res['mesto'] + 1;
                } else {
                    $mesto = 1;
                }
                mysql_query("INSERT INTO `cms_ads` SET
                    `type` = '$type',
                    `view` = '$view',
                    `mesto` = '$mesto',
                    `link` = '$link',
                    `name` = '$name',
                    `color` = '$color',
                    `count_link` = '$count',
                    `day` = '$day',
                    `layout` = '$layout',
                    `to` = '0',
                    `show` = '$show',
                    `time` = '" . time() . "',
                    `bold` = '$bold',
                    `italic` = '$italic',
                    `underline` = '$underline'
                ") or die (mysql_error());
            }
            mysql_query("UPDATE `users` SET `lastpost` = '" . time() . "' WHERE `id` = " . Vars::$USER_ID);
            echo'<div class="menu"><p>' . (Vars::$ID ? lng('link_edit_ok') : lng('link_add_ok')) . '<br />' .
                '<a href="' . Vars::$URI . '?sort=' . $type . '">' . lng('continue') . '</a></p></div>';
        } else {
            $tpl->res = $res;
            $tpl->form_token = mt_rand(100, 10000);
            $_SESSION['form_token'] = $tpl->form_token;
            $tpl->contents = $tpl->includeTpl('links_edit');
        }
        break;

    case 'down':
        /*
        -----------------------------------------------------------------
        Перемещаем на позицию вниз
        -----------------------------------------------------------------
        */
        if (Vars::$ID) {
            $req = mysql_query("SELECT `mesto`, `type` FROM `cms_ads` WHERE `id` = " . Vars::$ID);
            if (mysql_num_rows($req) > 0) {
                $res = mysql_fetch_array($req);
                $mesto = $res['mesto'];
                $req = mysql_query("SELECT * FROM `cms_ads` WHERE `mesto` > '$mesto' AND `type` = '" . $res['type'] . "' ORDER BY `mesto` ASC");
                if (mysql_num_rows($req) > 0) {
                    $res = mysql_fetch_array($req);
                    $id2 = $res['id'];
                    $mesto2 = $res['mesto'];
                    mysql_query("UPDATE `cms_ads` SET `mesto` = '$mesto2' WHERE `id` = " . Vars::$ID);
                    mysql_query("UPDATE `cms_ads` SET `mesto` = '$mesto' WHERE `id` = '$id2'");
                }
            }
        }
        header('Location: ' . getenv("HTTP_REFERER"));
        break;

    case 'up':
        /*
        -----------------------------------------------------------------
        Перемещаем на позицию вверх
        -----------------------------------------------------------------
        */
        if (Vars::$ID) {
            $req = mysql_query("SELECT `mesto`, `type` FROM `cms_ads` WHERE `id` = " . Vars::$ID);
            if (mysql_num_rows($req) > 0) {
                $res = mysql_fetch_array($req);
                $mesto = $res['mesto'];
                $req = mysql_query("SELECT * FROM `cms_ads` WHERE `mesto` < '$mesto' AND `type` = '" . $res['type'] . "' ORDER BY `mesto` DESC");
                if (mysql_num_rows($req) > 0) {
                    $res = mysql_fetch_array($req);
                    $id2 = $res['id'];
                    $mesto2 = $res['mesto'];
                    mysql_query("UPDATE `cms_ads` SET `mesto` = '$mesto2' WHERE `id` = " . Vars::$ID);
                    mysql_query("UPDATE `cms_ads` SET `mesto` = '$mesto' WHERE `id` = '$id2'");
                }
            }
        }
        header('Location: ' . getenv("HTTP_REFERER") . '');
        break;

    case 'del':
        /*
        -----------------------------------------------------------------
        Удаляем ссылку
        -----------------------------------------------------------------
        */
        if (Vars::$ID) {
            if (isset($_POST['submit'])) {
                mysql_query("DELETE FROM `cms_ads` WHERE `id` = " . Vars::$ID);
                header('Location: ' . $_POST['ref']);
            } else {
                echo '<div class="phdr"><a href="' . Vars::$URI . '"><b>' . lng('advertisement') . '</b></a> | ' . lng('delete') . '</div>' .
                    '<div class="rmenu"><form action="' . Vars::$URI . '?act=del&amp;id=' . Vars::$ID . '" method="post">' .
                    '<p>' . lng('link_deletion_warning') . '</p>' .
                    '<p><input type="submit" name="submit" value="' . lng('delete') . '" /></p>' .
                    '<input type="hidden" name="ref" value="' . htmlspecialchars($_SERVER['HTTP_REFERER']) . '" />' .
                    '</form></div>' .
                    '<div class="phdr"><a href="' . htmlspecialchars($_SERVER['HTTP_REFERER']) . '">' . lng('cancel') . '</a></div>';
            }
        }
        break;

    case 'clear':
        /*
        -----------------------------------------------------------------
        Очистка базы от неактивных ссылок
        -----------------------------------------------------------------
        */
        if (isset($_POST['submit'])) {
            mysql_query("DELETE FROM `cms_ads` WHERE `to` = '1'");
            mysql_query("OPTIMIZE TABLE `cms_ads`");
            header('location: ' . Vars::$URI);
        } else {
            echo '<div class="phdr"><a href="' . Vars::$URI . '"><b>' . lng('advertisement') . '</b></a> | ' . lng('links_delete_hidden') . '</div>' .
                '<div class="menu"><form method="post" action="' . Vars::$URI . '?act=clear">' .
                '<p>' . lng('link_clear_warning') . '</p>' .
                '<p><input type="submit" name="submit" value="' . lng('delete') . '" />' .
                '</p></form></div>' .
                '<div class="phdr"><a href="' . Vars::$URI . '">' . lng('cancel') . '</a></div>';
        }
        break;

    case 'show':
        /*
        -----------------------------------------------------------------
        Восстанавливаем / скрываем ссылку
        -----------------------------------------------------------------
        */
        if (Vars::$ID) {
            $req = mysql_query("SELECT * FROM `cms_ads` WHERE `id` = " . Vars::$ID);
            if (mysql_num_rows($req)) {
                $res = mysql_fetch_assoc($req);
                mysql_query("UPDATE `cms_ads` SET `to`='" . ($res['to'] ? 0 : 1) . "' WHERE `id` = " . Vars::$ID);
            }
        }
        header('Location: ' . $_SERVER['HTTP_REFERER']);
        break;

    default:
        /*
        -----------------------------------------------------------------
        Главное меню модуля управления рекламой
        -----------------------------------------------------------------
        */
        echo '<div class="phdr"><a href="' . Vars::$MODULE_URI . '"><b>' . lng('admin_panel') . '</b></a> | ' . lng('advertisement') . '</div>';
        $array_placing = array(
            lng('link_add_placing_all'),
            lng('link_add_placing_front'),
            lng('link_add_placing_child')
        );
        $array_show = array(
            lng('to_all'),
            lng('to_guest'),
            lng('to_users')
        );
        $type = isset($_GET['type']) ? intval($_GET['type']) : 0;
        $array_menu = array(
            (!$type ? lng('endwise') : '<a href="' . Vars::$URI . '">' . lng('endwise') . '</a>'),
            ($type == 1 ? lng('above_content') : '<a href="' . Vars::$URI . '?type=1">' . lng('above_content') . '</a>'),
            ($type == 2 ? lng('below_content') : '<a href="' . Vars::$URI . '?type=2">' . lng('below_content') . '</a>'),
            ($type == 3 ? lng('below') : '<a href="' . Vars::$URI . '?type=3">' . lng('below') . '</a>')
        );
        echo '<div class="topmenu">' . Functions::displayMenu($array_menu) . '</div>';
        $total = mysql_result(mysql_query("SELECT COUNT(*) FROM `cms_ads` WHERE `type` = '$type'"), 0);
        if ($total) {
            $req = mysql_query("SELECT * FROM `cms_ads` WHERE `type` = '$type' ORDER BY `mesto` ASC " . Vars::db_pagination());
            $i = 0;
            while ($res = mysql_fetch_assoc($req)) {
                echo $i % 2 ? '<div class="list2">' : '<div class="list1">';
                $name = str_replace('|', '; ', $res['name']);
                $name = htmlentities($name, ENT_QUOTES, 'UTF-8');
                // Если был задан цвет, то применяем
                if (!empty($res['color']))
                    $name = '<span style="color:#' . $res['color'] . '">' . $name . '</span>';
                // Если было задано начертание шрифта, то применяем
                $font = $res['bold'] ? 'font-weight: bold;' : FALSE;
                $font .= $res['italic'] ? ' font-style:italic;' : FALSE;
                $font .= $res['underline'] ? ' text-decoration:underline;' : FALSE;
                if ($font)
                    $name = '<span style="' . $font . '">' . $name . '</span>';
                ////////////////////////////////////////////////////////////
                // Выводим рекламмную ссылку с атрибутами                 //
                ////////////////////////////////////////////////////////////
                echo '<p>' . Functions::getImage(($res['to'] ? 'red' : 'green') . '.png', '', 'class="left"') . '&#160;' .
                    '<a href="' . htmlspecialchars($res['link']) . '">' . htmlspecialchars($res['link']) . '</a>&nbsp;[' . $res['count'] . ']<br />' . $name . '</p>';
                $menu = array(
                    '<a href="' . Vars::$URI . '?act=up&amp;id=' . $res['id'] . '">' . lng('up') . '</a>',
                    '<a href="' . Vars::$URI . '?act=down&amp;id=' . $res['id'] . '">' . lng('down') . '</a>',
                    '<a href="' . Vars::$URI . '?act=edit&amp;id=' . $res['id'] . '">' . lng('edit') . '</a>',
                    '<a href="' . Vars::$URI . '?act=del&amp;id=' . $res['id'] . '">' . lng('delete') . '</a>',
                    '<a href="' . Vars::$URI . '?act=show&amp;id=' . $res['id'] . '">' . ($res['to'] ? lng('to_show') : lng('hide')) . '</a>'
                );
                echo '<div class="sub">' .
                    '<div>' . Functions::displayMenu($menu) . '</div>' .
                    '<p><span class="gray">' . lng('installation_date') . ':</span> ' . Functions::displayDate($res['time']) . '<br />' .
                    '<span class="gray">' . lng('placing') . ':</span>&nbsp;' . $array_placing[$res['layout']] . '<br />' .
                    '<span class="gray">' . lng('to_show') . ':</span>&nbsp;' . $array_show[$res['view']];
                // Вычисляем условия договора на рекламу
                $agreement = array();
                $remains = array();
                if (!empty($res['count_link'])) {
                    $agreement[] = $res['count_link'] . ' ' . lng('transitions_n');
                    $remains_count = $res['count_link'] - $res['count'];
                    if ($remains_count > 0)
                        $remains[] = $remains_count . ' ' . lng('transitions_n');
                }
                if (!empty($res['day'])) {
                    $agreement[] = Functions::timeCount($res['day'] * 86400);
                    $remains_count = $res['day'] * 86400 - (time() - $res['time']);
                    if ($remains_count > 0)
                        $remains[] = Functions::timeCount($remains_count);
                }
                // Если был договор, то выводим описание
                if ($agreement) {
                    echo '<br /><span class="gray">' . lng('agreement') . ':</span>&nbsp;' . implode($agreement, ', ');
                    if ($remains)
                        echo '<br /><span class="gray">' . lng('remains') . ':</span> ' . implode($remains, ', ');
                }
                echo ($res['show'] ? '<br /><span class="red"><b>' . lng('link_direct') . '</b></span>' : '') . '</p></div></div>';
                ++$i;
            }
        } else {
            echo '<div class="menu"><p>' . lng('list_empty') . '</p></div>';
        }
        echo '<div class="phdr">' . lng('total') . ': ' . $total . '</div>';
        if ($total > Vars::$USER_SET['page_size']) {
            echo '<div class="topmenu">' . Functions::displayPagination(Vars::$URI . '?type=' . $type . '&amp;', Vars::$START, $total, Vars::$USER_SET['page_size']) . '</div>' .
                '<p><form action="' . Vars::$URI . '?type=' . $type . '" method="post">' .
                '<input type="text" name="page" size="2"/>' .
                '<input type="submit" value="' . lng('to_page') . ' &gt;&gt;"/></form></p>';
        }
        echo'<p><a href="' . Vars::$URI . '?act=edit">' . lng('link_add') . '</a><br />' .
            '<a href="' . Vars::$URI . '?act=clear">' . lng('links_delete_hidden') . '</a><br />' .
            '<a href="' . Vars::$MODULE_URI . '">' . lng('admin_panel') . '</a></p>';
}