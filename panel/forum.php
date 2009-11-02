<?php

/*
////////////////////////////////////////////////////////////////////////////////
// JohnCMS                             Content Management System              //
// Официальный сайт сайт проекта:      http://johncms.com                     //
// Дополнительный сайт поддержки:      http://gazenwagen.com                  //
////////////////////////////////////////////////////////////////////////////////
// JohnCMS core team:                                                         //
// Евгений Рябинин aka john77          john77@gazenwagen.com                  //
// Олег Касьянов aka AlkatraZ          alkatraz@gazenwagen.com                //
//                                                                            //
// Информацию о версиях смотрите в прилагаемом файле version.txt              //
////////////////////////////////////////////////////////////////////////////////
*/

define('_IN_JOHNCMS', 1);

$textl = 'Форум';
require_once ('../incfiles/core.php');
require_once ('../incfiles/head.php');

if (!$dostsadm)
{
    header('Location: main.php');
    exit;
}

switch ($act)
{
    case 'moders':
        if (isset($_POST['submit']))
        {
            if (empty($_GET['id']))
            {
                echo "Ошибка!<br/><a href='forum.php?'>В управление форумом</a><br/>";
                require_once ("../incfiles/end.php");
                exit;
            }
            if (isset($_POST['mod']))
            {
                $q = mysql_query("select * from `forum` where type='a' and refid='" . $id . "';");
                while ($q1 = mysql_fetch_array($q))
                {
                    if (!in_array($q1['from'], $_POST['mod']))
                    {
                        mysql_query("delete from `forum` where `id`='" . $q1['id'] . "';");
                    }
                }
                foreach ($_POST['mod'] as $v)
                {
                    $q2 = mysql_query("select * from `forum` where type='a' and `from`='" . $v . "' and refid='" . $id . "';");
                    $q3 = mysql_num_rows($q2);
                    if ($q3 == 0)
                    {
                        mysql_query("INSERT INTO `forum` SET
							`refid`='" . $id . "',
							`type`='a',
							`from`='" . check($v) . "';");
                    }
                }
            } else
            {
                $q = mysql_query("select * from `forum` where type='a' and refid='" . $id . "';");
                while ($q1 = mysql_fetch_array($q))
                {
                    mysql_query("delete from `forum` where `id`='" . $q1['id'] . "';");
                }
            }
            header("Location: forum.php?act=moders&id=$id");
        } else
        {
            if (!empty($_GET['id']))
            {
                $typ = mysql_query("select * from `forum` where id='" . $id . "';");
                $ms = mysql_fetch_array($typ);
                if ($ms['type'] != "f")
                {
                    echo "Ошибка!<br/><a href='forum.php?'>В управление форумом</a><br/>";
                    require_once ("../incfiles/end.php");
                    exit;
                }
                echo "Назначение модеров в подфорум $ms[text]<br/><form action='forum.php?act=moders&amp;id=" . $id . "' method='post'>";
                $q = mysql_query("select * from `users` where rights='3';");
                while ($q1 = mysql_fetch_array($q))
                {
                    $q2 = mysql_query("select * from `forum` where type='a' and `from`='" . $q1['name'] . "' and refid='" . $id . "';");
                    $q3 = mysql_num_rows($q2);
                    if ($q3 == 0)
                    {
                        echo "<input type='checkbox' name='mod[]' value='" . $q1['name'] . "'/>$q1[name]<br/>";
                    } else
                    {
                        echo "<input type='checkbox' name='mod[]' value='" . $q1['name'] . "' checked='checked'/>$q1[name]<br/>";
                    }
                }
                echo "<input type='submit' name='submit' value='Ok!'/><br/></form>";
                echo "<br/><a href='forum.php?act=moders'>Выбрать подфорум</a>";
            } else
            {
                echo "Выберите подфорум<hr/>";
                $q = mysql_query("select * from `forum` where type='f' order by realid;");
                while ($q1 = mysql_fetch_array($q))
                {
                    echo "<a href='forum.php?act=moders&amp;id=" . $q1['id'] . "'>$q1[text]</a><br/>";
                }
            }
        }
        echo "<br/><a href='forum.php?'>В управление форумом</a><br/>";

        break;

    case 'del':
        echo 'Еще не готово';
        break;

    case 'add':
        ////////////////////////////////////////////////////////////
        // Добавление категории                                   //
        ////////////////////////////////////////////////////////////
        if ($id)
        {
            // Проверяем наличие категории
            $req = mysql_query("SELECT `text` FROM `forum` WHERE `id` = '$id' AND `type` = 'f' LIMIT 1");
            if (mysql_num_rows($req))
            {
                $res = mysql_fetch_array($req);
                $cat_name = $res['text'];
            } else
            {
                header('Location: forum.php?act=cat');
                exit;
            }
        }
        if (isset($_POST['submit']))
        {
            // Принимаем данные
            $name = isset($_POST['name']) ? check($_POST['name']) : '';
            $desc = isset($_POST['desc']) ? check($_POST['desc']) : '';
            // проверяем на ошибки
            $error = array();
            if (!$name)
                $error[] = 'Вы не ввели название';
            if ($name && (mb_strlen($name) < 2 || mb_strlen($name) > 30))
                $error[] = 'Длина названия должна быть не менее 2-х и не более 30 символов';
            if ($desc && mb_strlen($desc) < 2)
                $error[] = 'Длина описания должна быть не менее 2-х символов';
            if (!$error)
            {
                // Добавляем в базу категорию
                $req = mysql_query("SELECT `realid` FROM `forum` WHERE " . ($id ? "`refid` = '$id' AND `type` = 'r'" : "`type` = 'f'") . " ORDER BY `realid` DESC LIMIT 1");
                if (mysql_num_rows($req))
                {
                    $res = mysql_fetch_assoc($req);
                    $sort = $res['realid'] + 1;
                } else
                {
                    $sort = 1;
                }
                mysql_query("INSERT INTO `forum` SET
                `refid` = '" . ($id ? $id : '') . "',
                `type` = '" . ($id ? 'r' : 'f') . "',
                `text` = '$name',
                `soft` = '$desc',
                `realid` = '$sort'");
                header('Location: forum.php?act=cat' . ($id ? '&id=' . $id : ''));
            } else
            {
                // Выводим сообщение об ошибках
                echo display_error($error);
            }
        } else
        {
            // Форма ввода
            echo '<div class="phdr"><b>Добавить ' . ($id ? 'раздел' : 'категорию') . '</b></div>';
            echo '<div class="bmenu">В категорию: ' . $cat_name . '</div>';
            echo '<form action="forum.php?act=add' . ($id ? '&amp;id=' . $id : '') . '" method="post"><div class="gmenu"><p>';
            echo '<b>Название:</b><br /><input type="text" name="name" /><br /><small>Мин. 2, макс. 30 символов</small><br />';
            echo '<b>Описание:</b><br /><textarea name="desc" cols="24" rows="4"></textarea><br /><small>Мин. 2, макс. 500 симолов<br />Описание не обязательно</small><br />';
            echo '</p><p><input type="submit" value="Добавить" name="submit" />';
            echo '</p></div></form>';
            echo '<div class="phdr"><a href="forum.php?act=cat' . ($id ? '&amp;id=' . $id : '') . '">Назад</a></div>';
        }
        break;

    case 'edit':
        ////////////////////////////////////////////////////////////
        // Редактирование выбранной категории, или раздела        //
        ////////////////////////////////////////////////////////////
        if ($id)
        {
            $req = mysql_query("SELECT * FROM `forum` WHERE `id` = '$id' LIMIT 1");
            if (mysql_num_rows($req))
            {
                $res = mysql_fetch_assoc($req);
                if ($res['type'] == 'f' || $res['type'] == 'r')
                {
                    if (isset($_POST['submit']))
                    {
                        // Принимаем данные
                        $name = isset($_POST['name']) ? check($_POST['name']) : '';
                        $desc = isset($_POST['desc']) ? check($_POST['desc']) : '';
                        // проверяем на ошибки
                        $error = array();
                        if (!$name)
                            $error[] = 'Вы не ввели название';
                        if ($name && (mb_strlen($name) < 2 || mb_strlen($name) > 30))
                            $error[] = 'Длина названия должна быть не менее 2-х и не более 30 символов';
                        if ($desc && mb_strlen($desc) < 2)
                            $error[] = 'Длина описания должна быть не менее 2-х символов';
                        if (!$error)
                        {
                            // Записываем в базу
                            mysql_query("UPDATE `forum` SET
                            `text` = '$name',
                            `soft` = '$desc'
                            WHERE `id` = '$id'");
                            header('Location: forum.php?act=cat' . ($res['type'] == 'r' ? '&id=' . $res['refid'] : ''));
                        } else
                        {
                            // Выводим сообщение об ошибках
                            echo display_error($error);
                        }
                    } else
                    {
                        // Форма ввода
                        echo '<div class="phdr"><b>Редактируем ' . ($res['type'] == 'r' ? 'раздел' : 'категорию') . '</b></div>';
                        echo '<form action="forum.php?act=edit&amp;id=' . $id . '" method="post"><div class="gmenu"><p>';
                        echo '<b>Название:</b><br /><input type="text" name="name" value="' . $res['text'] . '"/><br /><small>Мин. 2, макс. 30 символов</small><br />';
                        echo '<b>Описание:</b><br /><textarea name="desc" cols="24" rows="4">' . str_replace('<br />', "\r\n", $res['soft']) . '</textarea><br /><small>Мин. 2, макс. 500 симолов<br />Описание не обязательно</small><br />';
                        echo '</p><p><input type="submit" value="Добавить" name="submit" />';
                        echo '</p></div></form>';
                        echo '<div class="phdr"><a href="forum.php?act=cat' . ($res['type'] == 'r' ? '&amp;id=' . $id : '') . '">Назад</a></div>';
                    }
                } else
                {
                    header('Location: forum.php?act=cat');
                }
            } else
            {
                header('Location: forum.php?act=cat');
            }
        } else
        {
            header('Location: forum.php?act=cat');
        }
        break;

    case 'up':
        ////////////////////////////////////////////////////////////
        // Перемещение на одну позицию вверх                      //
        ////////////////////////////////////////////////////////////
        if ($id)
        {
            $req = mysql_query("SELECT * FROM `forum` WHERE `id` = '" . $id . "' LIMIT 1");
            if (mysql_num_rows($req))
            {
                $res1 = mysql_fetch_array($req);
                $sort = $res1['realid'];
                $req = mysql_query("SELECT * FROM `forum` WHERE `type` = '" . ($res1['type'] == 'f' ? "f" : "r") . "' AND `realid` < '" . $sort . "' ORDER BY `realid` DESC LIMIT 1");
                if (mysql_num_rows($req))
                {
                    $res = mysql_fetch_assoc($req);
                    $id2 = $res['id'];
                    $sort2 = $res['realid'];
                    mysql_query("UPDATE `forum` SET `realid` = '" . $sort2 . "' WHERE `id` = '" . $id . "'");
                    mysql_query("UPDATE `forum` SET `realid` = '" . $sort . "' WHERE `id` = '" . $id2 . "'");
                }
            }
        }
        header('Location: forum.php?act=cat' . ($res1['type'] == 'r' ? '&id=' . $res1['refid'] : ''));
        break;

    case 'down':
        ////////////////////////////////////////////////////////////
        // Перемещение на одну позицию вниз                       //
        ////////////////////////////////////////////////////////////
        if ($id)
        {
            $req = mysql_query("SELECT * FROM `forum` WHERE `id` = '" . $id . "' LIMIT 1");
            if (mysql_num_rows($req))
            {
                $res1 = mysql_fetch_assoc($req);
                $sort = $res1['realid'];
                $req = mysql_query("SELECT * FROM `forum` WHERE `type` = '" . ($res1['type'] == 'f' ? "f" : "r") . "' AND `realid` > '" . $sort . "' ORDER BY `realid` ASC LIMIT 1");
                if (mysql_num_rows($req))
                {
                    $res = mysql_fetch_array($req);
                    $id2 = $res['id'];
                    $sort2 = $res['realid'];
                    mysql_query("UPDATE `forum` SET `realid` = '" . $sort2 . "' WHERE `id` = '" . $id . "'");
                    mysql_query("UPDATE `forum` SET `realid` = '" . $sort . "' WHERE `id` = '" . $id2 . "'");
                }
            }
        }
        header('Location: forum.php?act=cat' . ($res1['type'] == 'r' ? '&id=' . $res1['refid'] : ''));
        break;

    case 'cat';
        if ($id)
        {
            ////////////////////////////////////////////////////////////
            // Управление разделами                                   //
            ////////////////////////////////////////////////////////////
            $req = mysql_query("SELECT `text` FROM `forum` WHERE `id` = '$id' AND `type` = 'f' LIMIT 1");
            $res = mysql_fetch_assoc($req);
            echo '<div class="phdr"><b>Категория:</b> ' . $res['text'] . '</div>';
            $req = mysql_query("SELECT * FROM `forum` WHERE `refid` = '$id' AND `type` = 'r' ORDER BY `realid` ASC");
            if (mysql_num_rows($req))
            {
                echo '<div class="bmenu">Список разделов</div>';
                while ($res = mysql_fetch_assoc($req))
                {
                    echo is_integer($i / 2) ? '<div class="list1">' : '<div class="list2">';
                    echo '<b>' . $res['text'] . '</b>';
                    if (!empty($res['soft']))
                        echo '<br /><span class="gray"><small>' . $res['soft'] . '</small></span><br />';
                    echo '<div class="sub"><a href="forum.php?act=up&amp;id=' . $res['id'] . '">Вверх</a> | <a href="forum.php?act=down&amp;id=' . $res['id'] . '">Вниз</a> | <a href="forum.php?act=edit&amp;id=' . $res['id'] .
                        '">Изм.</a> | <a href="forum.php?act=del&amp;id=' . $res['id'] . '">Удалить</a></div></div>';
                    ++$i;
                }
            } else
            {
                echo '<div class="menu"><p>Список разделов пуст</p></div>';
            }
        } else
        {
            ////////////////////////////////////////////////////////////
            // Управление категориями                                 //
            ////////////////////////////////////////////////////////////
            echo '<div class="phdr"><b>Список категорий</b></div>';
            $req = mysql_query("SELECT * FROM `forum` WHERE `type` = 'f' ORDER BY `realid` ASC");
            while ($res = mysql_fetch_assoc($req))
            {
                //TODO: Написать вывод описаний категорий
                echo is_integer($i / 2) ? '<div class="list1">' : '<div class="list2">';
                echo '<b>' . $res['text'] . '</b> (' . mysql_result(mysql_query("SELECT COUNT(*) FROM `forum` WHERE `type` = 'r' AND `refid` = '" . $res['id'] . "'"), 0) . ') <a href="forum.php?act=cat&amp;id=' . $res['id'] . '">&gt;&gt;</a>';
                if (!empty($res['soft']))
                    echo '<br /><span class="gray"><small>' . $res['soft'] . '</small></span><br />';
                echo '<div class="sub"><a href="forum.php?act=up&amp;id=' . $res['id'] . '">Вверх</a> | <a href="forum.php?act=down&amp;id=' . $res['id'] . '">Вниз</a> | <a href="forum.php?act=edit&amp;id=' . $res['id'] .
                    '">Изм.</a> | <a href="forum.php?act=del&amp;id=' . $res['id'] . '">Удалить</a></div></div>';
                ++$i;
            }
        }
        echo '<div class="gmenu"><form action="forum.php?act=add' . ($id ? '&amp;id=' . $id : '') . '" method="post"><input type="submit" value="Добавить" /></form></div>';
        echo '<div class="phdr">' . ($act == 'cat' && $id ? '<a href="forum.php?act=cat">К списку категорий</a>' : '<a href="forum.php">Управление Форумом</a>') . '</div>';
        break;

    default:
        ////////////////////////////////////////////////////////////
        // Панель управления форумом                              //
        ////////////////////////////////////////////////////////////
        $total_cat = mysql_result(mysql_query("SELECT COUNT(*) FROM `forum` WHERE `type` = 'f'"), 0);
        $total_sub = mysql_result(mysql_query("SELECT COUNT(*) FROM `forum` WHERE `type` = 'r'"), 0);
        $total_thm = mysql_result(mysql_query("SELECT COUNT(*) FROM `forum` WHERE `type` = 't'"), 0);
        $total_thm_del = mysql_result(mysql_query("SELECT COUNT(*) FROM `forum` WHERE `type` = 't' AND `close` = '1'"), 0);
        $total_msg = mysql_result(mysql_query("SELECT COUNT(*) FROM `forum` WHERE `type` = 'm'"), 0);
        $total_msg_del = mysql_result(mysql_query("SELECT COUNT(*) FROM `forum` WHERE `type` = 'm' AND `close` = '1'"), 0);
        $total_files = mysql_result(mysql_query("SELECT COUNT(*) FROM `cms_forum_files`"), 0);
        $total_votes = mysql_result(mysql_query("SELECT COUNT(*) FROM `forum_vote` WHERE `type` = '1'"), 0);
        echo '<div class="phdr"><b>Управление форумом</b></div>';
        echo '<div class="gmenu"><p><h3><img src="../images/rate.gif" width="16" height="16" class="left" />&nbsp;Статистика</h3><ul>';
        echo '<li>Категории:&nbsp;' . $total_cat . '</li>';
        echo '<li>Разделы:&nbsp;' . $total_sub . '</li>';
        echo '<li>Темы:&nbsp;' . $total_thm . '&nbsp;/&nbsp;<span class="red">' . $total_thm_del . '</span></li>';
        echo '<li>Посты:&nbsp;' . $total_msg . '&nbsp;/&nbsp;<span class="red">' . $total_msg_del . '</span></li>';
        echo '<li>Файлы:&nbsp;' . $total_files . '</li>';
        echo '<li>Голосования:&nbsp;' . $total_votes . '</li>';
        echo '</ul></p></div>';
        echo '<div class="menu"><p><h3><img src="../images/settings.png" width="16" height="16" class="left" />&nbsp;Управление</h3><ul>';
        echo '<li><a href="forum.php?act=cat">Структура форума</a></li>';
        echo '<li><a href="forum.php?act=them">Удаленные темы</a></li>';
        echo '<li><a href="forum.php?act=post">Удаленные посты</a></li>';
        echo '<li><a href="forum.php?act=delhid">Чистка форума</a></li>';
        echo '<li><a href="forum.php?act=moders">Модераторы</a></li>';
        echo '</ul></p></div>';
        echo '<div class="phdr"><a href="../forum/index.php">В форум</a></div>';
}

echo '<p><a href="main.php">В Админку</a><br /><a href="../forum/index.php">В Форум</a></p>';

require_once ("../incfiles/end.php");

?>