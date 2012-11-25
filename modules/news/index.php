<?php

/**
 * @package     JohnCMS
 * @link        http://johncms.com
 * @copyright   Copyright (C) 2008-2012 JohnCMS Community
 * @license     LICENSE.txt (see attached file)
 * @version     VERSION.txt (see attached file)
 * @author      http://johncms.com/about
 */

$tpl = Template::getInstance();

switch (Vars::$ACT) {
    case 'add':
        /*
        -----------------------------------------------------------------
        Добавление новости
        -----------------------------------------------------------------
        */
        if (Vars::$USER_RIGHTS >= 7) {
            $old = 20;
            if (isset($_POST['submit'])
                && isset($_POST['form_token'])
                && isset($_SESSION['form_token'])
                && $_POST['form_token'] == $_SESSION['form_token']
            ) {
                $error = array();
                $name = isset($_POST['name']) ? Validate::checkout($_POST['name']) : FALSE;
                $text = isset($_POST['text']) ? trim($_POST['text']) : FALSE;
                if (empty($name)) {
                    $error['title'] = __('error_title');
                }
                if (empty($text)) {
                    $error['text'] = __('error_text');
                }
                $flood = Functions::antiFlood();
                if ($flood) {
                    $error[] = __('error_flood') . ' ' . $flood . '&#160;' . __('seconds');
                }

                if (empty($error)) {
                    $rid = 0;
                    if (!empty($_POST['pf']) && ($_POST['pf'] != '0')) {
                        $pf = intval($_POST['pf']);
                        $rz = $_POST['rz'];
                        $pr = mysql_query("SELECT * FROM `forum` WHERE `refid` = '$pf' AND `type` = 'r'");
                        while ($pr1 = mysql_fetch_array($pr)) {
                            $arr[] = $pr1['id'];
                        }
                        foreach ($rz as $v) {
                            if (in_array($v, $arr)) {
                                mysql_query("INSERT INTO `forum` SET
                                    `refid` = '$v',
                                    `type` = 't',
                                    `time` = '" . time() . "',
                                    `user_id` = " . Vars::$USER_ID . ",
                                    `from` = '" . mysql_real_escape_string(Vars::$USER_NICKNAME) . "',
                                    `text` = '" . mysql_real_escape_string($name) . "'
                                ");
                                $rid = mysql_insert_id();
                                mysql_query("INSERT INTO `forum` SET
                                    `refid` = '$rid',
                                    `type` = 'm',
                                    `time` = '" . time() . "',
                                    `user_id` = " . Vars::$USER_ID . ",
                                    `from` = '" . mysql_real_escape_string(Vars::$USER_NICKNAME) . "',
                                    `ip` = '" . long2ip(Vars::$IP) . "',
                                    `soft` = '" . mysql_real_escape_string(Vars::$USER_AGENT) . "',
                                    `text` = '" . mysql_real_escape_string($text) . "'
                                ");
                            }
                        }
                    }
                    mysql_query("INSERT INTO `cms_news` SET
                        `time` = '" . time() . "',
                        `author` = '" . mysql_real_escape_string(Vars::$USER_NICKNAME) . "',
                        `author_id` = '" . Vars::$USER_ID . "',
                        `name` = '" . mysql_real_escape_string($name) . "',
                        `text` = '" . mysql_real_escape_string($text) . "',
                        `comments` = '$rid'
                    ") or die(mysql_error());
                    mysql_query("UPDATE `users` SET
                        `lastpost` = '" . time() . "'
                        WHERE `id` = " . Vars::$USER_ID
                    );
                    $tpl->continue = Vars::$URI;
                    $tpl->message = __('article_added');
                    $tpl->contents = $tpl->includeTpl('message', 1);
                    exit;
                } else {
                    $tpl->error = $error;
                }
            }
            $list = array();
            $fr = mysql_query("SELECT * FROM `forum` WHERE `type` = 'f'");
            $list[] = '<label class="small"><input type="radio" name="pf" value="0" checked="checked" />&#160;' . __('discuss_off') . '</label>';
            while ($fr1 = mysql_fetch_array($fr)) {
                $list[] = '<label class="small"><input type="radio" name="pf" value="' . $fr1['id'] . '"/>&#160;' . $fr1['text'] . '</label><br/><label><select name="rz[]">';
                $pr = mysql_query("SELECT * FROM `forum` WHERE `type` = 'r' AND `refid` = '" . $fr1['id'] . "'");
                while ($pr1 = mysql_fetch_array($pr)) {
                    $list[] = '<option value="' . $pr1['id'] . '">' . $pr1['text'] . '</option>';
                }
                $list[] = '</select></label><br/>';
            }
            // Выводим форму добавления новости
            $tpl->list = $list;
            $tpl->form_token = mt_rand(100, 10000);
            $_SESSION['form_token'] = $tpl->form_token;
            $tpl->contents = $tpl->includeTpl('news_add');
        } else {
            // Если доступ закрыт, выводим сообщение
            $tpl->back = Vars::$URI;
            $tpl->message = __('access_forbidden');
            $tpl->contents = $tpl->includeTpl('message', 1);
        }
        break;

    case 'edit':
        /*
        -----------------------------------------------------------------
        Редактирование новости
        -----------------------------------------------------------------
        */
        if (Vars::$USER_RIGHTS >= 7) {
            if (Vars::$ID && mysql_result(mysql_query("SELECT COUNT(*) FROM `cms_news` WHERE `id` = " . Vars::$ID), 0)) {
                if (!Vars::$ID) {
                    echo Functions::displayError(__('error_wrong_data'), '<a href="' . Vars::$URI . '">' . __('to_news') . '</a>');
                    exit;
                }
                if (isset($_POST['submit'])
                    && isset($_POST['form_token'])
                    && isset($_SESSION['form_token'])
                    && $_POST['form_token'] == $_SESSION['form_token']
                ) {
                    $error = array();
                    if (empty($_POST['name']))
                        $error[] = __('error_title');
                    if (empty($_POST['text']))
                        $error[] = __('error_text');
                    $name = Validate::checkout($_POST['name']);
                    $text = mysql_real_escape_string(trim($_POST['text']));
                    if (!$error) {
                        mysql_query("UPDATE `cms_news` SET
                        `name` = '" . mysql_real_escape_string($name) . "',
                        `text` = '$text'
                        WHERE `id` = " . Vars::$ID
                        );
                    } else {
                        echo Functions::displayError($error, '<a href="' . Vars::$URI . '?act=edit&amp;id=' . Vars::$ID . '">' . __('repeat') . '</a>');
                    }
                    $tpl->continue = Vars::$URI;
                    $tpl->message = __('article_changed');
                    $tpl->contents = $tpl->includeTpl('message', 1);
                    exit;
                } else {
                    // Выводим форму добавления новости
                    $req = mysql_query("SELECT * FROM `cms_news` WHERE `id` = " . Vars::$ID);
                    $res = mysql_fetch_assoc($req);
                    $tpl->title = Validate::checkout($res['name']);
                    $tpl->text = Validate::checkout($res['text']);
                    $tpl->form_token = mt_rand(100, 10000);
                    $_SESSION['form_token'] = $tpl->form_token;
                    $tpl->contents = $tpl->includeTpl('news_edit');
                }
            } else {
                echo Functions::displayError(__('error_wrong_data'));
            }
        } else {
            $tpl->back = Vars::$URI;
            $tpl->message = __('access_forbidden');
            $tpl->contents = $tpl->includeTpl('message', 1);
        }
        break;

    case 'clean':
        /*
        -----------------------------------------------------------------
        Чистка новостей
        -----------------------------------------------------------------
        */
        if (Vars::$USER_RIGHTS >= 7) {
            echo '<div class="phdr"><a href="' . Vars::$URI . '"><b>' . __('site_news') . '</b></a> | ' . __('clear') . '</div>';
            if (isset($_POST['submit'])) {
                $cl = isset($_POST['cl']) ? intval($_POST['cl']) : '';
                switch ($cl) {
                    case '1':
                        // Чистим новости, старше 1 недели
                        mysql_query("DELETE FROM `cms_news` WHERE `time`<='" . (time() - 604800) . "'");
                        mysql_query("OPTIMIZE TABLE `cms_news`");
                        echo '<p>' . __('clear_week_confirmation') . '</p><p><a href="' . Vars::$URI . '">' . __('to_news') . '</a></p>';
                        break;

                    case '2':
                        // Проводим полную очистку
                        mysql_query("TRUNCATE TABLE `cms_news`");
                        echo '<p>' . __('clear_all_confirmation') . '</p><p><a href="' . Vars::$URI . '">' . __('to_news') . '</a></p>';
                        break;
                    default :
                        // Чистим сообщения, старше 1 месяца
                        mysql_query("DELETE FROM `cms_news` WHERE `time`<='" . (time() - 2592000) . "'");
                        mysql_query("OPTIMIZE TABLE `cms_news`");
                        echo '<p>' . __('clear_month_confirmation') . '</p><p><a href="' . Vars::$URI . '">' . __('to_news') . '</a></p>';
                }
            } else {
                echo '<div class="menu"><form id="clean" method="post" action="' . Vars::$URI . '?act=clean">' .
                    '<p><h3>' . __('clear_param') . '</h3>' .
                    '<input type="radio" name="cl" value="0" checked="checked" />' . __('clear_month') . '<br />' .
                    '<input type="radio" name="cl" value="1" />' . __('clear_week') . '<br />' .
                    '<input type="radio" name="cl" value="2" />' . __('clear_all') . '</p>' .
                    '<p><input type="submit" name="submit" value="' . __('clear') . '" /></p>' .
                    '</form></div>' .
                    '<div class="phdr"><a href="' . Vars::$URI . '">' . __('cancel') . '</a></div>';
            }
        } else {
            $tpl->back = Vars::$URI;
            $tpl->message = __('access_forbidden');
            $tpl->contents = $tpl->includeTpl('message', 1);
        }
        break;

    case 'del':
        /*
        -----------------------------------------------------------------
        Удаление новости
        -----------------------------------------------------------------
        */
        if (Vars::$USER_RIGHTS >= 7) {
            echo '<div class="phdr"><a href="' . Vars::$URI . '"><b>' . __('site_news') . '</b></a> | ' . __('delete') . '</div>';
            if (isset($_POST['submit'])
                && isset($_POST['form_token'])
                && isset($_SESSION['form_token'])
                && $_POST['form_token'] == $_SESSION['form_token']
            ) {
                mysql_query("DELETE FROM `cms_news` WHERE `id` = " . Vars::$ID);
                $tpl->continue = Vars::$URI;
                $tpl->message = __('article_deleted');
                $tpl->contents = $tpl->includeTpl('message', 1);
            } else {
                $tpl->id = Vars::$ID;
                $tpl->form_token = mt_rand(100, 10000);
                $_SESSION['form_token'] = $tpl->form_token;
                $tpl->contents = $tpl->includeTpl('news_delete');
            }
        } else {
            $tpl->back = Vars::$URI;
            $tpl->message = __('access_forbidden');
            $tpl->contents = $tpl->includeTpl('message', 1);
        }
        break;

    default:
        /*
        -----------------------------------------------------------------
        Вывод списка новостей
        -----------------------------------------------------------------
        */
        $tpl->total = mysql_result(mysql_query("SELECT COUNT(*) FROM `cms_news`"), 0);
        if ($tpl->total) {
            $req = mysql_query("SELECT * FROM `cms_news` ORDER BY `id` DESC " . Vars::db_pagination());
            for ($i = 0; $tpl->list[$i] = mysql_fetch_assoc($req); ++$i) {
                $tpl->list[$i]['text'] = Validate::checkout($tpl->list[$i]['text'], 1, 1, 1);
            }
            unset($tpl->list[$i]);
        }
        $tpl->contents = $tpl->includeTpl('index');
}