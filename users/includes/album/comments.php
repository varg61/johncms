<?php

// Проверяем наличие комментируемого объекта
$req = mysql_query("SELECT * FROM `cms_album_files` WHERE `id` = '$img' LIMIT 1");
if (mysql_num_rows($req)) {
    $res = mysql_fetch_assoc($req);

    /*
    -----------------------------------------------------------------
    Параметры комментариев
    -----------------------------------------------------------------
    */
    $arg = array (
        'comments_table' => 'cms_album_comments', // Таблица с комментариями
        'object_table' => 'cms_album_files',      // Таблица комментируемых объектов
        'script' => 'album.php?act=comments',     // Имя скрипта (с параметрами вызова)
        'sub_id_name' => 'img',                   // Имя идентификатора комментируемого объекта
        'sub_id' => $img,                         // Идентификатор комментируемого объекта
        'owner' => $res['user_id'],               // Владелец объекта
        'owner_delete' => true,                   // Возможность владельцу удалять комментарий
        'owner_reply' => true,                    // Возможность владельцу отвечать на комментарий
        'owner_edit' => false,                    // Возможность владельцу редактировать комментарий
        'title' => $lng['comments'],              // Название раздела
        'context_top' => 'top',                   // Выводится вверху списка
        'context_bottom' => ''                    // Выводится внизу списка
    );

    /*
    -----------------------------------------------------------------
    Показываем комментарии
    -----------------------------------------------------------------
    */
    $comm = new comments($arg);
} else {
    echo functions::display_error($lng['error_wrong_data']);
}
?>