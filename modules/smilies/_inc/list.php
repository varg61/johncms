<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Oleg Kasyanov
 * Date: 07.01.13
 * Time: 16:46
 * To change this template use File | Settings | File Templates.
 */

$cat = Router::$ROUTE[1];
$backLink = Router::getUrl(2);
$url = Router::getUrl(3);

$smileys = glob(ROOTPATH . 'assets' . DIRECTORY_SEPARATOR . 'smilies' . DIRECTORY_SEPARATOR . $cat . DIRECTORY_SEPARATOR . '*.{gif,jpg,png}', GLOB_BRACE);
$total = count($smileys);

$end = Vars::$START + Vars::$USER_SET['page_size'];
if ($end > $total) {
    $end = $total;
}
echo'<div class="phdr"><a href="' . $url . '"><b>' . __('smileys') . '</b></a> | ' . __($cat) . '</div>';
if ($total) {
    if (Vars::$USER_ID && !Vars::$IS_MOBILE) {
        if (($user_sm = Vars::getUserData('smileys')) === FALSE) {
            $user_sm = array();
        }
        echo'<div class="topmenu">' .
            '<a href="' . $backLink . '/mysmilies">' . __('my_smileys') . '</a>  (' . count($user_sm) . ' / ' . $user_smileys . ')' .
            '</div>' .
            '<form action="' . $backLink . '/mysmilies/set?cat=' . $cat . '&amp;start=' . Vars::$START . '" method="post">';
    }
    if ($total > Vars::$USER_SET['page_size']) {
        echo'<div class="topmenu">' . Functions::displayPagination($url . '?', Vars::$START, $total, Vars::$USER_SET['page_size']) . '</div>';
    }
    for ($i = Vars::$START; $i < $end; $i++) {
        $smile = preg_replace('#^(.*?).(gif|jpg|png)$#isU', '$1', basename($smileys[$i], 1));
        echo $i % 2 ? '<div class="list2">' : '<div class="list1">';
        if (Vars::$USER_ID && !Vars::$IS_MOBILE) {
            echo (in_array($smile, $user_sm) ? '' : '<input type="checkbox" name="add_sm[]" value="' . $smile . '" />&#160;');
        }
        echo '<img src="' . Vars::$HOME_URL . '/assets/smilies/' . $cat . '/' . basename($smileys[$i]) . '" alt="" />&#160;:' . $smile . ': ' . __('lng_or') . ' :' . Functions::translit($smile) . ':' .
            '</div>';
    }
    if (Vars::$USER_ID && !Vars::$IS_MOBILE) {
        echo '<div class="gmenu"><input type="submit" name="add" value=" ' . __('add') . ' "/></div></form>';
    }
} else {
    echo '<div class="menu"><p>' . __('list_empty') . '</p></div>';
}
echo '<div class="phdr">' . __('total') . ': ' . $total . '</div>';
if ($total > Vars::$USER_SET['page_size']) {
    echo'<div class="topmenu">' . Functions::displayPagination($url . '?', Vars::$START, $total, Vars::$USER_SET['page_size']) . '</div>' .
        '<p><form action="' . $url . '?act=list&amp;cat=' . urlencode($cat) . '" method="post">' .
        '<input type="text" name="page" size="2"/>' .
        '<input type="submit" value="' . __('to_page') . ' &gt;&gt;"/></form></p>';
}
echo '<p><a href="' . $_SESSION['ref'] . '">' . __('back') . '</a></p>';