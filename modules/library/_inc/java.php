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

if (Vars::$USER_ID) {
    $req = mysql_query("select `name`, `text` from `lib` where `id` = " . Vars::$ID . " and `type` = 'bk' and `moder`='1' LIMIT 1;");
    if (mysql_num_rows($req) == 0) {
        echo '<p>ERROR</p>';
        exit;
    }
    $res = mysql_fetch_array($req);
    // Создаем JAR файл
    if (!file_exists(FILEPATH . 'library' . DIRECTORY_SEPARATOR . Vars::$ID . '.jar')) {
        $midlet_name = mb_substr($res['name'], 0, 10);
        $midlet_name = iconv('UTF-8', 'windows-1251', $midlet_name);
        // Записываем текст статьи
        $files = fopen(MODPATH . Vars::$MODULE . DIRECTORY_SEPARATOR . '_java' . DIRECTORY_SEPARATOR . 'textfile.txt', 'w+') or exit('ERROR: textfile.txt');
        flock($files, LOCK_EX);
        $book_name = iconv('UTF-8', 'windows-1251', $res['name']);
        $book_text = iconv('UTF-8', 'windows-1251', $res['text']);
        $result = "\r\n" . $book_name . "\r\n\r\n----------\r\n\r\n" . $book_text . "\r\n\r\nDownloaded from " . Vars::$HOME_URL;
        fputs($files, $result);
        flock($files, LOCK_UN);
        fclose($files);
        // Записываем манифест
        $manifest_text = 'Manifest-Version: 1.0
MIDlet-1: Book ' . Vars::$ID . ', , br.BookReader
MIDlet-Name: Book ' . Vars::$ID .
                         '
MIDlet-Vendor: MobiCMS
MIDlet-Version: 1.5.3
MIDletX-No-Command: true
MIDletX-LG-Contents: true
MicroEdition-Configuration: CLDC-1.0
MicroEdition-Profile: MIDP-1.0
TCBR-Platform: Generic version (all phones)';
        $files = fopen(MODPATH . Vars::$MODULE . DIRECTORY_SEPARATOR . '_java' . DIRECTORY_SEPARATOR . 'META-INF' . DIRECTORY_SEPARATOR . 'MANIFEST.MF', 'w+') or exit('ERROR: MANIFEST.MF');
        flock($files, LOCK_EX);
        fputs($files, $manifest_text);
        flock($files, LOCK_UN);
        fclose($files);

        // Создаем архив
        $archive = new PclZip(FILEPATH . 'library' . DIRECTORY_SEPARATOR . Vars::$ID . '.jar');
        $list = $archive->create(MODPATH . Vars::$MODULE . DIRECTORY_SEPARATOR . '_java', PCLZIP_OPT_REMOVE_PATH, MODPATH . Vars::$MODULE . DIRECTORY_SEPARATOR . '_java');
        if (!file_exists(FILEPATH . 'library' . DIRECTORY_SEPARATOR . Vars::$ID . '.jar')) {
            echo '<p>Error creating JAR file</p>';
            exit;
        }
    }

    // Создаем JAD файл
    if (!file_exists(FILEPATH . 'library' . DIRECTORY_SEPARATOR . Vars::$ID . '.jad')) {
        $filesize = filesize(FILEPATH . 'library' . DIRECTORY_SEPARATOR . Vars::$ID . '.jar');
        $jad_text = 'Manifest-Version: 1.0
MIDlet-1: Book ' . Vars::$ID . ', , br.BookReader
MIDlet-Name: Book ' . Vars::$ID .
                    '
MIDlet-Vendor: MobiCMS
MIDlet-Version: 1.5.3
MIDletX-No-Command: true
MIDletX-LG-Contents: true
MicroEdition-Configuration: CLDC-1.0
MicroEdition-Profile: MIDP-1.0
TCBR-Platform: Generic version (all phones)
MIDlet-Jar-Size: ' . $filesize . '
MIDlet-Jar-URL: ' . Vars::$HOME_URL . '/files/library/' . Vars::$ID . '.jar';
        $files = fopen(FILEPATH . 'library' . DIRECTORY_SEPARATOR . Vars::$ID . '.jad', 'w+');
        flock($files, LOCK_EX);
        fputs($files, $jad_text);
        flock($files, LOCK_UN);
        fclose($files);
    }
    echo lng('download_java_help') . '<br /><br />' .
         lng('title') . ': ' . Validate::filterString($res['name']) . '<br />' .
         lng('download') . ': <a href="' . Vars::$HOME_URL . '/files/library/' . Vars::$ID . '.jar">JAR</a> | <a href="' . Vars::$HOME_URL . '/files/library/' . Vars::$ID . '.jad">JAD</a>' .
         '<p><a href="' . Vars::$URI . '?id=' . Vars::$ID . '">' . lng('to_article') . '</a></p>';
} else {
    echo '<p>' . lng('access_guest_forbidden') . '</p>' .
         '<p><a href="' . Vars::$URI . '?id=' . Vars::$ID . '">' . lng('back') . '</a></p>';
}