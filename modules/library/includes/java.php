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
    if (!file_exists('../files/library/' . Vars::$ID . '.jar')) {
        $midlet_name = mb_substr($res['name'], 0, 10);
        $midlet_name = iconv('UTF-8', 'windows-1251', $midlet_name);
        // Записываем текст статьи
        $files = fopen("java/textfile.txt", 'w+');
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
        $files = fopen("java/META-INF/MANIFEST.MF", 'w+');
        flock($files, LOCK_EX);
        fputs($files, $manifest_text);
        flock($files, LOCK_UN);
        fclose($files);

        // Создаем архив
        require_once('../includes/lib/pclzip.lib.php');
        $archive = new PclZip('../files/library/' . Vars::$ID . '.jar');
        $list = $archive->create('java', PCLZIP_OPT_REMOVE_PATH, 'java');
        if (!file_exists('../files/library/' . Vars::$ID . '.jar')) {
            echo '<p>Error creating JAR file</p>';
            exit;
        }
    }

    // Создаем JAD файл
    if (!file_exists('../files/library/' . Vars::$ID . '.jad')) {
        $filesize = filesize('../files/library/' . Vars::$ID . '.jar');
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
MIDlet-Jar-URL: ' . Vars::$HOME_URL . '/library/files/' . Vars::$ID . '.jar';
        $files = fopen('../files/library/' . Vars::$ID . '.jad', 'w+');
        flock($files, LOCK_EX);
        fputs($files, $jad_text);
        flock($files, LOCK_UN);
        fclose($files);
    }
    echo $lng_lib['download_java_help'] . '<br /><br />' .
         Vars::$LNG['title'] . ': ' . $res['name'] . '<br />' .
         Vars::$LNG['download'] . ': <a href="../files/library/' . Vars::$ID . '.jar">JAR</a> | <a href="../files/library/' . Vars::$ID . '.jad">JAD</a>' .
         '<p><a href="index.php?id=' . Vars::$ID . '">' . Vars::$LNG['to_article'] . '</a></p>';
} else {
    echo '<p>' . Vars::$LNG['access_guest_forbidden'] . '</p>' .
         '<p><a href="index.php?id=' . Vars::$ID . '">' . Vars::$LNG['back'] . '</a></p>';
}