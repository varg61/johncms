<?php

/**
 * @author simba
 * @copyright 2011
 */
 
defined('_IN_JOHNCMS') or die('Restricted access');

echo '<div class="phdr">'.lng('entry_points').'</div>';
$count = mysql_result(mysql_query("SELECT COUNT(DISTINCT `pop`) FROM `counter` WHERE `robot` = '' AND `host` != 0;"), 0);
if($count > 0){
    $req = mysql_query("SELECT * FROM `counter` WHERE `robot` = '' AND `host` != 0 GROUP BY `pop` ORDER BY `date` ". Vars::db_pagination());
    $i = 0;
    while($arr = mysql_fetch_array($req)){
        echo ($i % 2) ? '<div class="list1">' : '<div class="list2">';
        ++$i;
        $count_view = mysql_result(mysql_query("SELECT COUNT(*) FROM `counter` WHERE `robot` = '' AND `host` != '0' AND `pop` = '" . $arr['pop'] . "'") , 0);
        
        if($arr['pop'] !== '/'){
        echo '<b>'.Functions::displayDate($arr['date']).'</b> | '.lng('title').': '.$arr['head'];
        echo '<div class="sub">'.lng('page').': <a href="'.$arr['pop'].'">'.$arr['pop'].'</a><br/>';
        }else{ 
        echo'<b>'.Functions::displayDate($arr['date']).'</b> | <a href="'.$arr['pop'].'">'.lng('home_page').'</a><div class="sub">'; 
        }
        
        echo lng('movies').': '.$count_view.'</div>';
        
        echo '</div>';    
        }
    
    echo '<div class="phdr">'.lng('total').': '.$count.'</div>';
    if ($count > Vars::$USER_SET['page_size']){
    	echo '<div class="topmenu">';
    	echo Functions::displayPagination(Vars::$URI.'?act=point_in&amp;', Vars::$START, $count, Vars::$USER_SET['page_size']) . '</div>';
    	echo '<p><form action="'.Vars::$URI.'" method="get"><input type="hidden" name="act" value="point_in"/><input type="text" name="page" size="2"/><input type="submit" value="'.lng('to_page').' &gt;&gt;"/></form></p>';}
    
}else{
 echo '<div class="rmenu">'.lng('no_data').'!</div>';   
}
?>