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
    // Стиль отображения
    public $style_list1 = 'list1';  // Стиль списка 1
    public $style_list2 = 'list2';  // Стиль списка 2
    public $style_empty = 'menu';   // Стиль пустого списка
    public $style_form = 'gmenu';   // Стиль формы ввода комментария
    public $style_error = 'rmenu';  // Стиль сообщения об ошибке
    public $style_func = 'sub';     // Стиль блока служебных ссылок
    public $style_reply = 'func';   // Стиль ответа на сообщение

    // Параметры отображения комментариев
    public $sub_id;                 // Идентификатор комментируемого объекта
    public $sub_id_name = 'id';     // Имя идентификатора объекта
    public $script;                 // Имя скрипта, использующего класс
    public $min_lenght = 2;         // Мин. к-во символов в комментарии
    public $max_lenght = 1000;      // Макс. к-во символов в комментарии
    public $reply = false;          // Возможность отвечать на комментарий
    public $reply_author = false;   // Идентификатор юзера, которому разрешено отвечать
    public $check_table = false;    // Таблица проверки идентификатора объекта
    public $check_field = 'id';     // Поле в таблице, для проверки идентификатора
    public $captcha = false;        // Показывать CAPTCHA
    
    // Служебные данные
    private $table = false;         // Таблица базы данных
    private $module;                // Модуль, использующий комментарии
    private $lng;                   // Язык (фразы)
    private $ban = false;           // Находится ли юзер в бане?
    private $url;                   // URL формируемых ссылок

    /*
    -----------------------------------------------------------------
    Конструктор класса
    -----------------------------------------------------------------
    */
    function __construct($table, $module){
        global $lng, $ban, $user_id;
        $this->table = $table;
        $this->module = $module;
        $this->lng = $lng;
    }

    /*
    -----------------------------------------------------------------
    Переключаем режимы работы и показываем комментарии
    -----------------------------------------------------------------
    */
    public function display_comments() {
        global $mod, $ban, $set;
        // Формируем служебные ссылки
        $this->url = $this->script . '&amp;' . $this->sub_id_name . '=' . $this->sub_id;
        $item = isset($_POST['item']) ? abs(intval($_POST['item'])) : false;
        switch ($mod) {
            case 'edit': break;

            case 'delete':
                $this->delete($item);
                break;

            case 'add':
                // Добавляем комментарий
                $error = $this->add();
                if (!empty($error))
                    echo functions::display_error($error, '<a href="' . $this->url . '">' . $this->lng['back'] . '</a>');
                else
                    header('Location: ' . str_replace('&amp;', '&', $this->url));
                break;

            default:
                // Показываем комментарии
                if (!$ban)
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
        global $start, $kmess, $rights;
        $req = mysql_query("SELECT * FROM `" . $this->table . "` WHERE `module` = '" . $this->module . "' AND `sub_id` = '" . $this->sub_id . "' ORDER BY `id` DESC LIMIT $start, $kmess");

        if (mysql_num_rows($req)) {
            $out = '';
            while ($res = mysql_fetch_assoc($req)) {
                $out .= $i % 2 ? '<div class="' . $this->style_list2 . '">' : '<div class="' . $this->style_list1 . '">';
                $out .= functions::smileys(functions::checkout($res['text'], 1, 1));
                if ($rights >= 6) {
                    $out .= '<div class="' . $this->style_func . '">';
                    $out .= '<a href="">' . $this->lng['edit'] . '</a> | ';
                    $out .= '<a href="' . $this->url . '&amp;mod=delete&amp;item=' . $res['id'] . '">' . $this->lng['delete'] . '</a>';
                    $out .= '</div>';
                }
                $out .= '</div>';
                ++$i;
            }
            return $out;
        } else {
            return '<div class="' . $this->style_empty . '"><p>' . $this->lng['list_empty'] . '</p></div>';
        }
    }

    /*
    -----------------------------------------------------------------
    Форма ввода комментария
    -----------------------------------------------------------------
    */
    private function submit_form() {
        global $set_user;
        $out = '<div class="' . $this->style_form . '"><form action="' . $this->script . '&amp;mod=add&amp;img=' . $this->sub_id . '" method="post">';
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

        // Транслит сообщения
        if ($translit)
            $message = functions::trans($message);
        // Проверка на флуд
        $flood = functions::antiflood();

        if ($flood)
            return $flood;

        // Проверяем на минимально допустимую длину
        if (mb_strlen($message) < $this->min_lenght)
            return $this->lng['error_text_short'];
        // Проверяем на повтор сообщений
        $req = mysql_query("SELECT * FROM `" . $this->table . "` WHERE `user_id` = '$user_id' ORDER BY `id` DESC LIMIT 1");
        $res = mysql_fetch_assoc($req);

        if ($message == $res['text'])
            return $this->lng['error_message_exists'];
        // Записываем комментарий в базу
        mysql_query("INSERT INTO `" . $this->table . "` SET
            `module` = '" . $this->module . "',
            `sub_id` = '" . intval($this->sub_id) . "',
            `user_id` = '$user_id',
            `text` = '" . mysql_real_escape_string($message) . "',
            `time` = '$realtime',
            `ip` = '" . $ip . "',
            `ua` = '" . mysql_real_escape_string($agn) . "'
        ");
        // Обновляем статистику пользователя
        $comm_count = $datauser['komm'] + 1;
        mysql_query("UPDATE `users` SET `komm` = '$comm_count', `lastpost` = '$realtime' WHERE `id` = '$user_id' LIMIT 1");
    }

    /*
    -----------------------------------------------------------------
    Удаляем комментарий
    -----------------------------------------------------------------
    */
    private function delete($id = false) {
        if(!$id)
            return false;
    }

    /*
    -----------------------------------------------------------------
    Редактируем комментарий
    -----------------------------------------------------------------
    */
    private function edit() { }

    /*
    -----------------------------------------------------------------
    Проверяем идентификатор
    -----------------------------------------------------------------
    */
    private function check_sub() {
        if (!$this->check_table) {
            return true;
        } else {
            $req = mysql_query("SELECT * FROM `" . $this->check_table . "` WHERE `" . $this->check_field . "` = '" . $this->sub_id . "' LIMIT 1");
            if (mysql_num_rows($req))
                return true;
            else
                return false;
        }
    }
}
?>