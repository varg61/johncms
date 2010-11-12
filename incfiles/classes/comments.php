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
    // Параметры отображения комментариев
    public $sub_id;             // Идентификатор комментируемого объекта
    public $sub_id_name = 'id'; // Имя идентификатора объекта
    public $script;             // Имя скрипта, использующего класс
    public $min_lenght = 4;     // Мин. к-во символов в комментарии
    public $max_lenght = 1000;  // Макс. к-во символов в комментарии
    public $captcha = false;    // Показывать CAPTCHA

    // Права доступа
    public $msg_reply = false;  // Возможность отвечать на комментарий
    public $msg_edit = false;   // Возможность редактировать комментарий
    public $msg_delete = false; // Возможность удалять комментарий
    public $access_level = 6;   // Уровень доступа для Администрации

    // Служебные данные
    private $table = false; // Таблица базы данных
    private $module;        // Модуль, использующий комментарии
    private $lng;           // Язык (фразы)
    private $ban = false;   // Находится ли юзер в бане?
    private $url;           // URL формируемых ссылок

    /*
    -----------------------------------------------------------------
    Конструктор класса
    -----------------------------------------------------------------
    */
    function __construct($table, $module) {
        global $lng, $ban, $rights;
        $this->table = $table;
        $this->module = $module;
        $this->lng = $lng;
        $this->ban = $ban;

        if ($rights >= $this->access_level) {
            $this->msg_reply = true;
            $this->msg_edit = true;
            $this->msg_delete = true;
        }
    }

    /*
    -----------------------------------------------------------------
    Переключаем режимы работы и показываем комментарии
    -----------------------------------------------------------------
    */
    public function display_comments() {
        global $mod, $set;
        // Формируем служебные ссылки
        $this->url = $this->script . '&amp;' . $this->sub_id_name . '=' . $this->sub_id;
        $item = isset($_GET['item']) ? abs(intval($_GET['item'])) : false;

        switch ($mod) {
            case 'reply':
            /*
            -----------------------------------------------------------------
            Отвечаем на комментарий
            -----------------------------------------------------------------
            */
            break;

            case 'edit':
            /*
            -----------------------------------------------------------------
            Редактируем комментарий
            -----------------------------------------------------------------
            */
            break;

            case 'del':
                /*
                -----------------------------------------------------------------
                Удаляем комментарий
                -----------------------------------------------------------------
                */
                if (isset($_GET['yes'])) {
                    $this->delete($item, isset($_GET['all']));
                    header('Location: ' . str_replace('&amp;', '&', $this->url));
                } else {
                    echo '<div class="rmenu"><p>' . $this->lng['delete_confirmation'] . '<br />' .
                        '<a href="' . $this->url . '&amp;mod=del&amp;item=' . $item . '&amp;yes">' . $this->lng['delete'] . '</a> | ' .
                        '<a href="' . $this->url . '">' . $this->lng['cancel'] . '</a><br /><br />' .
                        '<span class="red">' . $this->lng['clear_user_msg'] . '<br />' .
                        '<a href="' . $this->url . '&amp;mod=del&amp;item=' . $item . '&amp;yes&amp;all">' . $this->lng['clear'] . '</a></span></p></div>';
                }
                break;

            case 'add':
                /*
                -----------------------------------------------------------------
                Показываем комментарии
                -----------------------------------------------------------------
                */
                // Добавляем комментарий
                $error = $this->add();
                if (!empty($error))
                    echo functions::display_error($error, '<a href="' . $this->url . '">' . $this->lng['back'] . '</a>');
                else
                    header('Location: ' . str_replace('&amp;', '&', $this->url));
                break;

            default:
                /*
                -----------------------------------------------------------------
                Показываем комментарии
                -----------------------------------------------------------------
                */
                if (!$this->ban)
                    echo $this->submit_form();
                echo $this->comments_list();
        }
    }

    /*
    -----------------------------------------------------------------
    Листинг комментариев
    -----------------------------------------------------------------
    */
    private function comments_list() {
        global $start, $kmess, $rights, $user_id;
        $req = mysql_query("SELECT `" . $this->table . "`.*, `" . $this->table . "`.`id` AS `subid`, `users`.`rights`, `users`.`lastdate`, `users`.`sex`, `users`.`status`, `users`.`datereg`, `users`.`id`
            FROM `" . $this->table . "` LEFT JOIN `users` ON `" . $this->table . "`.`user_id` = `users`.`id`
            WHERE `module` = '" . $this->module . "' AND `sub_id` = '" . $this->sub_id . "' ORDER BY `subid` DESC LIMIT $start, $kmess
        ");

        if (mysql_num_rows($req)) {
            $out = '';
            while ($res = mysql_fetch_assoc($req)) {
                $out .= $i % 2 ? '<div class="list2">' : '<div class="list1">';
                // Формируем служебное Меню
                $menu = array (
                    $this->msg_reply ? '<a href="' . $this->url . '&amp;mod=reply&amp;item=' . $res['subid'] . '">' . $this->lng['reply'] . '</a>' : '',
                    $this->msg_edit ? '<a href="' . $this->url . '&amp;mod=edit&amp;item=' . $res['subid'] . '">' . $this->lng['edit'] . '</a>' : '',
                    $this->msg_delete ? '<a href="' . $this->url . '&amp;mod=del&amp;item=' . $res['subid'] . '">' . $this->lng['delete'] . '</a>' : ''
                );
                $arg = array (
                    'header' => ' <span class="gray">(' . date("d.m.Y / H:i:s", $res['time'] + $set_user['sdvig'] * 3600) . ')</span>',
                    'body' => functions::smileys(functions::checkout($res['text'], 1, 1)),
                    'sub' => functions::display_menu($menu)
                );
                $out .= functions::display_user($res, $arg);
                $out .= '</div>';
                ++$i;
            }
            return $out;
        } else {
            return '<div class="menu"><p>' . $this->lng['list_empty'] . '</p></div>';
        }
    }

    /*
    -----------------------------------------------------------------
    Форма ввода комментария
    -----------------------------------------------------------------
    */
    private function submit_form() {
        global $set_user;
        $out = '<div class="gmenu"><form action="' . $this->script . '&amp;mod=add&amp;img=' . $this->sub_id . '" method="post">';
        $out .= $this->lng['message'] . ':<br /><textarea cols="' . $set_user['field_w'] . '" rows="' . $set_user['field_h'] . '" name="message"></textarea><br/>';

        if ($set_user['translit'])
            $out .= '<input type="checkbox" name="translit" value="1" />&nbsp;' . $this->lng['translit'] . '<br/>';
        $out .= '<input type="submit" name="submit" value="' . $this->lng['sent'] . '"/></form></div>';
        return $out;
    }

    /*
    -----------------------------------------------------------------
    Добавление сообщения в базу
    -----------------------------------------------------------------
    */
    private function add() {
        global $datauser, $user_id, $realtime, $ip, $agn;
        $message = isset($_POST['message']) ? mb_substr(trim($_POST['message']), 0, $this->max_lenght) : false;
        $translit = isset($_POST['translit']);
        $error = array ();

        // Транслит сообщения
        if ($translit)
            $message = functions::trans($message);
        // Проверка на флуд
        $flood = functions::antiflood();

        if ($flood)
            $error[] = $this->lng['error_flood'] . ' ' . $flood . '&#160;' . $this->lng['seconds'];

        // Проверяем на минимально допустимую длину
        if (mb_strlen($message) < $this->min_lenght)
            $error[] = $this->lng['error_message_short'];

        // Проверяем на повтор сообщений
        $req = mysql_query("SELECT * FROM `" . $this->table . "` WHERE `user_id` = '$user_id' ORDER BY `id` DESC LIMIT 1");
        $res = mysql_fetch_assoc($req);

        if (mb_strtolower($message) == mb_strtolower($res['text']))
            $error[] = $this->lng['error_message_exists'];

        if (!$error) {
            // Записываем комментарий в базу
            mysql_query("INSERT INTO `" . $this->table . "` SET
                `module` = '" . $this->module . "',
                `sub_id` = '" . intval($this->sub_id) . "',
                `user_id` = '$user_id',
                `name` = '" . mysql_real_escape_string($datauser['name']) . "',
                `text` = '" . mysql_real_escape_string($message) . "',
                `time` = '$realtime',
                `ip` = '" . $ip . "',
                `browser` = '" . mysql_real_escape_string($agn) . "'
            ");
            // Обновляем статистику пользователя
            $comm_count = $datauser['komm'] + 1;
            mysql_query("UPDATE `users` SET `komm` = '$comm_count', `lastpost` = '$realtime' WHERE `id` = '$user_id'");
        }
        return $error;
    }

    /*
    -----------------------------------------------------------------
    Удаляем комментарии
    -----------------------------------------------------------------
    */
    private function delete($id = false, $clear = false) {
        if ($id && $this->msg_delete) {
            $req = mysql_query("SELECT * FROM `" . $this->table . "` WHERE `id` = '$id' AND `sub_id` = '" . $this->sub_id . "' AND `module` = '" . $this->module . "' LIMIT 1");
            if (mysql_num_rows($req)) {
                if ($clear) {
                    $res = mysql_fetch_assoc($req);
                    mysql_query("DELETE FROM `" . $this->table . "` WHERE `module` = '" . $this->module . "' AND `sub_id` = '" . $this->sub_id . "' AND `user_id` = '" . $res['user_id'] . "'");
                } else {
                    mysql_query("DELETE FROM `" . $this->table . "` WHERE `id` = '$id'");
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
    -----------------------------------------------------------------
    Редактируем комментарий
    -----------------------------------------------------------------
    */
    private function edit() { }
}
?>