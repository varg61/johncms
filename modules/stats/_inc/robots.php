<?php

/**
 * @author simba
 * @copyright 2011
 */
defined('_IN_JOHNCMS') or die('Restricted access');

echo '<div class="phdr">'.lng('robots').'</div>';
$count = mysql_result(mysql_query("SELECT COUNT(DISTINCT `robot`) FROM `counter` WHERE `robot` != '';"), 0);
if($count > 0){
    $req = mysql_query("SELECT * FROM `counter` WHERE `robot` != '' GROUP BY `robot`". Vars::db_pagination());
    $i = 0;
    while($arr = mysql_fetch_array($req)){
        echo ($i % 2) ? '<div class="list1">' : '<div class="list2">';
        ++$i;
        $count_view = mysql_result(mysql_query("SELECT COUNT(*) FROM `counter` WHERE `robot` = '".$arr['robot']."'") , 0);
        echo Functions::loadModuleImage('robot.png') .' <a href="'.Vars::$URI.'?act=robot_types&amp;robot='.$arr['robot'].'">'.$arr['robot'].'</a>
        <div class="sub">'.lng('movies').': '.$count_view.'</div>';
        echo '</div>';    
        }
    
    echo '<div class="phdr">'.lng('total').': '.$count.'</div>';
    
}else{
 echo '<div class="rmenu">'.lng('no_data').'!</div>';   
}