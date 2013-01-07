<?php

/**
 * @package     JohnCMS
 * @link        http://johncms.com
 * @copyright   Copyright (C) 2008-2012 JohnCMS Community
 * @license     LICENSE.txt (see attached file)
 * @version     VERSION.txt (see attached file)
 * @author      http://johncms.com/about
 *
 * Главное меню сайта
 */

defined('_IN_JOHNCMS') or die('Error: restricted access');
$url = Router::getUrl(2);

// Обрабатываем ссылку для возврата
if (empty($_SESSION['ref'])) {
    $_SESSION['ref'] = isset($_SERVER['HTTP_REFERER']) ? htmlspecialchars($_SERVER['HTTP_REFERER']) : Vars::$HOME_URL;
}

// Обрабатываем глобальные переменные
$cat_list = array();
$dir_list = glob(ROOTPATH . 'assets' . DIRECTORY_SEPARATOR . 'avatars' . DIRECTORY_SEPARATOR . '*', GLOB_ONLYDIR);
foreach ($dir_list as $val) {
    $dir = basename($val);
    $avatar_cat[$dir] = __($dir);
    $cat_list[] = $dir;
}
$cat = isset($_GET['cat']) && in_array(trim($_GET['cat']), $cat_list) ? trim($_GET['cat']) : $cat_list[0];

$tpl = Template::getInstance();

switch (Vars::$ACT) {
    case 'avset':
        $select = isset($_GET['select']) ? substr(trim($_GET['select']), 0, 20) : FALSE;
        if (Vars::$USER_ID
            && $select
            && is_file(ROOTPATH . 'assets' . DIRECTORY_SEPARATOR . 'avatars' . DIRECTORY_SEPARATOR . $cat . DIRECTORY_SEPARATOR . $select)
        ) {
            if (isset($_POST['submit'])) {
                // Устанавливаем пользовательский Аватар
                if (@copy(ROOTPATH . 'assets' . DIRECTORY_SEPARATOR . 'avatars' . DIRECTORY_SEPARATOR . $cat . DIRECTORY_SEPARATOR . $select,
                    FILEPATH . 'users' . DIRECTORY_SEPARATOR . 'avatar' . DIRECTORY_SEPARATOR . Vars::$USER_ID . '.gif')
                ) {
                    echo '<div class="gmenu"><p>' . __('avatar_applied') . '<br />' .
                        '<a href="' . $_SESSION['ref'] . '">' . __('continue') . '</a></p></div>';
                } else {
                    echo Functions::displayError(__('error_avatar_select'), '<a href="' . $_SESSION['ref'] . '">' . __('back') . '</a>');
                }
            } else {
                echo'<div class="phdr"><a href="' . $url . '"><b>' . __('avatars') . '</b></a> | ' . __('set_to_profile') . '</div>' .
                    '<div class="rmenu"><p>' . __('avatar_change_warning') . '</p>' .
                    '<p><img src="' . Vars::$HOME_URL . '/assets/avatars/' . $cat . '/' . $select . '" alt="" /></p>' .
                    '<p><form action="' . $url . '?act=avset&amp;cat=' . urlencode($cat) . '&amp;select=' . urlencode($select) . '" method="post">' .
                    '<input type="submit" name="submit" value="' . __('save') . '"/>' .
                    '</form></p>' .
                    '</div>' .
                    '<div class="phdr"><a href="' . $url . '?act=avlist&amp;cat=' . $cat . '">' . __('cancel') . '</a></div>';
            }
        } else {
            echo Functions::displayError(__('error_wrong_data'));
        }
        break;

    case 'list':
        /*
        -----------------------------------------------------------------
        Показываем список аватаров
        -----------------------------------------------------------------
        */
        $avatars = glob(ROOTPATH . 'assets' . DIRECTORY_SEPARATOR . 'avatars' . DIRECTORY_SEPARATOR . $cat . DIRECTORY_SEPARATOR . '*.{gif,jpg,png}', GLOB_BRACE);
        $tpl->total = count($avatars);
        $end = Vars::$START + Vars::$USER_SET['page_size'];
        if ($end > $tpl->total) {
            $end = $tpl->total;
        }
        $list_avatars = array();
        if ($tpl->total) {
            for ($i = Vars::$START; $i < $end; $i++) {
                $list_avatars[$i] = array(
                    'image' => Vars::$HOME_URL . '/assets/avatars/' . $cat . '/' . basename($avatars[$i]),
                    'link'  => (Vars::$USER_ID ? $url . '?act=avset&amp;cat=' . urlencode($cat) . '&amp;select=' . urlencode(basename($avatars[$i])) : '#')
                );
            }
        } else {
            echo '<div class="menu"><p>' . __('list_empty') . '</p></div>';
        }
        echo'<div class="phdr">' . __('total') . ': ' . $tpl->total . '</div>';
        if ($tpl->total > Vars::$USER_SET['page_size']) {
            echo'<div class="topmenu">' . Functions::displayPagination($url . '?act=list&amp;cat=' . urlencode($cat) . '&amp;', Vars::$START, $tpl->total, Vars::$USER_SET['page_size']) . '</div>' .
                '<p><form action="' . $url . '?act=list&amp;cat=' . urlencode($cat) . '" method="post">' .
                '<input type="text" name="page" size="2"/>' .
                '<input type="submit" value="' . __('to_page') . ' &gt;&gt;"/></form></p>';
        }
        echo'<p><a href="' . $_SESSION['ref'] . '">' . __('back') . '</a></p>';
        $tpl->list = $list_avatars;
        $tpl->category = __($cat);
        $tpl->contents = $tpl->includeTpl('list_avatars');
        break;

    default:
        /*
        -----------------------------------------------------------------
        Выводим каталог Аватаров
        -----------------------------------------------------------------
        */
        echo '<div class="phdr"><a href="' . Vars::$HOME_URL . '/help"><b>F.A.Q.</b></a> | ' . __('avatars') . '</div>';
        asort($avatar_cat);
        $i = 0;
        $list_categories = array();
        foreach ($avatar_cat as $key => $val) {
            $count = count(glob(ROOTPATH . 'assets' . DIRECTORY_SEPARATOR . 'avatars' . DIRECTORY_SEPARATOR . $key . DIRECTORY_SEPARATOR . '*.{gif,jpg,png}', GLOB_BRACE));
            $list_categories[$i] = array(
                'link'  => $url . '?act=list&amp;cat=' . urlencode($key),
                'name'  => htmlspecialchars($val),
                'count' => count(glob(ROOTPATH . 'assets' . DIRECTORY_SEPARATOR . 'avatars' . DIRECTORY_SEPARATOR . $key . DIRECTORY_SEPARATOR . '*.{gif,jpg,png}', GLOB_BRACE))
            );
            ++$i;
        }
        $tpl->list = $list_categories;
        $tpl->contents = $tpl->includeTpl('list_categories');
}