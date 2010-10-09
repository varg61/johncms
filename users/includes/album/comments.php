<?php

require('../incfiles/comments_class.php');

/*
-----------------------------------------------------------------
Инициализируем класс комментариев
-----------------------------------------------------------------
Параметр 1 - Таблица MySQL с комментариями
Параметр 2 - Идентификатор модуля, использующего комментарии
-----------------------------------------------------------------
*/
$comm = new comments('cms_comments', 'album');
// Имя скрипта (с параметрами вызова), который использует комментарии
$comm->script = 'album.php?act=comments';
// Идентификатор комментируемого объекта
$comm->sub_id = $img;
// Имя идентификатора комментируемого объекта
$comm->sub_id_name = 'img';
// Таблица, в которой нужно проверять, существует ли объект
$comm->check_table = 'cms_album_files';

/*
-----------------------------------------------------------------
Показываем комментарии
-----------------------------------------------------------------
*/
$comm->display_comments();

?>