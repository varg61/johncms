<?php

/**
 * @package     JohnCMS
 * @link        http://johncms.com
 * @copyright   Copyright (C) 2008-2012 JohnCMS Community
 * @license     LICENSE.txt (see attached file)
 * @version     VERSION.txt (see attached file)
 * @author      http://johncms.com/about
 */

$filesroot = ROOTPATH . 'files' . DIRECTORY_SEPARATOR . 'download' . DIRECTORY_SEPARATOR;
$screenroot = $filesroot . 'screen';
$loadroot = $filesroot . 'files';

// Ограничиваем доступ к Загрузкам
$error = '';
if (!Vars::$SYSTEM_SET['mod_down'] && Vars::$USER_RIGHTS < 7)
    $error = lng('downloads_closed');
elseif (Vars::$SYSTEM_SET['mod_down'] == 1 && !Vars::$USER_ID)
    $error = lng('access_guest_forbidden');
if ($error) {
    echo '<div class="rmenu"><p>' . $error . '</p></div>';
    exit;
}

function provcat($catalog)
{
    $cat1 = mysql_query("select * from `download` where type = 'cat' and id = '" . $catalog . "';");
    $cat2 = mysql_num_rows($cat1);
    $adrdir = mysql_fetch_array($cat1);
    if (($cat2 == 0) || (!is_dir("$adrdir[adres]/$adrdir[name]"))) {
        echo 'ERROR<br/><a href="?">Back</a><br/>';
        exit;
    }
}

$actions = array(
    'scan_dir'  => 'scan_dir.php',
    'rat'       => 'rat.php',
    'delmes'    => 'delmes.php',
    'search'    => 'search.php',
    'addkomm'   => 'addkomm.php',
    'komm'      => 'komm.php',
    'new'       => 'new.php',
    'zip'       => 'zip.php',
    'arc'       => 'arc.php',
    'down'      => 'down.php',
    'dfile'     => 'dfile.php',
    'opis'      => 'opis.php',
    'screen'    => 'screen.php',
    'ren'       => 'ren.php',
    'import'    => 'import.php',
    'refresh'   => 'refresh.php',
    'upl'       => 'upl.php',
    'view'      => 'view.php',
    'makdir'    => 'makdir.php',
    'select'    => 'select.php',
    'preview'   => 'preview.php',
    'delcat'    => 'delcat.php',
    'mp3'       => 'mp3.php',
);

if (isset($actions[Vars::$ACT]) && is_file(MODPATH . Vars::$MODULE . DIRECTORY_SEPARATOR . '_inc' . DIRECTORY_SEPARATOR . $actions[Vars::$ACT])) {
    require_once(MODPATH . Vars::$MODULE . DIRECTORY_SEPARATOR . '_inc' . DIRECTORY_SEPARATOR . $actions[Vars::$ACT]);
} else {
    if (!Vars::$SYSTEM_SET['mod_down'])
        echo '<p><font color="#FF0000"><b>' . lng('downloads_closed') . '</b></font></p>';
    // Ссылка на новые файлы
    echo '<p><a href="?act=new">' . lng('new_files') . '</a> (' . mysql_result(mysql_query("SELECT COUNT(*) FROM `download` WHERE `time` > '" . (time() - 259200) . "' AND `type` = 'file'"), 0) . ')</p>';
    $cat = isset($_GET['cat']) ? intval($_GET['cat']) : '';
    if (empty($_GET['cat'])) {
        // Заголовок начальной страницы загрузок
        echo '<div class="phdr"><b>' . lng('downloads', 1) . '</b></div>';
    } else {
        // Заголовок страниц категорий
        $req = mysql_query("SELECT * FROM `download` WHERE `type` = 'cat' AND `id` = '" . $cat . "' LIMIT 1");
        $res = mysql_fetch_array($req);
        if (mysql_num_rows($req) == 0 || !is_dir($res['adres'] . DIRECTORY_SEPARATOR . $res['name'])) {
            // Если неправильно выбран каталог, выводим ошибку
            echo Functions::displayError(lng('folder_does_not_exist'), '<a href="' . Vars::$URI . '">' . lng('back') . '</a>');
            exit;
        }
        ////////////////////////////////////////////////////////////
        // Получаем структуру каталогов                           //
        ////////////////////////////////////////////////////////////
        $tree = array();
        $dirid = $cat;
        while ($dirid != '0' && $dirid != "") {
            $req = mysql_query("SELECT * FROM `download` WHERE `type` = 'cat' and `id` = '" . $dirid . "' LIMIT 1");
            $res = mysql_fetch_array($req);
            $tree[] = '<a href="' . Vars::$URI . '?cat=' . $dirid . '">' . $res['text'] . '</a>';
            $dirid = $res['refid'];
        }
        krsort($tree);
        $cdir = array_pop($tree);
        echo '<div class="phdr"><a href="' . Vars::$URI . '"><b>' . lng('downloads', 1) . '</b></a> | ';
        foreach ($tree as $value) {
            echo $value . ' | ';
        }
        echo strip_tags($cdir) . '</div>';
    }
    // Подсчитываем число папок
    $totalcat = mysql_result(mysql_query("SELECT COUNT(*) FROM `download` WHERE `refid` = '$cat' AND `type` = 'cat'"), 0);
    // Подсчитываем число файлов
    $totalfile = mysql_result(mysql_query("SELECT COUNT(*) FROM `download` WHERE `refid` = '$cat' AND `type` = 'file'"), 0);
    $total = (int)$totalcat + $totalfile;
    if ($total > 0) {
        $zap = mysql_query("SELECT * FROM `download` WHERE `refid` = '$cat' ORDER BY `type` ASC, `text` ASC, `name` ASC " . Vars::db_pagination());
        while ($zap2 = mysql_fetch_array($zap)) {
            ////////////////////////////////////////////////////////////
            // Выводим список папок                                   //
            ////////////////////////////////////////////////////////////
            if ($totalcat > 0 && $zap2['type'] == 'cat') {
                echo '<div class="list1">';
                echo '<a href="?cat=' . $zap2['id'] . '">' . $zap2['text'] . '</a>';
                $g1 = 0;
                // Считаем число файлов в подкаталогах
                $req = mysql_query("SELECT COUNT(*) FROM `download` WHERE `type` = 'file' AND `adres` LIKE '" . ($zap2['adres'] . '/' . $zap2['name']) . "%'");
                $g = mysql_result($req, 0);
                // Считаем новые файлы в подкаталогах
                $req = mysql_query("SELECT COUNT(*) FROM `download` WHERE `type` = 'file' AND `adres` LIKE '" . ($zap2['adres'] . '/' . $zap2['name']) . "%' AND `time` > '" . (time() - 259200) . "'");
                $g1 = mysql_result($req, 0);
                echo "($g";
                if ($g1 != 0) {
                    echo "/+$g1)</div>";
                } else {
                    echo ")</div>";
                }
            }
            ////////////////////////////////////////////////////////////
            // Выводим cписок файлов                                  //
            ////////////////////////////////////////////////////////////
            if ($totalfile > 0 && $zap2['type'] == 'file') {
                echo '<div class="list2">';
                $ft = Functions::format($zap2['name']);
                switch ($ft) {
                    case "mp3":
                        $imt = "download_mp3.png";
                        break;

                    case "zip":
                        $imt = "download_rar.png";
                        break;

                    case "jar":
                        $imt = "download_jar.png";
                        break;

                    case "gif":
                        $imt = "download_gif.png";
                        break;

                    case "jpg":
                        $imt = "download_jpg.png";
                        break;

                    case "png":
                        $imt = "download_png.png";
                        break;
                    default :
                        $imt = "download_file.gif";
                        break;
                }
                echo Functions::getImage($imt) . '<a href="?act=view&amp;file=' . $zap2['id'] . '">' . htmlentities($zap2['name'], ENT_QUOTES, 'UTF-8') . '</a>';
                if ($zap2['text'] != "") {
                    // Выводим анонс текстового описания (если есть)
                    $tx = $zap2['text'];
                    if (mb_strlen($tx) > 100) {
                        $tx = mb_substr(strip_tags($tx), 0, 90);
                        $tx .= '...';
                    }
                    echo '<div class="sub">' . Validate::filterString($tx) . '</div>';
                }
                echo '</div>';
            }
        }
    } else {
        echo '<div class="menu"><p>' . lng('list_empty') . '</p></div>';
    }
    echo '<div class="phdr">';
    if ($totalcat > 0)
        echo lng('folders') . ': ' . $totalcat;
    echo '&#160;&#160;';
    if ($totalfile > 0)
        echo lng('files') . ': ' . $totalfile;
    echo '</div>';
    // Постраничная навигация
    if ($total > Vars::$USER_SET['page_size']) {
        echo '<p>' . Functions::displayPagination(Vars::$URI . '?cat=' . $cat . '&amp;', Vars::$START, $total, Vars::$USER_SET['page_size']) . '</p>';
    }
    if (Vars::$USER_RIGHTS == 4 || Vars::$USER_RIGHTS >= 6) {
        ////////////////////////////////////////////////////////////
        // Выводим ссылки на модерские функции                    //
        ////////////////////////////////////////////////////////////
        echo '<p><div class="func">';
        echo '<a href="?act=makdir&amp;cat=' . $cat . '">' . lng('make_folder') . '</a><br/>';
        if (!empty($_GET['cat'])) {
            $delcat = mysql_query("select * from `download` where type = 'cat' and refid = '" . $cat . "';");
            $delcat1 = mysql_num_rows($delcat);
            if ($delcat1 == 0) {
                echo '<a href="' . Vars::$URI . '?act=delcat&amp;cat=' . $cat . '">' . lng('delete_folder') . '</a><br />';
            }
            echo '<a href="' . Vars::$URI . '?act=ren&amp;cat=' . $cat . '">' . lng('rename_folder') . '</a><br />';
            echo '<a href="' . Vars::$URI . '?act=select&amp;cat=' . $cat . '">' . lng('upload_file') . '</a><br />';
            echo '<a href="' . Vars::$URI . '?act=import&amp;cat=' . $cat . '">' . lng('import_file') . '</a><br />';
        }
        echo '<a href="' . Vars::$URI . '?act=refresh">' . lng('refresh_downloads') . '</a>';
        echo '</div></p>';
    }
    if (!empty($cat))
        echo '<p><a href="' . Vars::$URI . '">' . lng('downloads') . '</a></p>';
    echo '<p><a href="' . Vars::$URI . '?act=preview">' . lng('images_size') . '</a></p>';
    if (empty($cat)) {
        echo '<form action="' . Vars::$URI . '?act=search" method="post">';
        echo lng('search_file') . ': <br/><input type="text" name="srh" size="20" maxlength="20" value=""/><br/>';
        echo '<input type="submit" value="' . lng('search') . '"/></form>';
    }
}