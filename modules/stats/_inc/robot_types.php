<?php

/**
 * @author simba
 * @copyright 2011
 */
defined('_IN_JOHNCMS') or die('Restricted access');
$robot = isset($_GET['robot']) ? htmlspecialchars((string)$_GET['robot']) : FALSE;

if(!$robot){
    echo Functions::displayError(lng('error'), '<a href="'.Vars::$URI.'">'.lng('statistics').'</a>');
}else{

echo '<div class="phdr">'.lng('statistics_on').' '.lng('robot').' '.$robot.'</div>';
$count = mysql_num_rows(mysql_query("select * from `counter` WHERE `robot` = '".$robot."' GROUP BY `robot_type`;"));
if($count > 0){
    $req = mysql_query("SELECT * FROM `counter` WHERE `robot` = '".$robot."' GROUP BY `robot_type` ". Vars::db_pagination());
    $i = 0;
    while($arr = mysql_fetch_array($req)){
        echo ($i % 2) ? '<div class="list1">' : '<div class="list2">';
        ++$i;
        $count_view = mysql_result(mysql_query("SELECT COUNT(*) FROM `counter` WHERE `robot` = '".$robot."' AND `robot_type` = '".$arr['robot_type']."'") , 0);
        echo Functions::getIcon('robot.png') .' <b>'.$arr['robot_type'].'</b>
        <div class="sub">'.lng('movies').': '.$count_view.'</div>';
        echo '</div>';    
        }
    echo '<div class="phdr">'.lng('total').': '.$count.'</div>';
}else{
 echo '<div class="rmenu">'.lng('no_data').'!</div>';   
}
$back_links = '<a href="?act=robots">'.lng('back').'</a><br/>';
}
?>