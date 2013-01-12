<?php

/**
 * @package     JohnCMS
 * @link        http://johncms.com
 * @copyright   Copyright (C) 2008-2011 JohnCMS Community
 * @license     LICENSE.txt (see attached file)
 * @version     VERSION.txt (see attached file)
 * @author      http://johncms.com/about
 */

defined('_IN_JOHNCMS') or die('Error: restricted access');
$url = Router::getUri(2);

$textl = __('download_title');
$down_path = 'files/download';
$screens_path = 'files/download/screen';
$files_path = 'files/download/files';
/*
-----------------------------------------------------------------
Настройки
-----------------------------------------------------------------
*/
$set_down = !empty(Vars::$SYSTEM_SET['download']) ? unserialize(Vars::$SYSTEM_SET['download']) : array('mod'           => 1,
                                                                                                       'theme_screen'  => 1,
                                                                                                       'top'           => 25,
                                                                                                       'icon_java'     => 1,
                                                                                                       'video_screen'  => 1,
                                                                                                       'screen_resize' => 1);
if ($set_down['video_screen'] && !extension_loaded('ffmpeg')) $set_down['video_screen'] = 0;

/*
-----------------------------------------------------------------
Ограничиваем доступ к Загрузкам
-----------------------------------------------------------------
*/
$error = '';
if ((!isset(Vars::$ACL['downloads']) || !Vars::$ACL['downloads']) && Vars::$USER_RIGHTS < 7) {
    $error = __('download_closed');
} elseif (isset(Vars::$ACL['downloads']) && Vars::$ACL['downloads'] == 1 && !Vars::$USER_ID) {
    $error = __('access_guest_forbidden');
}
if ($error) {
    echo Functions::displayError($error);
    exit;
}
$old = time() - 259200;

/*
-----------------------------------------------------------------
Список разрешений для выгрузки
-----------------------------------------------------------------
*/
$defaultExt = array('mp4',
    'rar',
    'zip',
    'pdf',
    'nth',
    'txt',
    'tar',
    'gz',
    'jpg',
    'jpeg',
    'gif',
    'png',
    'bmp',
    '3gp',
    'mp3',
    'mpg',
    'thm',
    'jad',
    'jar',
    'cab',
    'sis',
    'sisx',
    'exe',
    'msi',
    'apk',
    'djvu',
    'fb2'
);
/*
-----------------------------------------------------------------
Переключаем режимы работы
-----------------------------------------------------------------
*/
$actions = array(
    'add_cat'         => '_inc/category',
    'edit_cat'        => '_inc/category',
    'delete_cat'      => '_inc/category',
    'mod_files'       => '_inc/outputFiles',
    'new_files'       => '_inc/outputFiles',
    'top_files'       => '_inc/outputFiles',
    'user_files'      => '_inc/outputFiles',
    'comments'        => '_inc/comments',
    'review_comments' => '_inc/comments',
    'edit_file'       => '_inc/fileControl',
    'delete_file'     => '_inc/fileControl',
    'edit_about'      => '_inc/fileControl',
    'edit_screen'     => '_inc/fileControl',
    'files_more'      => '_inc/fileControl',
    'jad_file'        => '_inc/fileControl',
    'mp3tags'         => '_inc/fileControl',
    'load_file'       => '_inc/fileControl',
    'open_zip'        => '_inc/fileControl',
    'txt_in_jar'      => '_inc/fileControl',
    'txt_in_zip'      => '_inc/fileControl',
    'view'            => '_inc/fileControl',
    'transfer_file'   => '_inc/fileControl',
    'custom_size'     => '_inc/fileControl',
    'down_file'       => '_inc/upload',
    'import'          => '_inc/upload',
    'scan_about'      => '_inc',
    'scan_dir'        => '_inc',
    'search'          => '_inc',
    'top_users'       => '_inc',
    'recount'         => '_inc',
    'bookmark'        => '_inc',
    'redirect'        => '_inc'
);

if (isset($actions[Vars::$ACT]) && is_file(MODPATH . Router::$PATH . DIRECTORY_SEPARATOR . $actions[Vars::$ACT] . DIRECTORY_SEPARATOR . Vars::$ACT . '.php')) {
    require_once(MODPATH . Router::$PATH . DIRECTORY_SEPARATOR . $actions[Vars::$ACT] . DIRECTORY_SEPARATOR . Vars::$ACT . '.php');
} else {
    if (!isset(Vars::$ACL['downloads']) || !Vars::$ACL['downloads'])
        echo '<div class="rmenu"><b>' . __('download_closed') . '</b></div>';
    /*
    -----------------------------------------------------------------
    Получаем список файлов и папок
    -----------------------------------------------------------------
    */
    $notice = FALSE;
    if (Vars::$ID) {
        $cat = mysql_query("SELECT * FROM `cms_download_category` WHERE `id` = '" . Vars::$ID . "' LIMIT 1");
        $res_down_cat = mysql_fetch_assoc($cat);
        if (mysql_num_rows($cat) == 0 || !is_dir($res_down_cat['dir'])) {
            echo Functions::displayError(__('not_found_dir'), '<a href="' . $url . '">' . __('download_title') . '</a>');
            exit;
        }
        $title_pages = Validate::checkout(mb_substr($res_down_cat['rus_name'], 0, 30));
        $textl = mb_strlen($res_down_cat['rus_name']) > 30 ? $title_pages . '...' : $title_pages;
        $navigation = Download::navigation(array('dir' => $res_down_cat['dir'], 'refid' => $res_down_cat['refid'], 'name' => $res_down_cat['rus_name']));
        $total_new = mysql_result(mysql_query("SELECT COUNT(*) FROM `cms_download_files` WHERE `type` = '2'  AND `time` > $old AND `dir` LIKE '" . ($res_down_cat['dir']) . "%'"), 0);
        if ($total_new)
            $notice = '<a href="' . $url . '?act=new_files&amp;id=' . Vars::$ID . '">' . __('new_files') . '</a> (' . $total_new . ')<br />';
    } else {
        $navigation = '<b>' . __('download_title') . '</b></div>' .
            '<div class="topmenu"><a href="' . $url . '?act=search">' . __('search') . '</a> | ' .
            '<a href="' . $url . '?act=top_files&amp;id=0">' . __('top_files') . '</a> | ' .
            '<a href="' . $url . '?act=top_users">' . __('top_users') . '</a>';
        $total_new = mysql_result(mysql_query("SELECT COUNT(*) FROM `cms_download_files` WHERE `type` = '2'  AND `time` > $old"), 0);
        if ($total_new)
            $notice = '<a href="' . $url . '?act=new_files&amp;id=' . Vars::$ID . '">' . __('new_files') . '</a> (' . $total_new . ')<br />';
    }
    if (Vars::$USER_RIGHTS == 4 || Vars::$USER_RIGHTS >= 6) {
        $mod_files = mysql_result(mysql_query("SELECT COUNT(*) FROM `cms_download_files` WHERE `type` = '3'"), 0);
        if ($mod_files > 0)
            $notice .= '<a href="' . $url . '?act=mod_files">' . __('mod_files') . '</a> ' . $mod_files;
    }
    /*
    -----------------------------------------------------------------
    Уведомления
    -----------------------------------------------------------------
    */
    if ($notice) echo '<p>' . $notice . '</p>';
    /*
    -----------------------------------------------------------------
    Навигация
    -----------------------------------------------------------------
    */
    echo '<div class="phdr">' . $navigation . '</div>';
    /*
    -----------------------------------------------------------------
    Выводим список папок и файлов
    -----------------------------------------------------------------
    */
    $total_cat = mysql_result(mysql_query("SELECT COUNT(*) FROM `cms_download_category` WHERE `refid` = '" . Vars::$ID . "'"), 0);
    $total_files = mysql_result(mysql_query("SELECT COUNT(*) FROM `cms_download_files` WHERE `refid` = '" . Vars::$ID . "' AND `type` = 2"), 0);
    $sum_total = $total_files + $total_cat;
    if ($sum_total) {
        if ($total_cat > 0) {
            /*
             -----------------------------------------------------------------
             Выводи папки
             -----------------------------------------------------------------
             */
            if ($total_files) echo '<div class="phdr"><b>' . __('list_category') . '</b></div>';
            $req_down = mysql_query("SELECT * FROM `cms_download_category` WHERE `refid` = '" . Vars::$ID . "' ORDER BY `sort` ASC ");
            $i = 0;
            while ($res_down = mysql_fetch_assoc($req_down)) {
                echo (($i++ % 2) ? '<div class="list2">' : '<div class="list1">') .
                    Functions::loadModuleImage('folder.png') . '&#160;' .
                    '<a href="' . $url . '?id=' . $res_down['id'] . '">' . Validate::checkout($res_down['rus_name']) . '</a> (' . $res_down['total'] . ')';
                if ($res_down['field'])
                    echo '<div><small>' . __('extensions') . ': <span class="green"><b>' . $res_down['text'] . '</b></span></small></div>';
                if (Vars::$USER_RIGHTS == 4 || Vars::$USER_RIGHTS >= 6 || !empty($res_down['desc'])) {
                    $menu = array(
                        '<a href="' . $url . '?act=edit_cat&amp;id=' . $res_down['id'] . '&amp;up">' . __('up') . '</a>',
                        '<a href="' . $url . '?act=edit_cat&amp;id=' . $res_down['id'] . '&amp;down">' . __('down') . '</a>',
                        '<a href="' . $url . '?act=edit_cat&amp;id=' . $res_down['id'] . '">' . __('edit') . '</a>',
                        '<a href="' . $url . '?act=delete_cat&amp;id=' . $res_down['id'] . '">' . __('delete') . '</a>'
                    );
                    echo '<div class="sub">' .
                        (!empty($res_down['desc']) ? '<div class="gray">' . Validate::checkout($res_down['desc'], 1, 1) . '</div>' : '') .
                        (Vars::$USER_RIGHTS == 4 || Vars::$USER_RIGHTS >= 6 ? Functions::displayMenu($menu) : '') .
                        '</div>';
                }
                echo '</div>';
            }
        }
        if ($total_files > 0) {
            /*
             -----------------------------------------------------------------
             Выводи файлы
             -----------------------------------------------------------------
             */
            if ($total_cat) echo '<div class="phdr"><b>' . __('list_files') . '</b></div>';
            if ($total_files > 1) {
                /*
               -----------------------------------------------------------------
               Сортировка файлов
               -----------------------------------------------------------------
               */
                if (!isset($_SESSION['sort_down'])) $_SESSION['sort_down'] = 0;
                if (!isset($_SESSION['sort_down2'])) $_SESSION['sort_down2'] = 0;
                if (isset($_POST['sort_down']))
                    $_SESSION['sort_down'] = $_POST['sort_down'] ? 1 : 0;
                if (isset($_POST['sort_down2']))
                    $_SESSION['sort_down2'] = $_POST['sort_down2'] ? 1 : 0;
                $sql_sort = isset($_SESSION['sort_down']) && $_SESSION['sort_down'] ? ', `name`' : ', `time`';
                $sql_sort .= isset($_SESSION['sort_down2']) && $_SESSION['sort_down2'] ? ' ASC' : ' DESC';
                echo '<form action="' . $url . '?id=' . Vars::$ID . '" method="post"><div class="topmenu">' .
                    '<b>' . __('download_sort') . ': </b>' .
                    '<select name="sort_down" style="font-size:x-small">' .
                    '<option value="0"' . (!$_SESSION['sort_down'] ? ' selected="selected"' : '') . '>' . __('download_sort1') . '</option>' .
                    '<option value="1"' . ($_SESSION['sort_down'] ? ' selected="selected"' : '') . '>' . __('download_sort2') . '</option></select> &amp; ' .
                    '<select name="sort_down2" style="font-size:x-small">' .
                    '<option value="0"' . (!$_SESSION['sort_down2'] ? ' selected="selected"' : '') . '>' . __('download_sort3') . '</option>' .
                    '<option value="1"' . ($_SESSION['sort_down2'] ? ' selected="selected"' : '') . '>' . __('download_sort4') . '</option></select>' .
                    '<input type="submit" value="&gt;&gt;" style="font-size:x-small"/></div></form>';
            } else
                $sql_sort = '';
            /*
              -----------------------------------------------------------------
              Постраничная навигация
              -----------------------------------------------------------------
              */
            if ($total_files > Vars::$USER_SET['page_size'])
                echo '<div class="topmenu">' . Functions::displayPagination($url . '?id=' . Vars::$ID . '&amp;', Vars::$START, $total_files, Vars::$USER_SET['page_size']) . '</div>';
            /*
              -----------------------------------------------------------------
              Выводи данные
              -----------------------------------------------------------------
              */
            $req_down = mysql_query("SELECT * FROM `cms_download_files` WHERE `refid` = '" . Vars::$ID . "' AND `type` < 3 ORDER BY `type` ASC $sql_sort " . Vars::db_pagination());
            $i = 0;
            while ($res_down = mysql_fetch_assoc($req_down)) {
                echo (($i++ % 2) ? '<div class="list2">' : '<div class="list1">') . Download::displayFile($res_down) . '</div>';
            }
        }
    } else {
        echo '<div class="menu"><p>' . __('list_empty') . '</p></div>';
    }
    echo '<div class="phdr">';
    if ($total_cat || !$total_files) echo  __('total_dir') . ': ' . $total_cat;
    if ($total_cat && $total_files) echo '&nbsp;|&nbsp;';
    if ($total_files) echo __('total_files') . ': ' . $total_files;
    echo '</div>';
    /*
    -----------------------------------------------------------------
    Постраничная навигация
    -----------------------------------------------------------------
    */
    if ($total_files > Vars::$USER_SET['page_size']) {
        echo '<div class="topmenu">' . Functions::displayPagination($url . '?id=' . Vars::$ID . '&amp;', Vars::$START, $total_files, Vars::$USER_SET['page_size']) . '</div>' .
            '<p><form action="' . $url . '" method="get">' .
            '<input type="hidden" name="id" value="' . Vars::$ID . '"/>' .
            '<input type="text" name="page" size="2"/><input type="submit" value="' . __('to_page') . ' &gt;&gt;"/></form></p>';
    }
    if (Vars::$USER_RIGHTS == 4 || Vars::$USER_RIGHTS >= 6) {
        /*
        -----------------------------------------------------------------
        Выводим ссылки на модерские функции
        -----------------------------------------------------------------
        */
        echo '<p><div class="func"><form action="' . $url . '?act=redirect" method="post"><select name="admin_act">' .
            '<option value="add_cat">' . __('download_add_cat') . '</option>';
        if (Vars::$ID) {
            $del_cat = mysql_result(mysql_query("SELECT COUNT(*) FROM `cms_download_categoty` WHERE `refid` = '" . Vars::$ID . "'"), 0);
            if (!$del_cat) {
                echo '<option value="delete_cat">' . __('download_del_cat') . '</option>';
            }
            echo '<option value="edit_cat">' . __('download_edit_cat') . '</option>' .
                '<option value="import">' . __('download_import') . '</option>' .
                '<option value="down_file">' . __('download_upload_file') . '</option>';
        }
        echo '<option value="scan_dir">' . __('download_scan_dir') . '</option>' .
            '<option value="clean">' . __('download_clean') . '</option>' .
            '<option value="scan_about">' . __('download_scan_about') . '</option>' .
            '<option value="recount">' . __('download_recount') . '</option>' .
            '<input type="hidden" name="admin_id" value="' . Vars::$ID . '"/>' .
            '</select><input type="submit" value="' . __('do') . '"/></form></div></p>';
    } else if (isset($res_down_cat['field']) && $res_down_cat['field'] && Vars::$USER_ID && Vars::$ID)
        echo '<p><div class="func"><a href="' . $url . '?act=down_file&amp;id=' . Vars::$ID . '">' . __('download_upload_file') . '</a></div></p>';
    /*
    -----------------------------------------------------------------
    Нижнее меню навигации
    -----------------------------------------------------------------
    */
    echo'<p>';
    if (Vars::$ID) {
        echo'<a href="' . $url . '">' . __('download_title') . '</a>';
    } else {
        echo ((isset(Vars::$ACL['downcomm']) && Vars::$ACL['downcomm']) || Vars::$USER_RIGHTS >= 7 ? '<a href="' . $url . '?act=review_comments">' . __('review_comments') . '</a><br />' : '') .
            '<a href="' . $url . '?act=bookmark">' . __('download_bookmark') . '</a>';
    }
    echo'</p>';
}