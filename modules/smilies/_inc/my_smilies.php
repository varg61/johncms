<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Oleg Kasyanov
 * Date: 07.01.13
 * Time: 16:55
 * To change this template use File | Settings | File Templates.
 */

global $catalog;
$cat = isset($_GET['cat']) && array_key_exists(trim($_GET['cat']), $catalog) ? trim($_GET['cat']) : $catalog[0];
$backLink = Router::getUri(2);
$url = Router::getUri(3);

$act = isset(Router::$ROUTE[2]) ? Router::$ROUTE[2] : FALSE;
switch ($act) {
    case'set':
        $add = isset($_POST['add']);
        $delete = isset($_POST['delete']);
        if (Vars::$IS_MOBILE || ($delete && !$_POST['delete_sm']) || ($add && !$_POST['add_sm'])) {
            echo Functions::displayError(__('error_wrong_data'), '<a href="' . $url . '">' . __('smilies') . '</a>');
            exit;
        }
        if (($smilies = Vars::getUserData('smilies')) === FALSE) $smilies = array();
        if (!is_array($smilies))
            $smilies = array();
        if ($delete)
            $smilies = array_diff($smilies, $_POST['delete_sm']);
        if ($add) {
            $add_sm = $_POST['add_sm'];
            $smilies = array_unique(array_merge($smilies, $add_sm));
        }
        if (isset($_GET['clean']))
            $smilies = array();
        if (count($smilies) > $user_smilies) {
            $smilies = array_chunk($smilies, $user_smilies, TRUE);
            $smilies = $smilies[0];
        }
        Vars::setUserData('smilies', $smilies);
        if ($delete || isset($_GET['clean'])) {
            header('Location: ' . $url . '?start=' . Vars::$START . '');
        } else {
            header('Location: ' . $backLink . '/' . urlencode($cat) . '?start=' . Vars::$START . '');
        }
        break;

    default:
        echo '<div class="phdr"><a href="' . $url . '"><b>' . __('smilies') . '</b></a> | ' . __('my_smilies') . '</div>';
        if (($smilies = Vars::getUserData('smilies')) === FALSE) $smilies = array();
        $total = count($smilies);
        if ($total)
            echo '<form action="' . $url . '/set?start=' . Vars::$START . '" method="post">';
        if ($total > Vars::$USER_SET['page_size']) {
            $smilies = array_chunk($smilies, Vars::$USER_SET['page_size'], TRUE);
            if (Vars::$START) {
                $key = (Vars::$START - Vars::$START % Vars::$USER_SET['page_size']) / Vars::$USER_SET['page_size'];
                $smilies_view = $smilies[$key];
                if (!count($smilies_view))
                    $smilies_view = $smilies[0];
                $smilies = $smilies_view;
            } else {
                $smilies = $smilies[0];
            }
        }
        $i = 0;
        foreach ($smilies as $value) {
            $smile = ':' . $value . ':';
            echo ($i % 2 ? '<div class="list2">' : '<div class="list1">') .
                '<input type="checkbox" name="delete_sm[]" value="' . $value . '" />&#160;' .
                Functions::smilies($smile, Vars::$USER_RIGHTS >= 1 ? 1 : 0) . '&#160;' . $smile . ' ' . __('lng_or') . ' ' . Functions::translit($smile) . '</div>';
            $i++;
        }
        if ($total) {
            echo '<div class="rmenu"><input type="submit" name="delete" value=" ' . __('delete') . ' "/></div></form>';
        } else {
            echo '<div class="menu"><p>' . __('list_empty') . '<br /><a href="' . $url . '">' . __('add_smilies') . '</a></p></div>';
        }
        echo '<div class="phdr">' . __('total') . ': ' . $total . ' / ' . $user_smilies . '</div>';
        if ($total > Vars::$USER_SET['page_size'])
            echo '<div class="topmenu">' . Functions::displayPagination($url . '?', Vars::$START, $total, Vars::$USER_SET['page_size']) . '</div>';
        echo '<p>' . ($total ? '<a href="' . $url . '?act=set&amp;clean">' . __('clear') . '</a><br />'
            : '') . '<a href="' . $_SESSION['ref'] . '">' . __('back') . '</a></p>';
}