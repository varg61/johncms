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

// Ограничиваем доступ к Библиотеке
$error = '';
if ((!isset(Vars::$ACL['library']) || !Vars::$ACL['library']) && Vars::$USER_RIGHTS < 7) {
    $error = __('library_closed');
} elseif (isset(Vars::$ACL['library']) && Vars::$ACL['library'] == 1 && !Vars::$USER_ID) {
    $error = __('access_guest_forbidden');
}
if ($error) {
    echo '<div class="rmenu"><p>' . $error . '</p></div>';
    exit;
}

// Заголовки библиотеки
if (Vars::$ID) {
    $req = DB::PDO()->query("SELECT * FROM `lib` WHERE `id`= " . Vars::$ID);
    $zag = $req->fetch();
    $hdr = $zag['type'] == 'bk' ? $zag['name'] : $zag['text'];
    $hdr = htmlentities(mb_substr($hdr, 0, 30), ENT_QUOTES, 'UTF-8');
    $textl = mb_strlen($zag['text']) > 30 ? $hdr . '...' : $hdr;
}

$actions = array(
    'addkomm' => 'addkomm.php',
    'del'     => 'del.php',
    'edit'    => 'edit.php',
    'java'    => 'java.php',
    'load'    => 'load.php',
    'mkcat'   => 'mkcat.php',
    'moder'   => 'moder.php',
    'new'     => 'new.php',
    'topread' => 'topread.php',
    'write'   => 'write.php',
);

if (isset($actions[Vars::$ACT])
    && is_file(MODPATH . Router::$PATH . DIRECTORY_SEPARATOR . '_inc' . DIRECTORY_SEPARATOR . $actions[Vars::$ACT])
) {
    require_once(MODPATH . Router::$PATH . DIRECTORY_SEPARATOR . '_inc' . DIRECTORY_SEPARATOR . $actions[Vars::$ACT]);
} else {
    if (!isset(Vars::$ACL['library']) || !Vars::$ACL['library']) {
        echo '<p><font color="#FF0000"><b>' . __('library_closed') . '</b></font></p>';
    }
    if (!Vars::$ID) {
        echo '<div class="phdr"><b>' . __('library') . '</b></div>';
        echo '<div class="topmenu"><a href="' . $url . '/search">' . __('search') . '</a></div>';
        if (Vars::$USER_RIGHTS == 5 || Vars::$USER_RIGHTS >= 6) {
            // Считаем число статей, ожидающих модерацию
            $moder = DB::PDO()->query("SELECT COUNT(*) FROM `lib` WHERE `type` = 'bk' AND `moder` = '0'")->fetchColumn();
            if ($moder) {
                echo '<div class="rmenu">' . __('on_moderation') . ': <a href="' . $url . '?act=moder">' . $moder . '</a></div>';
            }
        }
        // Считаем новое в библиотеке
        $new = DB::PDO()->query("SELECT COUNT(*) FROM `lib` WHERE `time` > '" . (time() - 259200) . "' AND `type`='bk' AND `moder`='1'")->fetchColumn();
        echo '<div class="gmenu"><p>';
        if ($new) {
            echo '<a href="' . $url . '?act=new">' . __('new_articles') . '</a> (' . $new . ')<br/>';
        }
        echo '<a href="' . $url . '?act=topread">' . __('most_readed') . '</a></p></div>';
        Vars::$ID = 0;
        $tip = "cat";
    } else {
        $tip = $zag['type'];
        if ($tip == "cat") {
            echo '<div class="phdr"><a href="' . $url . '"><b>' . __('library') . '</b></a> | ' . htmlentities($zag['text'], ENT_QUOTES, 'UTF-8') . '</div>';
        }
    }

    switch ($tip) {
        case 'cat':
            $totalcat = DB::PDO()->query("SELECT COUNT(*) FROM `lib` WHERE `type` = 'cat' AND `refid` = " . Vars::$ID)->fetchColumn();
            $totalbk = DB::PDO()->query("SELECT COUNT(*) FROM `lib` WHERE `type` = 'bk' AND `refid` = " . Vars::$ID . " AND `moder`='1'")->fetchColumn();
            if ($totalcat) {
                $total = $totalcat;
                if ($total > Vars::$USER_SET['page_size']) {
                    echo '<div class="topmenu">' . Functions::displayPagination($url . '?id=' . Vars::$ID . '&amp;', Vars::$START, $total, Vars::$USER_SET['page_size']) . '</div>';
                }
                $req = DB::PDO()->query("SELECT `id`, `text`  FROM `lib` WHERE `type` = 'cat' AND `refid` = " . Vars::$ID . " " . Vars::db_pagination());
                $i = 0;
                while ($cat1 = $req->fetch()) {
                    $totalcat2 = DB::PDO()->query("SELECT COUNT(*) FROM `lib` WHERE `type` = 'cat' AND `refid` = " . $cat1['id'])->fetchColumn();
                    $totalbk2 = DB::PDO()->query("SELECT COUNT(*) FROM `lib` WHERE `type` = 'bk' AND `refid` = '" . $cat1['id'] . "' AND `moder` = '1'")->fetchColumn();
                    if ($totalcat2 != 0) {
                        $kol = "$totalcat2 кат.";
                    } elseif ($totalbk2 != 0) {
                        $kol = "$totalbk2 ст.";
                    } else {
                        $kol = "0";
                    }
                    echo $i % 2 ? '<div class="list2">' : '<div class="list1">';
                    echo '<a href="' . $url . '?id=' . $cat1['id'] . '">' . $cat1['text'] . '</a>(' . $kol . ')</div>';
                    ++$i;
                }
                echo '<div class="phdr">' . __('total') . ': ' . $totalcat . '</div>';
            } elseif ($totalbk > 0) {
                $total = $totalbk;
                if ($total > Vars::$USER_SET['page_size']) echo '<div class="topmenu">' . Functions::displayPagination($url . '?id=' . Vars::$ID . '&amp;', Vars::$START, $total, Vars::$USER_SET['page_size']) . '</div>';
                $bk = DB::PDO()->query("SELECT * FROM `lib` WHERE `type` = 'bk' AND `refid` = '" . Vars::$ID . "' AND `moder` = '1' ORDER BY `id` DESC " . Vars::db_pagination());
                $i = 0;
                while ($bk1 = $bk->fetch()) {
                    echo $i % 2 ? '<div class="list2">' : '<div class="list1">';
                    echo '<b><a href="' . $url . '?id=' . $bk1['id'] . '">' . htmlentities($bk1['name'], ENT_QUOTES, 'UTF-8') . '</a></b><br/>';
                    echo Validate::checkout($bk1['announce']);
                    echo '<div class="sub"><span class="gray">' . __('added') . ':</span> ' . $bk1['avtor'] . ' (' . Functions::displayDate($bk1['time']) . ')<br />';
                    echo '<span class="gray">' . __('reads') . ':</span> ' . $bk1['count'] . '</div></div>';
                    ++$i;
                }
                echo '<div class="phdr">' . __('total') . ': ' . $totalbk . '</div>';
            } else {
                $total = 0;
            }
            // Навигация по страницам
            if ($total > Vars::$USER_SET['page_size']) {
                echo'<div class="topmenu">' . Functions::displayPagination($url . '?id=' . Vars::$ID . '&amp;', Vars::$START, $total, Vars::$USER_SET['page_size']) . '</div>' .
                    '<p><form action="' . $url . '?id=' . Vars::$ID . '" method="post">' .
                    '<input type="text" name="page" size="2"/>' .
                    '<input type="submit" value="' . __('to_page') . ' &gt;&gt;"/>' .
                    '</form></p>';
            }
            echo '<p>';
            if ((Vars::$USER_RIGHTS == 5 || Vars::$USER_RIGHTS >= 6) && Vars::$ID != 0) {
                $ct1 = DB::PDO()->query("SELECT COUNT(*) FROM `lib` WHERE `type` = 'cat' AND `refid` = " . Vars::$ID)->fetchColumn();
                if (!$ct1) {
                    echo '<a href="' . $url . '?act=del&amp;id=' . Vars::$ID . '">' . __('delete_category') . '</a><br/>';
                }
                echo '<a href="' . $url . '?act=edit&amp;id=' . Vars::$ID . '">' . __('edit_category') . '</a><br/>';
            }
            if ((Vars::$USER_RIGHTS == 5 || Vars::$USER_RIGHTS >= 6) && (isset($zag['ip']) && $zag['ip'] == 1 || Vars::$ID == 0)) {
                echo '<a href="' . $url . '?act=mkcat&amp;id=' . Vars::$ID . '">' . __('create_category') . '</a><br/>';
            }
            if (isset($zag['ip']) && $zag['ip'] == 0 && Vars::$ID != 0) {
                if ((Vars::$USER_RIGHTS == 5 || Vars::$USER_RIGHTS >= 6) || ($zag['soft'] == 1 && !empty($_SESSION['uid']))) {
                    echo '<a href="' . $url . '?act=write&amp;id=' . Vars::$ID . '">' . __('write_article') . '</a><br/>';
                }
                if (Vars::$USER_RIGHTS == 5 || Vars::$USER_RIGHTS >= 6) {
                    echo '<a href="' . $url . '?act=load&amp;id=' . Vars::$ID . '">' . __('upload_article') . '</a><br/>';
                }
            }
            if (Vars::$ID) {
                $dnam1 = DB::PDO()->query("select `id`, `refid`, `text` from `lib` where type = 'cat' and id = '" . Vars::$ID . "'")->fetch();
                $dnam3 = DB::PDO()->query("select `id`, `refid`, `text` from `lib` where type = 'cat' and id = '" . $dnam1['refid'] . "'")->fetch();
                $catname = $dnam3['text'];
                $dirid = $dnam1['id'];

                $nadir = $dnam1['refid'];
                while ($nadir != "0") {
                    echo '&#187;<a href="' . $url . '?id=' . $nadir . '">' . $catname . '</a><br/>';
                    $dnamm1 = DB::PDO()->query("select `id`, `refid`, `text` from `lib` where type = 'cat' and id = '" . $nadir . "'")->fetch();
                    $dnamm3 = DB::PDO()->query("select `id`, `refid`, `text` from `lib` where type = 'cat' and id = '" . $dnamm1['refid'] . "'")->fetch();
                    $nadir = $dnamm1['refid'];
                    $catname = $dnamm3['text'];
                }
                echo '<a href="' . $url . '">' . __('to_library') . '</a><br/>';
            }
            echo '</p>';
            break;

        case 'bk':
            /*
            -----------------------------------------------------------------
            Читаем статью
            -----------------------------------------------------------------
            */
            if (!empty($_SESSION['symb'])) {
                $simvol = $_SESSION['symb'];
            } else {
                $simvol = 2000; // Число символов на страницу по умолчанию
            }
            // Счетчик прочтений
            if (!isset($_SESSION['lib']) || isset($_SESSION['lib']) && $_SESSION['lib'] != Vars::$ID) {
                $_SESSION['lib'] = Vars::$ID;
                $libcount = intval($zag['count']) + 1;
                DB::PDO()->exec("UPDATE `lib` SET  `count` = '$libcount' WHERE `id` = " . Vars::$ID);
            }
            // Запрашиваем выбранную статью из базы
            $symbols = Vars::$IS_MOBILE ? 3000 : 7000;
            $req = DB::PDO()->query("SELECT CHAR_LENGTH(`text`) / $symbols AS `count_pages` FROM `lib` WHERE `id`= " . Vars::$ID)->fetch();
            $count_pages = ceil($req['count_pages']);
            $start_pos = Vars::$PAGE == 1 ? 1 : Vars::$PAGE * $symbols - $symbols;
            $req = DB::PDO()->query("SELECT SUBSTRING(`text`, $start_pos, " . ($symbols + 100) . ") AS `text` FROM `lib` WHERE `id` = " . Vars::$ID)->fetch();
            if (Vars::$PAGE == 1) {
                $int_start = 0;
            } else {
                if (FALSE === ($pos1 = mb_strpos($req['text'], "\r\n"))) $pos1 = 100;
                if (FALSE === ($pos2 = mb_strpos($req['text'], ' '))) $pos2 = 100;
                $int_start = $pos1 >= $pos2 ? $pos2 : $pos1;
                Vars::$START = Vars::$PAGE - 1;
            }
            if ($count_pages == 1 || Vars::$PAGE == $count_pages) {
                $int_lenght = $symbols;
            } else {
                $tmp = mb_substr($req['text'], $symbols, 100);
                if (($pos1 = mb_strpos($tmp, "\r\n")) === FALSE) $pos1 = 100;
                if (($pos2 = mb_strpos($tmp, ' ')) === FALSE) $pos2 = 100;
                $int_lenght = $symbols + ($pos1 >= $pos2 ? $pos2 : $pos1) - $int_start;
            }

            // Заголовок статьи
            echo '<div class="phdr"><b>' . htmlentities($zag['name'], ENT_QUOTES, 'UTF-8') . '</b></div>';
            if ($count_pages > 1) {
                echo '<div class="topmenu">' . Functions::displayPagination($url . '?id=' . Vars::$ID . '&amp;', Vars::$START, $count_pages, 1) . '</div>';
            }
            // Текст статьи
            $text = Validate::checkout(mb_substr($req['text'], $int_start, $int_lenght), 1, 1);
            if (Vars::$USER_SET['smilies'])
                $text = Functions::smilies($text, Vars::$USER_RIGHTS ? 1 : 0);
            echo '<div class="list2">' . $text . '</div>';

            // Ссылка на комментарии
            if (Vars::$SYSTEM_SET['mod_lib_comm'] || Vars::$USER_RIGHTS >= 7) {
                $km1 = DB::PDO()->query("SELECT COUNT(*) FROM `lib` WHERE `type` = 'komm' AND `refid` = " . Vars::$ID)->fetchColumn();
                $comm_link = '<a href="' . $url . '?act=komm&amp;id=' . Vars::$ID . '">' . __('comments') . '</a> (' . $km1 . ')';
            } else {
                $comm_link = '&#160;';
            }
            echo '<div class="phdr">' . $comm_link . '</div>';
            if ($count_pages > 1) {
                echo '<div class="topmenu">' .
                    Functions::displayPagination($url . '?id=' . Vars::$ID . '&amp;', Vars::$START, $count_pages, 1) .
                    '</div><div class="topmenu">' .
                    '<form action="' . $url . '?id=' . Vars::$ID . '" method="post">' .
                    '<input type="text" name="page" size="2"/>' .
                    '<input type="submit" value="' . __('to_page') . ' &gt;&gt;"/>' .
                    '</form></div>';
            }
            if (Vars::$USER_RIGHTS == 5 || Vars::$USER_RIGHTS >= 6) {
                echo '<p><a href="' . $url . '?act=edit&amp;id=' . Vars::$ID . '">' . __('edit') . '</a><br/>';
                echo '<a href="' . $url . '?act=del&amp;id=' . Vars::$ID . '">' . __('delete') . '</a></p>';
            }
            echo '<a href="' . $url . '?act=java&amp;id=' . Vars::$ID . '">' . __('download_java') . '</a><br /><br />';
            $dnam1 = DB::PDO()->query("select `id`, `refid`, `text` from `lib` where type = 'cat' and id = '" . $zag['refid'] . "'")->fetch();
            $catname = $dnam1['text'];
            $dirid = $dnam1['id'];
            $nadir = $zag['refid'];
            while ($nadir != "0") {
                echo '&#187;<a href="' . $url . '?id=' . $nadir . '">' . $catname . '</a><br/>';
                $dnamm1 = DB::PDO()->query("select `id`, `refid`, `text` from `lib` where type = 'cat' and id = '" . $nadir . "'")->fetch();
                $dnamm3 = DB::PDO()->query("select `id`, `refid`, `text` from `lib` where type = 'cat' and id = '" . $dnamm1['refid'] . "'")->fetch();
                $nadir = $dnamm1['refid'];
                $catname = $dnamm3['text'];
            }
            echo '<a href="' . $url . '">' . __('to_library') . '</a>';
            break;

        default :
            header('location: ' . Vars::$HOME_URL);
    }
}