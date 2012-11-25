<?php
/**
 * @author simba
 * @copyright 2011
 */
defined('_IN_JOHNCMS') or die('Restricted access');

echo '<div class="phdr">'.__('view_hosts').'</div>';
$count = mysql_result(mysql_query("SELECT COUNT(*) FROM `counter` WHERE `robot` = '' AND `host` != 0;"), 0);
if($count > 0){
    $req = mysql_query("SELECT * FROM `counter` WHERE `robot` = '' AND `host` != 0". Vars::db_pagination());
    $i = 0;
    while($arr = mysql_fetch_array($req)){
        echo ($i % 2) ? '<div class="list1">' : '<div class="list2">';
        ++$i;
        $count_view = mysql_result(mysql_query("SELECT COUNT(*) FROM `counter` WHERE `browser` = '".$arr['browser']."' AND `ip` = '".$arr['ip']."'") , 0);
		$time = date("H:i", $arr['date']);
        echo '<b>'.$time.'</b> - '.$arr['browser'].'
        <div class="sub">Ip: <a href="'.Vars::$HOME_URL.'/admin?act=search_ip&amp;ip='.$arr['ip'].'">'.$arr['ip'].'</a> <a href="'.Vars::$HOME_URL.'/admin/whois?ip='.$arr['ip'].'" title = "WhoIS ip">[?]</a> ';
        if($arr['ip_via_proxy'])
        echo '| <a href="'.Vars::$HOME_URL.'/admin?act=search_ip&amp;ip='.$arr['ip_via_proxy'].'">'.$arr['ip_via_proxy'].'</a> <a href="'.Vars::$HOME_URL.'/admin/whois?ip='.$arr['ip_via_proxy'].'" title = "WhoIS ip">[?]</a> ';
        echo '| '.$arr['operator'].' | '.$arr['country'].' | '.__('movies').': '.$count_view.'</div>';
        echo '</div>';    
        }
    echo '<div class="phdr">'.__('total_hosts').': '.$count.'</div>';
    if ($count > Vars::$USER_SET['page_size']){
        echo '<div class="topmenu">';
    	echo Functions::displayPagination(Vars::$URI.'?act=hosts&amp;', Vars::$START, $count, Vars::$USER_SET['page_size']) . '</div>';
    	echo '<p><form action="'.Vars::$URI.'" method="get"><input type="hidden" name="act" value="hosts"/><input type="text" name="page" size="2"/><input type="submit" value="' . __('to_page') . ' &gt;&gt;"/></form></p>';}
}else{
 echo '<div class="rmenu">'.__('no_data').'!</div>';
}
?>