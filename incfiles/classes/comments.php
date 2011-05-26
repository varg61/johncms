<?php

/**
 * @package     JohnCMS
 * @link        http://johncms.com
 * @copyright   Copyright (C) 2008-2011 JohnCMS Community
 * @license     LICENSE.txt (see attached file)
 * @version     VERSION.txt (see attached file)
 * @author      http://johncms.com/about
 */

defined('_IN_JOHNCMS') or die('Restricted access');

class comments
{
    // Служебные данные
    private $object_table;                                // Таблица комментируемых объектов
    private $comments_table;                              // Таблица с комментариями
    private $sub_id = false;                              // Идентификатор комментируемого объекта
    private $item;                                        // Локальный идентификатор
    private $user_id = false;                             // Идентификатор авторизованного пользователя
    private $rights = 0;                                  // Права доступа
    private $ban = false;                                 // Находится ли юзер в бане?
    private $url;                                         // URL формируемых ссылок

    // Права доступа
    private $access_reply = false;                        // Возможность отвечать на комментарий
    private $access_edit = false;                         // Возможность редактировать комментарий
    private $access_delete = false;                       // Возможность удалять комментарий
    private $access_level = 6;                            // Уровень доступа для Администрации

    // Параметры отображения комментариев
    public $min_lenght = 4;                               // Мин. к-во символов в комментарии
    public $max_lenght = 5000;                            // Макс. к-во символов в комментарии
    public $captcha = false;                              // Показывать CAPTCHA

    // Возвращаемые значения
    public $total = 0;                                    // Общее число комментариев объекта

    /*
    -----------------------------------------------------------------
    Конструктор класса
    -----------------------------------------------------------------
    */
    function __construct($arg = array())
    {
        global $mod, $realtime, $start, $kmess;
        $this->comments_table = $arg['comments_table'];
        $this->object_table = !empty($arg['object_table']) ? $arg['object_table'] : false;
        if (!empty($arg['sub_id_name']) && !empty($arg['sub_id'])) {
            $this->sub_id = $arg['sub_id'];
            $this->url = $arg['script'] . '&amp;' . $arg['sub_id_name'] . '=' . $arg['sub_id'];
        } else {
            //TODO: Доработать на режим без sub_id
            $this->url = $arg['script'];
        }
        $this->item = isset($_GET['item']) ? abs(intval($_GET['item'])) : false;
        // Получаем данные пользователя
        if (core::$user_id) {
            $this->user_id = core::$user_id;
            $this->rights = core::$user_rights;
            $this->ban = core::$user_ban;
        }
        // Назначение пользовательских прав
        if (core::$user_id && isset($arg['owner']) && $arg['owner'] == core::$user_id && !$this->ban) {
            $this->access_delete = isset($arg['owner_delete']) ? $arg['owner_delete'] : false;
            $this->access_reply = isset($arg['owner_reply']) ? $arg['owner_reply'] : false;
            $this->access_edit = isset($arg['owner_edit']) ? $arg['owner_edit'] : false;
        }
        // Открываем доступ для Администрации
        if ($this->rights >= $this->access_level) {
            $this->access_reply = true;
            $this->access_edit = true;
            $this->access_delete = true;
        }

        switch ($mod) {
            case 'reply':
                /*
                -----------------------------------------------------------------
                Отвечаем на комментарий
                -----------------------------------------------------------------
                */
                if ($this->item && $this->access_reply && !$this->ban) {
                    echo '<div class="phdr"><a href="' . $this->url . '"><b>' . $arg['title'] . '</b></a> | ' . core::$lng['reply'] . '</div>';
                    $req = mysql_query("SELECT * FROM `" . $this->comments_table . "` WHERE `id` = '" . $this->item . "' AND `sub_id` = '" . $this->sub_id . "' LIMIT 1");
                    if (mysql_num_rows($req)) {
                        $res = mysql_fetch_assoc($req);
                        $attributes = unserialize($res['attributes']);
                        if (!empty($res['reply']) && $attributes['reply_rights'] > $this->rights) {
                            echo functions::display_error(core::$lng['error_reply_rights'], '<a href="' . $this->url . '">' . core::$lng['back'] . '</a>');
                        } elseif (isset($_POST['submit'])) {
                            $message = $this->msg_check();
                            if (empty($message['error'])) {
                                $attributes['reply_id'] = $this->user_id;
                                $attributes['reply_rights'] = $this->rights;
                                $attributes['reply_name'] = core::$user_data['name'];
                                $attributes['reply_time'] = $realtime;
                                mysql_query("UPDATE `" . $this->comments_table . "` SET
                                    `reply` = '" . mysql_real_escape_string($message['text']) . "',
                                    `attributes` = '" . mysql_real_escape_string(serialize($attributes)) . "'
                                    WHERE `id` = '" . $this->item . "'
                                ");
                                header('Location: ' . str_replace('&amp;', '&', $this->url));
                            } else {
                                echo functions::display_error($message['error'], '<a href="' . $this->url . '&amp;mod=reply&amp;item=' . $this->item . '">' . core::$lng['back'] . '</a>');
                            }
                        } else {
                            $text = '<a href="' . core::$system_set['homeurl'] . '/users/profile.php?user=' . $res['user_id'] . '"><b>' . $attributes['author_name'] . '</b></a>' .
                                    ' (' . date("d.m.Y / H:i:s", $res['time'] + core::$user_set['sdvig'] * 3600) . ')<br />' .
                                    functions::checkout($res['text']);
                            $reply = functions::checkout($res['reply']);
                            echo $this->msg_form('&amp;mod=reply&amp;item=' . $this->item, $text, $reply) .
                                 '<div class="phdr"><a href="' . $this->url . '">' . core::$lng['back'] . '</a></div>';
                        }
                    } else {
                        echo functions::display_error(core::$lng['error_wrong_data'], '<a href="' . $this->url . '">' . core::$lng['back'] . '</a>');
                    }
                }
                break;

            case 'edit':
                /*
                -----------------------------------------------------------------
                Редактируем комментарий
                -----------------------------------------------------------------
                */
                if ($this->item && $this->access_edit && !$this->ban) {
                    echo '<div class="phdr"><a href="' . $this->url . '"><b>' . $arg['title'] . '</b></a> | ' . core::$lng['edit'] . '</div>';
                    $req = mysql_query("SELECT * FROM `" . $this->comments_table . "` WHERE `id` = '" . $this->item . "' AND `sub_id` = '" . $this->sub_id . "' LIMIT 1");
                    if (mysql_num_rows($req)) {
                        $res = mysql_fetch_assoc($req);
                        $attributes = unserialize($res['attributes']);
                        $user = functions::get_user($res['user_id']);
                        if ($user['rights'] > core::$user_rights) {
                            echo functions::display_error(core::$lng['error_edit_rights'], '<a href="' . $this->url . '">' . core::$lng['back'] . '</a>');
                        } elseif (isset($_POST['submit'])) {
                            $message = $this->msg_check();
                            if (empty($message['error'])) {
                                $attributes['edit_id'] = $this->user_id;
                                $attributes['edit_name'] = core::$user_data['name'];
                                $attributes['edit_time'] = $realtime;
                                if (isset($attributes['edit_count']))
                                    ++$attributes['edit_count'];
                                else
                                    $attributes['edit_count'] = 1;
                                mysql_query("UPDATE `" . $this->comments_table . "` SET
                                    `text` = '" . mysql_real_escape_string($message['text']) . "',
                                    `attributes` = '" . mysql_real_escape_string(serialize($attributes)) . "'
                                    WHERE `id` = '" . $this->item . "'
                                ");
                                header('Location: ' . str_replace('&amp;', '&', $this->url));
                            } else {
                                echo functions::display_error($message['error'], '<a href="' . $this->url . '&amp;mod=edit&amp;item=' . $this->item . '">' . core::$lng['back'] . '</a>');
                            }
                        } else {
                            $author = '<a href="' . core::$system_set['homeurl'] . '/users/profile.php?user=' . $res['user_id'] . '"><b>' . $attributes['author_name'] . '</b></a>';
                            $author .= ' (' . date("d.m.Y / H:i:s", $res['time'] + core::$user_set['sdvig'] * 3600) . ')<br />';
                            $text = functions::checkout($res['text']);
                            echo $this->msg_form('&amp;mod=edit&amp;item=' . $this->item, $author, $text);
                        }
                    } else {
                        echo functions::display_error(core::$lng['error_wrong_data'], '<a href="' . $this->url . '">' . core::$lng['back'] . '</a>');
                    }
                    echo '<div class="phdr"><a href="' . $this->url . '">' . core::$lng['back'] . '</a></div>';
                }
                break;

            case 'del':
                /*
                -----------------------------------------------------------------
                Удаляем комментарий
                -----------------------------------------------------------------
                */
                if ($this->item && $this->access_delete && !$this->ban) {
                    if (isset($_GET['yes'])) {
                        //TODO: Продумать проверку на удаление постов администрации
                        $req = mysql_query("SELECT * FROM `" . $this->comments_table . "` WHERE `id` = '" . $this->item . "' AND `sub_id` = '" . $this->sub_id . "' LIMIT 1");
                        if (mysql_num_rows($req)) {
                            $res = mysql_fetch_assoc($req);
                            if (isset($_GET['all'])) {
                                // Удаляем все комментарии выбранного пользователя
                                $count = mysql_result(mysql_query("SELECT COUNT(*) FROM `" . $this->comments_table . "` WHERE `sub_id` = '" . $this->sub_id . "' AND `user_id` = '" . $res['user_id'] . "'"), 0);
                                mysql_query("DELETE FROM `" . $this->comments_table . "` WHERE `sub_id` = '" . $this->sub_id . "' AND `user_id` = '" . $res['user_id'] . "'");
                            } else {
                                // Удаляем отдельный комментарий
                                $count = 1;
                                mysql_query("DELETE FROM `" . $this->comments_table . "` WHERE `id` = '" . $this->item . "'");
                            }
                            // Вычитаем баллы из статистики пользователя
                            $req_u = mysql_query("SELECT * FROM `users` WHERE `id` = '" . $res['user_id'] . "'");
                            if (mysql_num_rows($req_u)) {
                                $res_u = mysql_fetch_assoc($req_u);
                                $count = $res_u['komm'] > $count ? $res_u['komm'] - $count : 0;
                                mysql_query("UPDATE `users` SET `komm` = '$count' WHERE `id` = '" . $res['user_id'] . "'");
                            }
                            // Обновляем счетчик комментариев
                            $this->msg_total(1);
                        }
                        header('Location: ' . str_replace('&amp;', '&', $this->url));
                    } else {
                        echo '<div class="phdr"><a href="' . $this->url . '"><b>' . $arg['title'] . '</b></a> | ' . core::$lng['delete'] . '</div>' .
                             '<div class="rmenu"><p>' . core::$lng['delete_confirmation'] . '<br />' .
                             '<a href="' . $this->url . '&amp;mod=del&amp;item=' . $this->item . '&amp;yes">' . core::$lng['delete'] . '</a> | ' .
                             '<a href="' . $this->url . '">' . core::$lng['cancel'] . '</a><br />' .
                             '<div class="sub">' . core::$lng['clear_user_msg'] . '<br />' .
                             '<span class="red"><a href="' . $this->url . '&amp;mod=del&amp;item=' . $this->item . '&amp;yes&amp;all">' . core::$lng['clear'] . '</a></span>' .
                             '</div></p></div>' .
                             '<div class="phdr"><a href="' . $this->url . '">' . core::$lng['back'] . '</a></div>';
                    }
                }
                break;

            case 'add_comment':
                /*
                -----------------------------------------------------------------
                Добавляем комментарий
                -----------------------------------------------------------------
                */
                if (!$this->ban) {
                    $message = $this->msg_check(1);
                    if (empty($message['error'])) {
                        // Формируем атрибуты сообщения
                        $attributes = array(
                            'author_name' => core::$user_data['name'],
                            'author_ip' => core::$ip,
                            'author_ip_via_proxy' => core::$ip_via_proxy,
                            'author_browser' => core::$user_agent
                        );

                        // Записываем комментарий в базу
                        mysql_query("INSERT INTO `" . $this->comments_table . "` SET
                            `sub_id` = '" . intval($this->sub_id) . "',
                            `user_id` = '" . $this->user_id . "',
                            `text` = '" . mysql_real_escape_string($message['text']) . "',
                            `time` = '$realtime',
                            `attributes` = '" . mysql_real_escape_string(serialize($attributes)) . "'
                        ");
                        // Обновляем счетчик комментариев
                        $this->msg_total(1);
                        // Обновляем статистику пользователя
                        mysql_query("UPDATE `users` SET `komm` = '" . (core::$user_data['komm'] + 1) . "', `lastpost` = '$realtime' WHERE `id` = '" . $this->user_id . "'");
                        header('Location: ' . str_replace('&amp;', '&', $this->url));
                    } else {
                        echo functions::display_error($message['error'], '<a href="' . $this->url . '">' . core::$lng['back'] . '</a>');
                    }
                }
                break;

            default:
                /*
                -----------------------------------------------------------------
                Показываем список комментариев
                -----------------------------------------------------------------
                */
                if (!empty($arg['context_top']))
                    echo $arg['context_top'];
                echo '<div class="phdr"><b>' . $arg['title'] . '</b></div>';
                if (!$this->ban) {
                    // Показываем форму ввода
                    echo $this->msg_form('&amp;mod=add_comment');
                }
                $this->total = $this->msg_total();
                if ($this->total) {
                    $req = mysql_query("SELECT `" . $this->comments_table . "`.*, `" . $this->comments_table . "`.`id` AS `subid`, `users`.`rights`, `users`.`lastdate`, `users`.`sex`, `users`.`status`, `users`.`datereg`, `users`.`id`
                    FROM `" . $this->comments_table . "` LEFT JOIN `users` ON `" . $this->comments_table . "`.`user_id` = `users`.`id`
                    WHERE `sub_id` = '" . $this->sub_id . "' ORDER BY `subid` DESC LIMIT $start, $kmess");
                    $i = 0;
                    while (($res = mysql_fetch_assoc($req)) !== false) {
                        $attributes = unserialize($res['attributes']);
                        $res['name'] = $attributes['author_name'];
                        $res['ip'] = $attributes['author_ip'];
                        $res['ip_via_proxy'] = isset($attributes['author_ip_via_proxy']) ? $attributes['author_ip_via_proxy'] : 0;
                        $res['browser'] = $attributes['author_browser'];
                        echo $i % 2 ? '<div class="list2">' : '<div class="list1">';
                        $menu = array(
                            $this->access_reply ? '<a href="' . $this->url . '&amp;mod=reply&amp;item=' . $res['subid'] . '">' . core::$lng['reply'] . '</a>' : '',
                            $this->access_edit ? '<a href="' . $this->url . '&amp;mod=edit&amp;item=' . $res['subid'] . '">' . core::$lng['edit'] . '</a>' : '',
                            $this->access_delete ? '<a href="' . $this->url . '&amp;mod=del&amp;item=' . $res['subid'] . '">' . core::$lng['delete'] . '</a>' : ''
                        );
                        $text = functions::checkout($res['text'], 1, 1);
                        if (core::$user_set['smileys'])
                            $text = functions::smileys($text, $res['rights'] >= 1 ? 1 : 0);
                        if (isset($attributes['edit_count'])) {
                            $text .= '<br /><span class="gray"><small>' . core::$lng['edited'] . ': <b>' . $attributes['edit_name'] . '</b>' .
                                     ' (' . date("d.m.Y / H:i", $attributes['edit_time'] + core::$user_set['sdvig'] * 3600) . ') <b>' .
                                     '[' . $attributes['edit_count'] . ']</b></small></span>';
                        }
                        if (!empty($res['reply'])) {
                            $reply = functions::checkout($res['reply'], 1, 1);
                            if (core::$user_set['smileys'])
                                $reply = functions::smileys($reply, $attributes['reply_rights'] >= 1 ? 1 : 0);
                            $text .= '<div class="' . ($attributes['reply_rights'] ? '' : 'g') . 'reply"><small>' .
                                     '<a href="' . core::$system_set['homeurl'] . '/users/profile.php?user=' . $attributes['reply_id'] . '"><b>' . $attributes['reply_name'] . '</b></a>' .
                                     ' (' . date("d.m.Y / H:i:s", $attributes['reply_time'] + core::$user_set['sdvig'] * 3600) . ')</small><br/>' . $reply . '</div>';
                        }
                        $user_arg = array(
                            'header' => ' <span class="gray">(' . date("d.m.Y / H:i:s", $res['time'] + core::$user_set['sdvig'] * 3600) . ')</span>',
                            'body' => $text,
                            'sub' => functions::display_menu($menu),
                            'iphide' => (core::$user_rights ? false : true)
                        );
                        echo functions::display_user($res, $user_arg);
                        echo '</div>';
                        ++$i;
                    }
                } else {
                    echo '<div class="menu"><p>' . core::$lng['list_empty'] . '</p></div>';
                }
                echo '<div class="phdr">' . core::$lng['total'] . ': ' . $this->total . '</div>';
                if ($this->total > $kmess) {
                    echo functions::display_pagination($this->url . '&amp;', $start, $this->total, $kmess);
                    echo '<p><form action="' . $this->url . '" method="post">' .
                         '<input type="text" name="page" size="2"/>' .
                         '<input type="submit" value="' . core::$lng['to_page'] . ' &gt;&gt;"/>' .
                         '</form></p>';
                }
                if (!empty($arg['context_bottom']))
                    echo $arg['context_bottom'];
        }
    }

    /*
    -----------------------------------------------------------------
    Форма ввода комментария
    -----------------------------------------------------------------
    */
    private function msg_form($submit_link = '', $text = '', $reply = '')
    {
        $out = '<div class="gmenu"><form name="form" action="' . $this->url . $submit_link . '" method="post"><p>';
        if (!empty($text)) {
            $out .= '<div class="quote">' . $text . '</div></p><p>';
        }
        $out .= '<b>' . core::$lng['message'] . '</b>: <small>(Max. ' . $this->max_lenght . ')</small><br />';
        if (!core::$is_mobile)
            $out .= '</p><p>' . bbcode::auto_bb('form', 'message');
        $out .= '<textarea rows="' . core::$user_set['field_h'] . '" name="message">' . $reply . '</textarea><br/>';
        if (core::$user_set['translit'])
            $out .= '<input type="checkbox" name="translit" value="1" />&nbsp;' . core::$lng['translit'] . '<br/>';
        $out .= '<input type="submit" name="submit" value="' . core::$lng['sent'] . '"/></p></form></div>';
        return $out;
    }

    /*
    -----------------------------------------------------------------
    Проверка текста сообщения
    -----------------------------------------------------------------
    $rpt_check (boolean)    проверка на повтор сообщений
    -----------------------------------------------------------------
    */
    private function msg_check($rpt_check = false)
    {
        $error = array();
        $message = isset($_POST['message']) ? mb_substr(trim($_POST['message']), 0, $this->max_lenght) : false;
        $translit = isset($_POST['translit']);
        // Проверяем на минимально допустимую длину
        if (mb_strlen($message) < $this->min_lenght) {
            $error[] = core::$lng['error_message_short'];
        } else {
            // Проверка на флуд
            $flood = functions::antiflood();
            if ($flood)
                $error[] = core::$lng['error_flood'] . ' ' . $flood . '&#160;' . core::$lng['seconds'];
        }
        // Проверка на повтор сообщений
        if (!$error && $rpt_check) {
            $req = mysql_query("SELECT * FROM `" . $this->comments_table . "` WHERE `user_id` = '" . $this->user_id . "' ORDER BY `id` DESC LIMIT 1");
            $res = mysql_fetch_assoc($req);
            if (mb_strtolower($message) == mb_strtolower($res['text']))
                $error[] = core::$lng['error_message_exists'];
        }
        // Транслит сообщения
        if (!$error && $translit)
            $message = functions::trans($message);
        // Возвращаем результат
        return array(
            'text' => $message,
            'error' => $error
        );
    }

    /*
    -----------------------------------------------------------------
    Счетчик комментариев
    -----------------------------------------------------------------
    */
    private function msg_total($update = false)
    {
        $total = mysql_result(mysql_query("SELECT COUNT(*) FROM `" . $this->comments_table . "` WHERE `sub_id` = '" . $this->sub_id . "'"), 0);
        if ($update) {
            // Обновляем счетчики в таблице объекта
            mysql_query("UPDATE `" . $this->object_table . "` SET `comm_count` = '$total' WHERE `id` = '" . $this->sub_id . "'");
        }
        return $total;
    }
}

?>