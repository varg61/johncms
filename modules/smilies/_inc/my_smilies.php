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
$backLink = Router::getUrl(2);
$url = Router::getUrl(3);

$act = isset(Router::$ROUTE[2]) ? Router::$ROUTE[2] : FALSE;
switch ($act) {
    case'set':
        $add = isset($_POST['add']);
        $delete = isset($_POST['delete']);
        if (Vars::$IS_MOBILE || ($delete && !$_POST['delete_sm']) || ($add && !$_POST['add_sm'])) {
            echo Functions::displayError(__('error_wrong_data'), '<a href="' . $url . '">' . __('smileys') . '</a>');
            exit;
        }
        if (($smileys = Vars::getUserData('smileys')) === FALSE) $smileys = array();
        if (!is_array($smileys))
            $smileys = array();
        if ($delete)
            $smileys = array_diff($smileys, $_POST['delete_sm']);
        if ($add) {
            $add_sm = $_POST['add_sm'];
            $smileys = array_unique(array_merge($smileys, $add_sm));
        }
        if (isset($_GET['clean']))
            $smileys = array();
        if (count($smileys) > $user_smileys) {
            $smileys = array_chunk($smileys, $user_smileys, TRUE);
            $smileys = $smileys[0];
        }
        Vars::setUserData('smileys', $smileys);
        if ($delete || isset($_GET['clean'])) {
            header('Location: ' . $url . '?start=' . Vars::$START . '');
        } else {
            header('Location: ' . $backLink . '/' . urlencode($cat) . '?start=' . Vars::$START . '');
        }
        break;

    default:
        echo '<div class="phdr"><a href="' . $url . '"><b>' . __('smileys') . '</b></a> | ' . __('my_smileys') . '</div>';
        if (($smileys = Vars::getUserData('smileys')) === FALSE) $smileys = array();
        $total = count($smileys);
        if ($total)
            echo '<form action="' . $url . '/set?start=' . Vars::$START . '" method="post">';
        if ($total > Vars::$USER_SET['page_size']) {
            $smileys = array_chunk($smileys, Vars::$USER_SET['page_size'], TRUE);
            if (Vars::$START) {
                $key = (Vars::$START - Vars::$START % Vars::$USER_SET['page_size']) / Vars::$USER_SET['page_size'];
                $smileys_view = $smileys[$key];
                if (!count($smileys_view))
                    $smileys_view = $smileys[0];
                $smileys = $smileys_view;
            } else {
                $smileys = $smileys[0];
            }
        }
        $i = 0;
        foreach ($smileys as $value) {
            $smile = ':' . $value . ':';
            echo ($i % 2 ? '<div class="list2">' : '<div class="list1">') .
                '<input type="checkbox" name="delete_sm[]" value="' . $value . '" />&#160;' .
                Functions::smilies($smile, Vars::$USER_RIGHTS >= 1 ? 1 : 0) . '&#160;' . $smile . ' ' . __('lng_or') . ' ' . Functions::translit($smile) . '</div>';
            $i++;
        }
        if ($total) {
            echo '<div class="rmenu"><input type="submit" name="delete" value=" ' . __('delete') . ' "/></div></form>';
        } else {
            echo '<div class="menu"><p>' . __('list_empty') . '<br /><a href="' . $url . '">' . __('add_smileys') . '</a></p></div>';
        }
        echo '<div class="phdr">' . __('total') . ': ' . $total . ' / ' . $user_smileys . '</div>';
        if ($total > Vars::$USER_SET['page_size'])
            echo '<div class="topmenu">' . Functions::displayPagination($url . '?', Vars::$START, $total, Vars::$USER_SET['page_size']) . '</div>';
        echo '<p>' . ($total ? '<a href="' . $url . '?act=set&amp;clean">' . __('clear') . '</a><br />'
            : '') . '<a href="' . $_SESSION['ref'] . '">' . __('back') . '</a></p>';
}