<?php

/**
 * @package     JohnCMS
 * @link        http://johncms.com
 * @copyright   Copyright (C) 2008-2012 JohnCMS Community
 * @license     LICENSE.txt (see attached file)
 * @version     VERSION.txt (see attached file)
 * @author      http://johncms.com/about
 */

class Comments
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
    public $added = false;                                // Метка добавления нового комментария

    /*
    -----------------------------------------------------------------
    Конструктор класса
    -----------------------------------------------------------------
    */
    function __construct($arg = array())
    {
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
        if (Vars::$USER_ID) {
            $this->user_id = Vars::$USER_ID;
            $this->rights = Vars::$USER_RIGHTS;
            $this->ban = Vars::$USER_BAN;
        }
        // Назначение пользовательских прав
        if (Vars::$USER_ID && isset($arg['owner']) && $arg['owner'] == Vars::$USER_ID && !$this->ban) {
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

        switch (Vars::$MOD) {
            case 'reply':
                /*
                -----------------------------------------------------------------
                Отвечаем на комментарий
                -----------------------------------------------------------------
                */
                if ($this->item && $this->access_reply && !$this->ban) {
                    echo '<div class="phdr"><a href="' . $this->url . '"><b>' . $arg['title'] . '</b></a> | ' . Vars::$LNG['reply'] . '</div>';
                    $req = mysql_query("SELECT * FROM `" . $this->comments_table . "` WHERE `id` = '" . $this->item . "' AND `sub_id` = '" . $this->sub_id . "' LIMIT 1");
                    if (mysql_num_rows($req)) {
                        $res = mysql_fetch_assoc($req);
                        $attributes = unserialize($res['attributes']);
                        if (!empty($res['reply']) && $attributes['reply_rights'] > $this->rights) {
                            echo Functions::displayError(Vars::$LNG['error_reply_rights'], '<a href="' . $this->url . '">' . Vars::$LNG['back'] . '</a>');
                        } elseif (isset($_POST['submit'])) {
                            $message = $this->_messageCheck();
                            if (empty($message['error'])) {
                                $attributes['reply_id'] = $this->user_id;
                                $attributes['reply_rights'] = $this->rights;
                                $attributes['reply_name'] = Vars::$USER_DATA['name'];
                                $attributes['reply_time'] = time();
                                mysql_query("UPDATE `" . $this->comments_table . "` SET
                                    `reply` = '" . mysql_real_escape_string($message['text']) . "',
                                    `attributes` = '" . mysql_real_escape_string(serialize($attributes)) . "'
                                    WHERE `id` = '" . $this->item . "'
                                ");
                                header('Location: ' . str_replace('&amp;', '&', $this->url));
                            } else {
                                echo Functions::displayError($message['error'], '<a href="' . $this->url . '&amp;mod=reply&amp;item=' . $this->item . '">' . Vars::$LNG['back'] . '</a>');
                            }
                        } else {
                            $text = '<a href="' . Vars::$HOME_URL . '/users/profile.php?user=' . $res['user_id'] . '"><b>' . $attributes['author_name'] . '</b></a>' .
                                    ' (' . Functions::displayDate($res['time']) . ')<br />' .
                                    Validate::filterString($res['text']);
                            $reply = Validate::filterString($res['reply']);
                            echo $this->_messageForm('&amp;mod=reply&amp;item=' . $this->item, $text, $reply) .
                                 '<div class="phdr"><a href="' . $this->url . '">' . Vars::$LNG['back'] . '</a></div>';
                        }
                    } else {
                        echo Functions::displayError(Vars::$LNG['error_wrong_data'], '<a href="' . $this->url . '">' . Vars::$LNG['back'] . '</a>');
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
                    echo '<div class="phdr"><a href="' . $this->url . '"><b>' . $arg['title'] . '</b></a> | ' . Vars::$LNG['edit'] . '</div>';
                    $req = mysql_query("SELECT * FROM `" . $this->comments_table . "` WHERE `id` = '" . $this->item . "' AND `sub_id` = '" . $this->sub_id . "' LIMIT 1");
                    if (mysql_num_rows($req)) {
                        $res = mysql_fetch_assoc($req);
                        $attributes = unserialize($res['attributes']);
                        $user = Functions::getUser($res['user_id']);
                        if ($user['rights'] > Vars::$USER_RIGHTS) {
                            echo Functions::displayError(Vars::$LNG['error_edit_rights'], '<a href="' . $this->url . '">' . Vars::$LNG['back'] . '</a>');
                        } elseif (isset($_POST['submit'])) {
                            $message = $this->_messageCheck();
                            if (empty($message['error'])) {
                                $attributes['edit_id'] = $this->user_id;
                                $attributes['edit_name'] = Vars::$USER_DATA['name'];
                                $attributes['edit_time'] = time();
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
                                echo Functions::displayError($message['error'], '<a href="' . $this->url . '&amp;mod=edit&amp;item=' . $this->item . '">' . Vars::$LNG['back'] . '</a>');
                            }
                        } else {
                            $author = '<a href="' . Vars::$HOME_URL . '/users/profile.php?user=' . $res['user_id'] . '"><b>' . $attributes['author_name'] . '</b></a>';
                            $author .= ' (' . Functions::displayDate($res['time']) . ')<br />';
                            $text = Validate::filterString($res['text']);
                            echo $this->_messageForm('&amp;mod=edit&amp;item=' . $this->item, $author, $text);
                        }
                    } else {
                        echo Functions::displayError(Vars::$LNG['error_wrong_data'], '<a href="' . $this->url . '">' . Vars::$LNG['back'] . '</a>');
                    }
                    echo '<div class="phdr"><a href="' . $this->url . '">' . Vars::$LNG['back'] . '</a></div>';
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
                            $this->_totalMessages(1);
                        }
                        header('Location: ' . str_replace('&amp;', '&', $this->url));
                    } else {
                        echo '<div class="phdr"><a href="' . $this->url . '"><b>' . $arg['title'] . '</b></a> | ' . Vars::$LNG['delete'] . '</div>' .
                             '<div class="rmenu"><p>' . Vars::$LNG['delete_confirmation'] . '<br />' .
                             '<a href="' . $this->url . '&amp;mod=del&amp;item=' . $this->item . '&amp;yes">' . Vars::$LNG['delete'] . '</a> | ' .
                             '<a href="' . $this->url . '">' . Vars::$LNG['cancel'] . '</a><br />' .
                             '<div class="sub">' . Vars::$LNG['clear_user_msg'] . '<br />' .
                             '<span class="red"><a href="' . $this->url . '&amp;mod=del&amp;item=' . $this->item . '&amp;yes&amp;all">' . Vars::$LNG['clear'] . '</a></span>' .
                             '</div></p></div>' .
                             '<div class="phdr"><a href="' . $this->url . '">' . Vars::$LNG['back'] . '</a></div>';
                    }
                }
                break;

            default:
                if (!empty($arg['context_top']))
                    echo $arg['context_top'];

                /*
                -----------------------------------------------------------------
                Добавляем новый комментарий
                -----------------------------------------------------------------
                */
                if(!$this->ban && isset($_POST['submit']) && ($message = $this->_messageCheck(1)) !== false){
                    if (empty($message['error'])) {
                        // Записываем комментарий в базу
                        $this->_addComment($message['text']);
                        $this->total = $this->_totalMessages(1);
                        $_SESSION['code'] = $message['code'];
                    } else {
                        // Показываем ошибки, если есть
                        echo Functions::displayError($message['error']);
                        $this->total = $this->_totalMessages();
                    }
                } else {
                    $this->total = $this->_totalMessages();
                }

                /*
                -----------------------------------------------------------------
                Показываем форму ввода
                -----------------------------------------------------------------
                */
                if (!$this->ban) {
                    echo $this->_messageForm();
                }

                /*
                -----------------------------------------------------------------
                Показываем список комментариев
                -----------------------------------------------------------------
                */
                echo '<div class="phdr"><b>' . $arg['title'] . '</b></div>';
                if ($this->total > Vars::$USER_SET['page_size']) echo '<div class="topmenu">' . Functions::displayPagination($this->url . '&amp;', Vars::$START, $this->total, Vars::$USER_SET['page_size']) . '</div>';
                if ($this->total) {
                    $req = mysql_query("SELECT `" . $this->comments_table . "`.*, `" . $this->comments_table . "`.`id` AS `subid`, `users`.`rights`, `users`.`lastdate`, `users`.`sex`, `users`.`status`, `users`.`datereg`, `users`.`id`
                    FROM `" . $this->comments_table . "` LEFT JOIN `users` ON `" . $this->comments_table . "`.`user_id` = `users`.`id`
                    WHERE `sub_id` = '" . $this->sub_id . "' ORDER BY `subid` DESC LIMIT " . Vars::db_pagination());
                    $i = 0;
                    while (($res = mysql_fetch_assoc($req)) !== false) {
                        $attributes = unserialize($res['attributes']);
                        $res['name'] = $attributes['author_name'];
                        $res['ip'] = $attributes['author_ip'];
                        $res['ip_via_proxy'] = isset($attributes['author_ip_via_proxy']) ? $attributes['author_ip_via_proxy'] : 0;
                        $res['browser'] = $attributes['author_browser'];
                        echo $i % 2 ? '<div class="list2">' : '<div class="list1">';
                        $menu = array(
                            $this->access_reply ? '<a href="' . $this->url . '&amp;mod=reply&amp;item=' . $res['subid'] . '">' . Vars::$LNG['reply'] . '</a>' : '',
                            $this->access_edit ? '<a href="' . $this->url . '&amp;mod=edit&amp;item=' . $res['subid'] . '">' . Vars::$LNG['edit'] . '</a>' : '',
                            $this->access_delete ? '<a href="' . $this->url . '&amp;mod=del&amp;item=' . $res['subid'] . '">' . Vars::$LNG['delete'] . '</a>' : ''
                        );
                        $text = Validate::filterString($res['text'], 1, 1);
                        if (Vars::$USER_SET['smileys'])
                            $text = Functions::smileys($text, $res['rights'] >= 1 ? 1 : 0);
                        if (isset($attributes['edit_count'])) {
                            $text .= '<br /><span class="gray"><small>' . Vars::$LNG['edited'] . ': <b>' . $attributes['edit_name'] . '</b>' .
                                     ' (' . Functions::displayDate($attributes['edit_time']) . ') <b>' .
                                     '[' . $attributes['edit_count'] . ']</b></small></span>';
                        }
                        if (!empty($res['reply'])) {
                            $reply = Validate::filterString($res['reply'], 1, 1);
                            if (Vars::$USER_SET['smileys'])
                                $reply = Functions::smileys($reply, $attributes['reply_rights'] >= 1 ? 1 : 0);
                            $text .= '<div class="' . ($attributes['reply_rights'] ? '' : 'g') . 'reply"><small>' .
                                     '<a href="' . Vars::$HOME_URL . '/users/profile.php?user=' . $attributes['reply_id'] . '"><b>' . $attributes['reply_name'] . '</b></a>' .
                                     ' (' . Functions::displayDate($attributes['reply_time']) . ')</small><br/>' . $reply . '</div>';
                        }
                        $user_arg = array(
                            'header' => ' <span class="gray">(' . Functions::displayDate($res['time']) . ')</span>',
                            'body' => $text,
                            'sub' => Functions::displayMenu($menu),
                            'iphide' => (Vars::$USER_RIGHTS ? false : true)
                        );
                        echo Functions::displayUser($res, $user_arg);
                        echo '</div>';
                        ++$i;
                    }
                } else {
                    echo '<div class="menu"><p>' . Vars::$LNG['list_empty'] . '</p></div>';
                }
                echo '<div class="phdr">' . Vars::$LNG['total'] . ': ' . $this->total . '</div>';
                if ($this->total > Vars::$USER_SET['page_size']) {
                    echo '<div class="topmenu">' . Functions::displayPagination($this->url . '&amp;', Vars::$START, $this->total, Vars::$USER_SET['page_size']) . '</div>' .
                         '<p><form action="' . $this->url . '" method="post">' .
                         '<input type="text" name="page" size="2"/>' .
                         '<input type="submit" value="' . Vars::$LNG['to_page'] . ' &gt;&gt;"/>' .
                         '</form></p>';
                }
                if (!empty($arg['context_bottom']))
                    echo $arg['context_bottom'];
        }
    }

    /*
    -----------------------------------------------------------------
    Добавляем комментарий в базу
    -----------------------------------------------------------------
    */
    private function _addComment($message)
    {
        // Формируем атрибуты сообщения
        $attributes = array(
            'author_name' => Vars::$USER_DATA['name'],
            'author_ip' => Vars::$IP,
            'author_ip_via_proxy' => Vars::$IP_VIA_PROXY,
            'author_browser' => Vars::$USER_AGENT
        );
        // Записываем комментарий в базу
        mysql_query("INSERT INTO `" . $this->comments_table . "` SET
            `sub_id` = '" . intval($this->sub_id) . "',
            `user_id` = '" . $this->user_id . "',
            `text` = '" . mysql_real_escape_string($message) . "',
            `time` = '" . time() . "',
            `attributes` = '" . mysql_real_escape_string(serialize($attributes)) . "'
        ");
        // Обновляем статистику пользователя
        mysql_query("UPDATE `users` SET `komm` = '" . (++Vars::$USER_DATA['komm']) . "', `lastpost` = '" . time() . "' WHERE `id` = '" . $this->user_id . "'");
        $this->added = true;
    }

    /*
    -----------------------------------------------------------------
    Форма ввода комментария
    -----------------------------------------------------------------
    */
    private function _messageForm($submit_link = '', $text = '', $reply = '')
    {
        return '<div class="gmenu"><form name="form" action="' . $this->url . $submit_link . '" method="post"><p>' .
               (!empty($text) ? '<div class="quote">' . $text . '</div></p><p>' : '') .
               '<b>' . Vars::$LNG['message'] . '</b>: <small>(Max. ' . $this->max_lenght . ')</small><br />' .
               (!Vars::$IS_MOBILE ? '</p><p>' . TextParser::autoBB('form', 'message') : '') .
               '<textarea rows="' . Vars::$USER_SET['field_h'] . '" name="message">' . $reply . '</textarea><br/>' .
               (Vars::$USER_SET['translit'] ? '<input type="checkbox" name="translit" value="1" />&nbsp;' . Vars::$LNG['translit'] . '<br/>' : '') .
               '<input type="hidden" name="code" value="' . mt_rand(1000, 9999) . '" /><input type="submit" name="submit" value="' . Vars::$LNG['sent'] . '"/></p></form></div>';
    }

    /*
    -----------------------------------------------------------------
    Проверка текста сообщения
    -----------------------------------------------------------------
    $rpt_check (boolean)    проверка на повтор сообщений
    -----------------------------------------------------------------
    */
    private function _messageCheck($rpt_check = false)
    {
        $error = array();
        $message = isset($_POST['message']) ? mb_substr(trim($_POST['message']), 0, $this->max_lenght) : false;
        $code = isset($_POST['code']) ? intval($_POST['code']) : NULL;
        $code_chk = isset($_SESSION['code']) ? $_SESSION['code'] : NULL;
        $translit = isset($_POST['translit']);
        // Проверяем код
        if($code == $code_chk) return false;
        // Проверяем на минимально допустимую длину
        if (mb_strlen($message) < $this->min_lenght) {
            $error[] = Vars::$LNG['error_message_short'];
        } else {
            // Проверка на флуд
            $flood = Functions::antiFlood();
            if ($flood)
                $error[] = Vars::$LNG['error_flood'] . ' ' . $flood . '&#160;' . Vars::$LNG['seconds'];
        }
        // Проверка на повтор сообщений
        if (!$error && $rpt_check) {
            $req = mysql_query("SELECT * FROM `" . $this->comments_table . "` WHERE `user_id` = '" . $this->user_id . "' ORDER BY `id` DESC LIMIT 1");
            $res = mysql_fetch_assoc($req);
            if (mb_strtolower($message) == mb_strtolower($res['text']))
                $error[] = Vars::$LNG['error_message_exists'];
        }
        // Транслит сообщения
        if (!$error && $translit)
            $message = Functions::translit($message);
        // Возвращаем результат
        return array(
            'code' => $code,
            'text' => $message,
            'error' => $error
        );
    }

    /*
    -----------------------------------------------------------------
    Счетчик комментариев
    -----------------------------------------------------------------
    */
    private function _totalMessages($update = false)
    {
        $total = mysql_result(mysql_query("SELECT COUNT(*) FROM `" . $this->comments_table . "` WHERE `sub_id` = '" . $this->sub_id . "'"), 0);
        if ($update) {
            // Обновляем счетчики в таблице объекта
            mysql_query("UPDATE `" . $this->object_table . "` SET `comm_count` = '$total' WHERE `id` = '" . $this->sub_id . "'");
        }
        return $total;
    }
}