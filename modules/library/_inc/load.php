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

if (Vars::$USER_RIGHTS == 5 || Vars::$USER_RIGHTS >= 6) {
    if ($_GET['id'] == "") {
        echo "";
        exit;
    }
    $ms = DB::PDO()->query("select * from `lib` where `id` = " . Vars::$ID)->fetch();
    if (Vars::$ID != 0 && $ms['type'] != "cat") {
        echo "";
        exit;
    }
    if ($ms['ip'] == 0) {
        if (isset($_POST['submit'])) {
            if (empty($_POST['name'])) {
                echo Functions::displayError(__('error_empty_title'), '<a href="' . $url . '?act=load&amp;id=' . Vars::$ID . '">' . __('repeat') . '</a>');
                exit;
            }
            $name = mb_substr($_POST['name'], 0, 50);
            $fname = $_FILES['fail']['name'];
            $ftip = Functions::format($fname);
            $ftip = strtolower($ftip);
            if ($fname != "") {
                if (eregi("[^a-z0-9.()+_-]", $fname)) {
                    echo "Invalid file name<br /><a href='" . $url . "?act=load&amp;id=" . Vars::$ID . "'>" . __('repeat') . "</a><br/>";
                    exit;
                }
                if ((preg_match("/.php/i", $fname)) or (preg_match("/.pl/i", $fname)) or ($fname == ".htaccess")) {
                    echo "Invalid file format<br/><a href='" . $url . "?act=load&amp;id=" . Vars::$ID . "'>" . __('repeat') . "</a><br/>";
                    exit;
                }
                if ($ftip != "txt") {
                    echo "This is not a text file<br/><a href='" . $url . "?act=load&amp;id=" . Vars::$ID . "'>" . __('repeat') . "</a><br/>";
                    exit;
                }
                if ((move_uploaded_file($_FILES["fail"]["tmp_name"], "temp/$fname")) == TRUE) {
                    $ch = $fname;
                    @chmod("$ch", 0777);
                    @chmod("temp/$ch", 0777);
                    $txt = file_get_contents("temp/$ch");
                    if (mb_check_encoding($txt, 'UTF-8')) {
                    } elseif (mb_check_encoding($txt, 'windows-1251')) {
                        $txt = iconv("windows-1251", "UTF-8", $txt);
                    } elseif (mb_check_encoding($txt, 'KOI8-R')) {
                        $txt = iconv("KOI8-R", "UTF-8", $txt);
                    } else {
                        echo "File in an unknown encoding<br /><a href='" . $url . "?act=load&amp;id=" . Vars::$ID . "'>" . __('repeat') . "</a><br/>";
                        exit;
                    }
                    $anons = !empty($_POST['anons']) ? mb_substr($_POST['anons'], 0, 100) : mb_substr($txt, 0, 100);

                    $STH = $STH = DB::PDO()->prepare('
                    INSERT INTO `lib` SET
                        `refid` = :refid,
                        `time` = :time,
                        `type` = "bk",
                        `name` = :name,
                        `announce` = :announce,
                        `avtor` = :avtor,
                        `text` = :text,
                        `ip` = :ip,
                        `soft` = :soft,
                        `moder` = 1
                    ');

                    $STH->bindValue(':refid', Vars::$ID);
                    $STH->bindValue(':time', time());
                    $STH->bindValue(':name', $name);
                    $STH->bindValue(':announce', $anons);
                    $STH->bindValue(':avtor', Vars::$USER_NICKNAME);
                    $STH->bindValue(':text', $txt);
                    $STH->bindValue(':ip', Vars::$IP);
                    $STH->bindValue(':soft', Vars::$USER_AGENT);
                    $STH->execute();
                    $cid = DB::PDO()->lastInsertId();
                    $STH = NULL;

                    unlink("temp/$ch");
                    echo __('article_added') . "<br/><a href='" . $url . "?id=" . $cid . "'>" . __('to_article') . "</a><br/>";
                } else {
                    echo __('error_uploading') . "<br/><a href='" . $url . "?act=load&amp;id=" . Vars::$ID . "'>" . __('repeat') . "</a><br/>";
                    exit;
                }
            }
        } else {
            echo '<h3>' . __('upload_article') . '</h3>' . __('supported_encoding') . ' Win-1251, KOI8-R, UTF-8<br/><br/>' .
                '<form action="' . $url . '?act=load&amp;id=' . Vars::$ID . '" method="post" enctype="multipart/form-data">' .
                __('title') . ' (max. 50)<br/>' . '<input type="text" name="name"/><br/>' .
                __('announce') . ' (max. 100)<br/><input type="text" name="anons"/><br/>' .
                __('select_text_file') . ' ( .txt):<br/><input type="file" name="fail"/>' .
                '<p><input type="submit" name="submit" value="' . __('sent') . '"/></p>' .
                '</form>' .
                '<p><a href ="' . $url . '?id=' . Vars::$ID . '">' . __('back') . '</a></p>';
        }
    }
} else {
    header("location: " . $url);
}