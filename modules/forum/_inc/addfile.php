<?php

/**
 * @package     JohnCMS
 * @link        http://johncms.com
 * @copyright   Copyright (C) 2008-2012 JohnCMS Community
 * @license     LICENSE.txt (see attached file)
 * @version     VERSION.txt (see attached file)
 * @author      http://johncms.com/about
 */

defined('_IN_JOHNCMS') or die('Error: restricted access');
$url = Router::getUri(2);

if (!Vars::$ID || !Vars::$USER_ID) {
    echo Functions::displayError(__('error_wrong_data'));
    exit;
}
// Проверяем, тот ли юзер заливает файл
$req = DB::PDO()->query("SELECT * FROM `forum` WHERE `id` = " . Vars::$ID);
$res = $req->fetch();
if ($res['user_id'] != Vars::$USER_ID) {
    echo Functions::displayError(__('error_wrong_data'));
    exit;
}

$req1 = DB::PDO()->query("SELECT COUNT(*) FROM `cms_forum_files` WHERE `post` = " . Vars::$ID);
if ($req1->fetchColumn() > 0) {
    echo Functions::displayError(__('error_file_uploaded'));
    exit;
}

// Вычисляем страницу для перехода
$page = ceil(DB::PDO()->query("SELECT COUNT(*) FROM `forum` WHERE `refid` = '" . $res['refid'] . "' AND `id` " . ($set_forum['upfp'] ? ">=" : "<=") . " '" . $res['id'] . "'")->fetchColumn() / Vars::$USER_SET['page_size']);

switch ($res['type']) {
    case 'm':
        if (isset($_POST['submit'])) {
            /*
            -----------------------------------------------------------------
            Проверка, был ли выгружен файл и с какого браузера
            -----------------------------------------------------------------
            */
            $do_file = FALSE;
            if ($_FILES['fail']['size'] > 0) {
                // Проверка загрузки с обычного браузера
                $do_file = TRUE;
                $fname = strtolower($_FILES['fail']['name']);
                $fsize = $_FILES['fail']['size'];
            }
            /*
            -----------------------------------------------------------------
            Обработка файла (если есть), проверка на ошибки
            -----------------------------------------------------------------
            */
            if ($do_file) {
                // Список допустимых расширений файлов.
                $al_ext = array_merge($ext_win, $ext_java, $ext_sis, $ext_doc, $ext_pic, $ext_arch, $ext_video, $ext_audio, $ext_other);
                $ext = explode(".", $fname);
                $error = array();
                // Проверка на допустимый размер файла
                if ($fsize > 1024 * Vars::$SYSTEM_SET['filesize'])
                    $error[] = __('error_file_size') . ' ' . Vars::$SYSTEM_SET['filesize'] . 'kb.';
                // Проверка файла на наличие только одного расширения
                if (count($ext) != 2)
                    $error[] = __('error_file_name');
                // Проверка допустимых расширений файлов
                if (!in_array($ext[1], $al_ext))
                    $error[] = __('error_file_ext') . ':<br />' . implode(', ', $al_ext);
                // Проверка на длину имени
                if (strlen($fname) > 30)
                    $error[] = __('error_file_name_size');
                // Проверка на запрещенные символы
                if (preg_match("/[^\da-z_\-.]+/", $fname))
                    $error[] = __('error_file_symbols');
                // Проверка наличия файла с таким же именем
                if (file_exists(ROOTPATH . 'files' . DIRECTORY_SEPARATOR . 'forum' . DIRECTORY_SEPARATOR . $fname)) {
                    $fname = time() . $fname;
                }
                // Окончательная обработка
                if (!$error && $do_file) {
                    // Для обычного браузера
                    if ((move_uploaded_file($_FILES["fail"]["tmp_name"], ROOTPATH . 'files' . DIRECTORY_SEPARATOR . 'forum' . DIRECTORY_SEPARATOR . $fname)) == TRUE) {
                        @chmod("$fname", 0777);
                        @chmod(ROOTPATH . 'files' . DIRECTORY_SEPARATOR . 'forum' . DIRECTORY_SEPARATOR . $fname, 0777);
                        echo __('file_uploaded') . '<br/>';
                    } else {
                        $error[] = __('error_upload_error');
                    }
                }
                if (!$error) {
                    // Определяем тип файла
                    $ext = strtolower($ext[1]);
                    if (in_array($ext, $ext_win)) {
                        $type = 1;
                    } elseif (in_array($ext, $ext_java)) {
                        $type = 2;
                    } elseif (in_array($ext, $ext_sis)) {
                        $type = 3;
                    } elseif (in_array($ext, $ext_doc)) {
                        $type = 4;
                    } elseif (in_array($ext, $ext_pic)) {
                        $type = 5;
                    } elseif (in_array($ext, $ext_arch)) {
                        $type = 6;
                    } elseif (in_array($ext, $ext_video)) {
                        $type = 7;
                    } elseif (in_array($ext, $ext_audio)) {
                        $type = 8;
                    } else {
                        $type = 9;
                    }

                    // Определяем ID субкатегории и категории
                    $req2 = DB::PDO()->query("SELECT * FROM `forum` WHERE `id` = '" . $res['refid'] . "'");
                    $res2 = $req2->fetch();
                    $req3 = DB::PDO()->query("SELECT * FROM `forum` WHERE `id` = '" . $res2['refid'] . "'");
                    $res3 = $req3->fetch();

                    // Заносим данные в базу
                    $STH = DB::PDO()->prepare('
                        INSERT INTO `cms_forum_files`
                        (`cat`, `subcat`, `topic`, `post`, `time`, `filename`, `filetype`)
                        VALUES (?, ?, ?, ?, ?, ?, ?)
                    ');

                    $STH->execute(array(
                        $res3['refid'],
                        $res2['refid'],
                        $res['refid'],
                        Vars::$ID,
                        $res['time'],
                        $fname,
                        $type
                    ));
                    $STH = NULL;
                } else {
                    echo Functions::displayError($error, '<a href="' . $url . '?act=addfile&amp;id=' . Vars::$ID . '">' . __('repeat') . '</a>');
                }
            } else {
                echo __('error_upload_error') . '<br />';
            }
            echo '<br/><a href="' . $url . '?id=' . $res['refid'] . '&amp;page=' . $page . '">' . __('continue') . '</a><br/>';
        } else {
            /*
            -----------------------------------------------------------------
            Форма выбора файла для выгрузки
            -----------------------------------------------------------------
            */
            echo'<div class="phdr"><b>' . __('add_file') . '</b></div>' .
                '<div class="gmenu"><form action="' . $url . '?act=addfile&amp;id=' . Vars::$ID . '" method="post" enctype="multipart/form-data"><p>';
            if (stristr(Vars::$USER_AGENT, 'Opera/8.01')) {
                echo '<input name="fail1" value =""/>&#160;<br/><a href="op:fileselect">' . __('select_file') . '</a>';
            } else {
                echo '<input type="file" name="fail"/>';
            }
            echo'</p><p>' .
                '<input type="submit" name="submit" value="' . __('upload') . '"/>' .
                '</p><p><a href="' . $url . '?id=' . $res['refid'] . '&amp;page=' . $page . '">' . __('cancel') . '</a></p></form></div>' .
                '<div class="phdr">' . __('max_size') . ': ' . Vars::$SYSTEM_SET['filesize'] . 'kb.</div>';
        }
        break;

    default:
        echo Functions::displayError(__('error_wrong_data'));
}