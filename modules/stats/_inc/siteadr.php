<?php

/**
 * @author simba
 * @copyright 2011
 */
defined('_IN_JOHNCMS') or die('Restricted access');

echo '<div class="phdr">'.lng('referrers').'</div>';

$site = isset($_GET['site']) ? htmlspecialchars((string)$_GET['site']) : FALSE;

if(!$site){
    echo Functions::displayError(lng('error_data'), '<a href="'.Vars::$URI.'">'.lng('statistics').'</a>');
    
}else{

$count = mysql_result(mysql_query("SELECT COUNT(DISTINCT `ref`) FROM `counter` WHERE `ref` LIKE '%".$site."%';"), 0);
if($count > 0){
    $req = mysql_query("SELECT * FROM `counter` WHERE `ref` LIKE '%".$site."%' GROUP BY `ref` ". Vars::db_pagination());
    $i = 0;
    while($arr = mysql_fetch_array($req)){
        echo ($i % 2) ? '<div class="list1">' : '<div class="list2">';
        ++$i;
        $count_hits = mysql_result(mysql_query("SELECT COUNT(*) FROM `counter` WHERE `ref` = '".$arr['ref']."'") , 0);
        echo Functions::loadModuleImage('url.png') .' <a href="'.$arr['ref'].'">'.$arr['ref'].'</a>
        <div class="sub">'.Functions::displayDate($arr['date']).' | '.lng('movies').': '.$count_hits.'</div>
        ';
        echo '</div>';   
        }
    
    echo '<div class="phdr">'.lng('total').': '.$count.'</div>';
    if ($count > Vars::$USER_SET['page_size']){
    	echo '<div class="topmenu">';
    	echo Functions::displayPagination(Vars::$URI.'?act=siteadr&amp;site='.$site.'&amp;', Vars::$START, $count, Vars::$USER_SET['page_size']) . '</div>';
    	echo '<p><form action="'.Vars::$URI.'" method="get"><input type="hidden" name="act" value="siteadr"/><input type="hidden" name="site" value="'.$site.'"/><input type="text" name="page" size="2"/><input type="submit" value="'.lng('to_page').' &gt;&gt;"/></form></p>';}
    
}else{
 echo '<div class="rmenu">'.lng('no_data').'!</div>';   
}


$back_links = '<a href="?act=referer">'.lng('back').'</a><br/>';

}

?>