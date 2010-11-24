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

defined('_IN_JOHNCMS') or die('Restricted access');
class comments {
    // Служебные данные
    private $object_table;    // Таблица комментируемых объектов
    private $comments_table;  // Таблица с комментариями
    private $sub_id = false;  // Идентификатор комментируемого объекта
    private $item;            // Локальный идентификатор
    private $user_id = false; // Идентификатор авторизованного пользователя
    private $rights = 0;      // Права доступа
    private $ban = false;     // Находится ли юзер в бане?
    private $url;             // URL формируемых ссылок

    // Права доступа
    private $access_reply = false;  // Возможность отвечать на комментарий
    private $access_edit = false;   // Возможность редактировать комментарий
    private $access_delete = false; // Возможность удалять комментарий
    private $access_level = 6;      // Уровень доступа для Администрации

    // Параметры отображения комментариев
    public $min_lenght = 4;    // Мин. к-во символов в комментарии
    public $max_lenght = 5000; // Макс. к-во символов в комментарии
    public $captcha = false;   // Показывать CAPTCHA

    /*
    =================================================================
    Конструктор класса
    =================================================================
    */
    function __construct($arg = array ()) {
        global $lng, $user_id, $rights, $ban, $mod;
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
        if ($user_id) {
            $this->user_id = $user_id;
            $this->rights = $rights;
            $this->ban = $ban;
        }

        // Назначение пользовательских прав
        if ($arg['owner'] == $user_id) {
            $this->access_delete = isset($arg['owner_delete']);
            $this->access_reply = isset($arg['owner_reply']);
            $this->access_edit = isset($arg['owner_edit']);
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
                =================================================================
                Отвечаем на комментарий
                =================================================================
                */
                if ($this->access_reply || !$this->ban) {
                    $req = mysql_query("SELECT * FROM `" . $this->comments_table . "` WHERE `id` = '" . $this->item . "'");
                    if (isset($_POST['submit'])) {
                        $msg = '';
                    } else {
                        echo $this->msg_form($this->item);
                    }
                }
                break;

            case 'edit':
            /*
            =================================================================
            Редактируем комментарий
            =================================================================
            */
            break;

            case 'del':
                /*
                =================================================================
                Удаляем комментарий
                =================================================================
                */
                if (isset($_GET['yes'])) {
                    $this->msg_del($this->item, isset($_GET['all']));
                    header('Location: ' . str_replace('&amp;', '&', $this->url));
                } else {
                    echo '<div class="rmenu"><p>' . $lng['delete_confirmation'] . '<br />' .
                        '<a href="' . $this->url . '&amp;mod=del&amp;item=' . $this->item . '&amp;yes">' . $lng['delete'] . '</a> | ' .
                        '<a href="' . $this->url . '">' . $lng['cancel'] . '</a><br />' .
                        '<div class="sub">' . $lng['clear_user_msg'] . '<br />' .
                        '<span class="red"><a href="' . $this->url . '&amp;mod=del&amp;item=' . $this->item . '&amp;yes&amp;all">' . $lng['clear'] . '</a></span></div></p></div>';
                }
                break;

            case 'add_comment':
                /*
                =================================================================
                Добавляем комментарий
                =================================================================
                */
                $message = $this->msg_check(1);
                if (empty($message['error'])) {
                    $this->msg_add($message['text']);
                    header('Location: ' . str_replace('&amp;', '&', $this->url));
                } else {
                    echo functions::display_error($message['error'], '<a href="' . $this->url . '">' . $lng['back'] . '</a>');
                }
                break;

            default:
                /*
                =================================================================
                Показываем комментарии
                =================================================================
                */
                if (!$this->ban)
                    echo $this->msg_form();
                echo $this->msg_list();
        }
    }

    /*
    =================================================================
    Листинг комментариев
    =================================================================
    */
    private function msg_list() {
        global $start, $kmess, $lng;
        $total = $this->msg_total();

        if ($total) {
            $out = '';
            $req = mysql_query("SELECT `" . $this->comments_table . "`.*, `" . $this->comments_table . "`.`id` AS `subid`, `users`.`rights`, `users`.`lastdate`, `users`.`sex`, `users`.`status`, `users`.`datereg`, `users`.`id`
                FROM `" . $this->comments_table . "` LEFT JOIN `users` ON `" . $this->comments_table . "`.`user_id` = `users`.`id`
                WHERE `sub_id` = '" . $this->sub_id . "' ORDER BY `subid` DESC LIMIT $start, $kmess
            ");
            while ($res = mysql_fetch_assoc($req)) {
                $attributes = unserialize($res['attributes']);
                $res['name'] = $attributes['author_name'];
                $res['ip'] = $attributes['author_ip'];
                $res['browser'] = $attributes['author_browser'];
                $out .= $i % 2 ? '<div class="list2">' : '<div class="list1">';
                $menu = array (
                    $this->access_reply ? '<a href="' . $this->url . '&amp;mod=reply&amp;item=' . $res['subid'] . '">' . $lng['reply'] . '</a>' : '',
                    $this->access_edit ? '<a href="' . $this->url . '&amp;mod=edit&amp;item=' . $res['subid'] . '">' . $lng['edit'] . '</a>' : '',
                    $this->access_delete ? '<a href="' . $this->url . '&amp;mod=del&amp;item=' . $res['subid'] . '">' . $lng['delete'] . '</a>' : ''
                );
                //TODO: Добавить выключение смайлов
                $arg = array (
                    'header' => ' <span class="gray">(' . date("d.m.Y / H:i:s", $res['time'] + $set_user['sdvig'] * 3600) . ')</span>',
                    'body' => functions::smileys(functions::checkout($res['text'], 1, 1)),
                    'sub' => functions::display_menu($menu)
                );
                $out .= functions::display_user($res, $arg);
                $out .= '</div>';
                ++$i;
            }
            $out .= '<div class="phdr">' . $lng['total'] . ': ' . $total . '</div>';
            if($total > $kmess){
                $out .= functions::display_pagination($this->url . '&amp;', $start, $total, $kmess);
                $out .= '<p><form action="' . $this->url . '" method="post"><input type="text" name="page" size="2"/><input type="submit" value="' . $lng['to_page'] . ' &gt;&gt;"/></form></p>';
            }
            return $out;
        } else {
            return '<div class="menu"><p>' . $lng['list_empty'] . '</p></div>';
        }
    }

    /*
    =================================================================
    Форма ввода комментария
    -----------------------------------------------------------------
    $mode = 0               Добавление коммментария
    $mode = 1               Редактирование комментария
    $mode = 2               Ответ на комментарий
    =================================================================
    */
    private function msg_form($mode = 0) {
        global $set_user, $lng;
        $out = '<div class="gmenu"><form action="' . $this->url . '&amp;mod=add_comment" method="post">';
        $out .= $lng['message'] . ':<br /><textarea cols="' . $set_user['field_w'] . '" rows="' . $set_user['field_h'] . '" name="message"></textarea><br/>';

        if ($set_user['translit'])
            $out .= '<input type="checkbox" name="translit" value="1" />&nbsp;' . $lng['translit'] . '<br/>';
        $out .= '<input type="submit" name="submit" value="' . $lng['sent'] . '"/></form></div>';
        return $out;
    }

    /*
    =================================================================
    Проверка текста сообщения
    -----------------------------------------------------------------
    $rpt_check (boolean)    проверка на повтор сообщений
    =================================================================
    */
    private function msg_check($rpt_check = false) {
        global $lng;
        $error = array ();
        $message = isset($_POST['message']) ? mb_substr(trim($_POST['message']), 0, $this->max_lenght) : false;
        $translit = isset($_POST['translit']);

        // Проверяем на минимально допустимую длину
        if (mb_strlen($message) < $this->min_lenght)
            $error[] = $lng['error_message_short'];

        // Проверка на флуд
        $flood = functions::antiflood();

        if ($flood)
            $error[] = $lng['error_flood'] . ' ' . $flood . '&#160;' . $lng['seconds'];

        // Проверка на повтор сообщений
        if ($rpt_check) {
            $req = mysql_query("SELECT * FROM `" . $this->comments_table . "` WHERE `user_id` = '" . $this->user_id . "' ORDER BY `id` DESC LIMIT 1");
            $res = mysql_fetch_assoc($req);
            if (mb_strtolower($message) == mb_strtolower($res['text']))
                $error[] = $lng['error_message_exists'];
        }

        // Транслит сообщения
        if (!$error && $translit)
            $message = functions::trans($message);

        // Возвращаем результат
        return array (
            'text' => $message,
            'error' => $error
        );
    }

    /*
    =================================================================
    Добавление сообщения в базу
    -----------------------------------------------------------------
    $message (string)       текст сообщения
    =================================================================
    */
    private function msg_add($message = '') {
        global $datauser, $realtime, $ip, $agn;

        // Формируем атрибуты сообщения
        $attributes = array (
            'author_name' => $datauser['name'],
            'author_ip' => $ip,
            'author_browser' => $agn
        );

        // Записываем комментарий в базу
        mysql_query("INSERT INTO `" . $this->comments_table . "` SET
            `sub_id` = '" . intval($this->sub_id) . "',
            `user_id` = '" . $this->user_id . "',
            `text` = '" . mysql_real_escape_string($message) . "',
            `time` = '$realtime',
            `attributes` = '" . mysql_real_escape_string(serialize($attributes)) . "'
        ");
        // Обновляем счетчик комментариев
        $this->msg_total(1);
        // Обновляем статистику пользователя
        mysql_query("UPDATE `users` SET `komm` = '" . ($datauser['komm'] + 1) . "', `lastpost` = '$realtime' WHERE `id` = '" . $this->user_id . "'");
    }

    /*
    =================================================================
    Удаляем комментарии
    -----------------------------------------------------------------
    $item  (int)            идентификатор объекта
    $clear (boolean)        массовое удаление
    =================================================================
    */
    private function msg_del($item = false, $clear = false) {
        if ($item && $this->access_delete) {
            //TODO: Написать удаление баллов из статистики юзера
            $req = mysql_query("SELECT * FROM `" . $this->comments_table . "` WHERE `id` = '$item' AND `sub_id` = '" . $this->sub_id . "' AND `module` = '" . $this->module . "' LIMIT 1");
            if (mysql_num_rows($req)) {
                $res = mysql_fetch_assoc($req);
                if ($clear) {
                    $count = mysql_query("SELECT COUNT(*) FROM `" . $this->comments_table . "`");
                    mysql_query("DELETE FROM `" . $this->comments_table . "` WHERE `sub_id` = '" . $this->sub_id . "' AND `user_id` = '" . $res['user_id'] . "'");
                } else {
                    $count = 1;
                    mysql_query("DELETE FROM `" . $this->comments_table . "` WHERE `id` = '$item'");
                }
            } else {
                return false;
            }
            return true;
        } else {
            return false;
        }
    }

    /*
    =================================================================
    Редактируем комментарий
    =================================================================
    */
    private function msg_edit() { }

    /*
    =================================================================
    Счетчик комментариев
    -----------------------------------------------------------------
    $update (boolean)       Обновить статистику в таблице объекта
    =================================================================
    */
    private function msg_total($update = false) {
        $total = mysql_result(mysql_query("SELECT COUNT(*) FROM `" . $this->comments_table . "` WHERE `sub_id` = '" . $this->sub_id . "'"), 0);

        if ($update) {
            mysql_query("UPDATE `" . $this->object_table . "` SET
                `comm_count` = '$total'
                WHERE `id` = '" . $this->sub_id . "'
            ");
        }
        return $total;
    }
}
?>