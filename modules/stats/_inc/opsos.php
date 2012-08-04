<?php

/**
 * @author simba
 * @copyright 2011
 */
defined('_IN_JOHNCMS') or die('Restricted access');

echo '<div class="phdr">'.lng('operators').' ('.lng('hits').')</div>';
$count = mysql_result(mysql_query("SELECT COUNT(DISTINCT `operator`, `country`) FROM `counter`;"), 0);
if($count > 0){
    $req = mysql_query("SELECT * FROM `counter` GROUP BY `operator`, `country`". Vars::db_pagination());
    $i = 0;
    while($arr = mysql_fetch_array($req)){
        echo ($i % 2) ? '<div class="list1">' : '<div class="list2">';
        ++$i;
        $count_hits = mysql_result(mysql_query("SELECT COUNT(*) FROM `counter` WHERE `country` = '".$arr['country']."' AND `operator` = '".$arr['operator']."'") , 0);
        
        echo Functions::getIcon('opsos.png') .' '. $arr['operator'].'
        <div class="sub">'.lng('country').': '.$arr['country'].' | '.lng('total_hits').': '.$count_hits.'</div>';
        
        echo '</div>';    
        }
    
    echo '<div class="phdr">'.lng('total').': '.$count.'</div>';
    if ($count > Vars::$USER_SET['page_size']){
        echo '<div class="topmenu">';
    	echo Functions::displayPagination(Vars::$URI.'?act=opsos&amp;', Vars::$START, $count, Vars::$USER_SET['page_size']) . '</div>';
    	echo '<p><form action="'.Vars::$URI.'" method="get"><input type="hidden" name="act" value="opsos"/><input type="text" name="page" size="2"/><input type="submit" value="'.lng('to_page').' &gt;&gt;"/></form></p>';}
    
}else{
 echo '<div class="rmenu">'.lng('no_data').'!</div>';   
}
?>