<?php

/**
 * @author simba
 * @copyright 2011
 */

defined('_IN_JOHNCMS') or die('Restricted access');
$url = Router::getUri(2);

$robot = isset($_GET['robot']) ? htmlspecialchars((string)$_GET['robot']) : FALSE;

if (!$robot) {
    echo Functions::displayError(__('error'), '<a href="' . $url . '">' . __('statistics') . '</a>');
} else {

    echo '<div class="phdr">' . __('statistics_on') . ' ' . __('robot') . ' ' . $robot . '</div>';
    $count = mysql_num_rows(mysql_query("select * from `counter` WHERE `robot` = '" . $robot . "' GROUP BY `robot_type`;"));
    if ($count > 0) {
        $req = mysql_query("SELECT * FROM `counter` WHERE `robot` = '" . $robot . "' GROUP BY `robot_type` " . Vars::db_pagination());
        $i = 0;
        while ($arr = mysql_fetch_array($req)) {
            echo ($i % 2) ? '<div class="list1">' : '<div class="list2">';
            ++$i;
            $count_view = mysql_result(mysql_query("SELECT COUNT(*) FROM `counter` WHERE `robot` = '" . $robot . "' AND `robot_type` = '" . $arr['robot_type'] . "'"), 0);
            echo Functions::loadModuleImage('robot.png') . ' <b>' . $arr['robot_type'] . '</b>
        <div class="sub">' . __('movies') . ': ' . $count_view . '</div>';
            echo '</div>';
        }
        echo '<div class="phdr">' . __('total') . ': ' . $count . '</div>';
    } else {
        echo '<div class="rmenu">' . __('no_data') . '!</div>';
    }
    $back_links = '<a href="?act=robots">' . __('back') . '</a><br/>';
}
?>