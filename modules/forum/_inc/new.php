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

unset($_SESSION['fsort_id']);
unset($_SESSION['fsort_users']);
if (empty($_SESSION['uid'])) {
    if (isset($_GET['newup'])) {
        $_SESSION['uppost'] = 1;
    }
    if (isset($_GET['newdown'])) {
        $_SESSION['uppost'] = 0;
    }
}
if ($user_id) {
    //TODO: Переделать с $do на $mod
    switch ($do) {
        case 'reset':
            /*
            -----------------------------------------------------------------
            Отмечаем все темы как прочитанные
            -----------------------------------------------------------------
            */
            $req = mysql_query("SELECT `forum`.`id`
            FROM `forum` LEFT JOIN `cms_forum_rdm` ON `forum`.`id` = `cms_forum_rdm`.`topic_id` AND `cms_forum_rdm`.`user_id` = " . Vars::$USER_ID . "
            WHERE `forum`.`type`='t'
            AND `cms_forum_rdm`.`topic_id` Is Null");
            while ($res = mysql_fetch_assoc($req)) {
                mysql_query("INSERT INTO `cms_forum_rdm` SET
                    `topic_id` = '" . $res['id'] . "',
                    `user_id` = " . Vars::$USER_ID . ",
                    `time` = '" . time() . "'
                ");
            }
            $req = mysql_query("SELECT `forum`.`id` AS `id`
            FROM `forum` LEFT JOIN `cms_forum_rdm` ON `forum`.`id` = `cms_forum_rdm`.`topic_id` AND `cms_forum_rdm`.`user_id` = " . Vars::$USER_ID . "
            WHERE `forum`.`type`='t'
            AND `forum`.`time` > `cms_forum_rdm`.`time`");
            while ($res = mysql_fetch_array($req)) {
                mysql_query("UPDATE `cms_forum_rdm` SET
                    `time` = '" . time() . "'
                    WHERE `topic_id` = '" . $res['id'] . "' AND `user_id` = " . Vars::$USER_ID
                );
            }
            echo '<div class="menu"><p>' . lng('unread_reset_done') . '<br /><a href="index.php">' . lng('to_forum') . '</a></p></div>';
            break;

        case 'select':
            /*
            -----------------------------------------------------------------
            Форма выбора диапазона времени
            -----------------------------------------------------------------
            */
            echo '<div class="phdr"><a href="index.php"><b>' . lng('forum') . '</b></a> | ' . lng('unread_show_for_period') . '</div>';
            echo '<div class="menu"><p><form action="index.php?act=new&amp;do=period" method="post">' . lng('unread_period') . ':<br/>';
            echo '<input type="text" maxlength="3" name="vr" value="24" size="3"/>';
            echo '<input type="submit" name="submit" value="' . lng('show') . '"/></form></p></div>';
            echo '<div class="phdr"><a href="index.php?act=new">' . lng('back') . '</a></div>';
            break;

        case 'period':
            /*
            -----------------------------------------------------------------
            Показ новых тем за выбранный период
            -----------------------------------------------------------------
            */
            $vr = isset($_REQUEST['vr']) ? abs(intval($_REQUEST['vr'])) : null;
            if (!$vr) {
                echo lng('error_time_empty') . '<br/><a href="index.php?act=new&amp;do=all">' . lng('repeat') . '</a><br/>';
                exit;
            }
            $vr1 = time() - $vr * 3600;
            if (Vars::$USER_RIGHTS == 9) {
                $req = mysql_query("SELECT COUNT(*) FROM `forum` WHERE `type`='t' AND `time` > '$vr1'");
            } else {
                $req = mysql_query("SELECT COUNT(*) FROM `forum` WHERE `type`='t' AND `time` > '$vr1' AND `close` != '1'");
            }
            $count = mysql_result($req, 0);
            echo '<div class="phdr"><a href="index.php"><b>' . lng('forum') . '</b></a> | ' . lng('unread_all_for_period') . ' ' . $vr . ' ' . lng('hours') . '</div>';
            if ($count > Vars::$USER_SET['page_size'])
                echo '<div class="topmenu">' . Functions::displayPagination('index.php?act=new&amp;do=period&amp;vr=' . $vr . '&amp;', Vars::$START, $count, Vars::$USER_SET['page_size']) . '</div>';
            if ($count > 0) {
                if (Vars::$USER_RIGHTS == 9) {
                    $req = mysql_query("SELECT * FROM `forum` WHERE `type`='t' AND `time` > '" . $vr1 . "' ORDER BY `time` DESC " . Vars::db_pagination());
                } else {
                    $req = mysql_query("SELECT * FROM `forum` WHERE `type`='t' AND `time` > '" . $vr1 . "' AND `close` != '1' ORDER BY `time` DESC " . Vars::db_pagination());
                }
                $i = 0;
                while ($res = mysql_fetch_array($req)) {
                    echo $i % 2 ? '<div class="list2">' : '<div class="list1">';
                    $q3 = mysql_query("SELECT `id`, `refid`, `text` FROM `forum` WHERE `type`='r' AND `id`='" . $res['refid'] . "'");
                    $razd = mysql_fetch_array($q3);
                    $q4 = mysql_query("SELECT `text` FROM `forum` WHERE `type`='f' AND `id`='" . $razd['refid'] . "'");
                    $frm = mysql_fetch_array($q4);
                    $colmes = mysql_query("SELECT * FROM `forum` WHERE `refid` = '" . $res['id'] . "' AND `type` = 'm'" . (Vars::$USER_RIGHTS >= 7 ? '' : " AND `close` != '1'") . " ORDER BY `time` DESC");
                    $colmes1 = mysql_num_rows($colmes);
                    $cpg = ceil($colmes1 / Vars::$USER_SET['page_size']);
                    $nick = mysql_fetch_array($colmes);
                    if ($res['edit'])
                        echo Functions::getImage('forum_closed.png');
                    elseif ($res['close'])
                        echo Functions::getImage('forum_deleted.png');
                    else
                        echo Functions::getImage('forum_new.png');
                    if ($res['realid'] == 1)
                        echo '&#160;' . Functions::getImage('rating.png');
                    echo '&#160;<a href="index.php?id=' . $res['id'] . ($cpg > 1 && $set_forum['upfp'] && $set_forum['postclip'] ? '&amp;clip' : '') . ($set_forum['upfp'] && $cpg > 1 ? '&amp;page=' . $cpg : '') . '">' . $res['text'] .
                         '</a>&#160;[' . $colmes1 . ']';
                    if ($cpg > 1)
                        echo '<a href="index.php?id=' . $res['id'] . (!$set_forum['upfp'] && $set_forum['postclip'] ? '&amp;clip' : '') . ($set_forum['upfp'] ? '' : '&amp;page=' . $cpg) . '">&#160;&gt;&gt;</a>';
                    echo '<br /><div class="sub"><a href="index.php?id=' . $razd['id'] . '">' . $frm['text'] . '&#160;/&#160;' . $razd['text'] . '</a><br />';
                    echo $res['from'];
                    if ($colmes1 > 1) {
                        echo '&#160;/&#160;' . $nick['from'];
                    }
                    echo ' <span class="gray">' . date("d.m.y / H:i", $nick['time']) . '</span>';
                    echo '</div></div>';
                    ++$i;
                }
            } else {
                echo '<div class="menu"><p>' . lng('unread_period_empty') . '</p></div>';
            }
            echo '<div class="phdr">' . lng('total') . ': ' . $count . '</div>';
            if ($count > Vars::$USER_SET['page_size']) {
                echo '<div class="topmenu">' . Functions::displayPagination('index.php?act=new&amp;do=period&amp;vr=' . $vr . '&amp;', Vars::$START, $count, Vars::$USER_SET['page_size']) . '</div>' .
                     '<p><form action="index.php?act=new&amp;do=period&amp;vr=' . $vr . '" method="post">
                    <input type="text" name="page" size="2"/>
                    <input type="submit" value="' . lng('to_page') . ' &gt;&gt;"/></form></p>';
            }
            echo '<p><a href="index.php?act=new">' . lng('back') . '</a></p>';
            break;

        default:
            /*
            -----------------------------------------------------------------
            Вывод непрочитанных тем (для зарегистрированных)
            -----------------------------------------------------------------
            */
            $total = Counters::forumCountNew();
            echo '<div class="phdr"><a href="index.php"><b>' . lng('forum') . '</b></a> | ' . lng('unread') . '</div>';
            if ($total > Vars::$USER_SET['page_size'])
                echo '<div class="topmenu">' . Functions::displayPagination('index.php?act=new&amp;', Vars::$START, $total, Vars::$USER_SET['page_size']) . '</div>';
            if ($total > 0) {
                $req = mysql_query("SELECT * FROM `forum`
                LEFT JOIN `cms_forum_rdm` ON `forum`.`id` = `cms_forum_rdm`.`topic_id` AND `cms_forum_rdm`.`user_id` = " . Vars::$USER_ID . "
                WHERE `forum`.`type`='t'" . (Vars::$USER_RIGHTS >= 7 ? "" : " AND `forum`.`close` != '1'") . "
                AND (`cms_forum_rdm`.`topic_id` Is Null
                OR `forum`.`time` > `cms_forum_rdm`.`time`)
                ORDER BY `forum`.`time` DESC
                " . Vars::db_pagination());
                $i = 0;
                while ($res = mysql_fetch_assoc($req)) {
                    if ($res['close'])
                        echo '<div class="rmenu">';
                    else
                        echo $i % 2 ? '<div class="list2">' : '<div class="list1">';
                    $q3 = mysql_query("SELECT `id`, `refid`, `text` FROM `forum` WHERE `type` = 'r' AND `id` = '" . $res['refid'] . "' LIMIT 1");
                    $razd = mysql_fetch_assoc($q3);
                    $q4 = mysql_query("SELECT `id`, `text` FROM `forum` WHERE `type`='f' AND `id` = '" . $razd['refid'] . "' LIMIT 1");
                    $frm = mysql_fetch_assoc($q4);
                    $colmes = mysql_query("SELECT `from`, `time` FROM `forum` WHERE `refid` = '" . $res['id'] . "' AND `type` = 'm'" . (Vars::$USER_RIGHTS >= 7 ? '' : " AND `close` != '1'") . " ORDER BY `time` DESC");
                    $colmes1 = mysql_num_rows($colmes);
                    $cpg = ceil($colmes1 / Vars::$USER_SET['page_size']);
                    $nick = mysql_fetch_assoc($colmes);
                    // Значки
                    $icons = array(
                        (isset($np) ? (!$res['vip'] ? Functions::getImage('forum_normal.png') : '') : Functions::getImage('forum_new.png')),
                        ($res['vip'] ? Functions::getImage('forum_pin.png') : ''),
                        ($res['realid'] ? Functions::getImage('rating.png') : ''),
                        ($res['edit'] ? Functions::getImage('forum_closed.png') : '')
                    );
                    echo Functions::displayMenu($icons, '&#160;', '&#160;');
                    echo '<a href="index.php?id=' . $res['id'] . ($cpg > 1 && $set_forum['upfp'] && $set_forum['postclip'] ? '&amp;clip' : '') . ($set_forum['upfp'] && $cpg > 1 ? '&amp;page=' . $cpg : '') . '">' . $res['text'] .
                         '</a>&#160;[' . $colmes1 . ']';
                    if ($cpg > 1)
                        echo '&#160;<a href="index.php?id=' . $res['id'] . (!$set_forum['upfp'] && $set_forum['postclip'] ? '&amp;clip' : '') . ($set_forum['upfp'] ? '' : '&amp;page=' . $cpg) . '">&gt;&gt;</a>';
                    echo '<div class="sub">' . $res['from'] . ($colmes1 > 1 ? '&#160;/&#160;' . $nick['from'] : '') .
                         ' <span class="gray">(' . Functions::displayDate($nick['time']) . ')</span><br />' .
                         '<a href="index.php?id=' . $frm['id'] . '">' . $frm['text'] . '</a>&#160;/&#160;<a href="index.php?id=' . $razd['id'] . '">' . $razd['text'] . '</a>' .
                         '</div></div>';
                    ++$i;
                }
            } else {
                echo '<div class="menu"><p>' . lng('list_empty') . '</p></div>';
            }
            echo '<div class="phdr">' . lng('total') . ': ' . $total . '</div>';
            if ($total > Vars::$USER_SET['page_size']) {
                echo '<div class="topmenu">' . Functions::displayPagination('index.php?act=new&amp;', Vars::$START, $total, Vars::$USER_SET['page_size']) . '</div>' .
                     '<p><form action="index.php" method="get">' .
                     '<input type="hidden" name="act" value="new"/>' .
                     '<input type="text" name="page" size="2"/>' .
                     '<input type="submit" value="' . lng('to_page') . ' &gt;&gt;"/>' .
                     '</form></p>';
            }
            echo '<p>';
            if ($total)
                echo '<a href="index.php?act=new&amp;do=reset">' . lng('unread_reset') . '</a><br/>';
            echo '<a href="index.php?act=new&amp;do=select">' . lng('unread_show_for_period') . '</a></p>';
    }
} else {
    /*
    -----------------------------------------------------------------
    Вывод 10 последних тем (для незарегистрированных)
    -----------------------------------------------------------------
    */
    echo '<div class="phdr"><a href="index.php"><b>' . lng('forum') . '</b></a> | ' . lng('unread_last_10') . '</div>';
    $req = mysql_query("SELECT * FROM `forum` WHERE `type` = 't' AND `close` != '1' ORDER BY `time` DESC LIMIT 10");
    if (mysql_num_rows($req)) {
        while ($res = mysql_fetch_assoc($req)) {
            $q3 = mysql_query("select `id`, `refid`, `text` from `forum` where type='r' and id='" . $res['refid'] . "' LIMIT 1");
            $razd = mysql_fetch_assoc($q3);
            $q4 = mysql_query("select `id`, `refid`, `text` from `forum` where type='f' and id='" . $razd['refid'] . "' LIMIT 1");
            $frm = mysql_fetch_assoc($q4);
            $nikuser = mysql_query("SELECT `from`, `time` FROM `forum` WHERE `type` = 'm' AND `close` != '1' AND `refid` = '" . $res['id'] . "'ORDER BY `time` DESC");
            $colmes1 = mysql_num_rows($nikuser);
            $cpg = ceil($colmes1 / Vars::$USER_SET['page_size']);
            $nam = mysql_fetch_assoc($nikuser);
            echo $i % 2 ? '<div class="list2">' : '<div class="list1">';
            // Значки
            $icons = array(
                ($np ? (!$res['vip'] ? Functions::getImage('forum_normal.png') : '') : Functions::getImage('forum_new.png')),
                ($res['vip'] ? Functions::getImage('forum_pin.png') : ''),
                ($res['realid'] ? Functions::getImage('rating.png') : ''),
                ($res['edit'] ? Functions::getImage('forum_closed.png') : '')
            );
            echo Functions::displayMenu($icons, '&#160;', '&#160;');
            echo '<a href="index.php?id=' . $res['id'] . ($cpg > 1 && $_SESSION['uppost'] ? '&amp;clip&amp;page=' . $cpg : '') . '">' . $res['text'] . '</a>&#160;[' . $colmes1 . ']';
            if ($cpg > 1)
                echo '&#160;<a href="index.php?id=' . $res['id'] . ($_SESSION['uppost'] ? '' : '&amp;clip&amp;page=' . $cpg) . '">&gt;&gt;</a>';
            echo '<br/><div class="sub"><a href="index.php?id=' . $razd['id'] . '">' . $frm['text'] . '&#160;/&#160;' . $razd['text'] . '</a><br />';
            echo $res['from'];
            if (!empty($nam['from'])) {
                echo '&#160;/&#160;' . $nam['from'];
            }
            echo ' <span class="gray">' . date("d.m.y / H:i", $nam['time']) . '</span>';
            echo '</div></div>';
            $i++;
        }
    } else {
        echo '<div class="menu"><p>' . lng('list_empty') . '</p></div>';
    }
    echo '<div class="phdr"><a href="index.php">' . lng('to_forum') . '</a></div>';
}