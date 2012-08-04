<?php

/**
 * @author simba
 * @copyright 2012
 */

if(isset($_GET['sday']))
    $_SESSION['sday'] = $_GET['sday'];
if(isset($_GET['sengine']))
    $_SESSION['sengine'] = $_GET['sengine'];

$day = isset($_SESSION['sday']) ? $_SESSION['sday'] : '';
$engine = isset($_SESSION['sengine']) ? $_SESSION['sengine'] : '';

$sql = '';
$n = lng('all');
/////// Выбираем поисковую машину ///////
switch ($engine){
    case 'google':
    $sql = " AND `engine` LIKE '%google%'";
    $n = 'www.google.ru';
    break;
    case 'mail':
    $sql = " AND `engine` LIKE '%mail%'";
    $n = 'mail.ru';
    break;
    case 'rambler':
    $sql = " AND `engine` LIKE '%rambler%'";
    $n='rambler.ru';
    break;
    case 'yandex':
    $sql = " AND `engine` LIKE '%yandex%'";
    $n='yandex.ru';
    break;
    case 'bing':
    $sql = " AND `engine` LIKE '%bing%'";
    $n='bing.com';
    break;
    case 'nigma':
    $sql = " AND `engine` LIKE '%nigma%'";
    $n='nigma.ru';
    break;
    case 'qip':
    $sql = " AND `engine` LIKE '%qip%'";
    $n='search.qip.ru';
    break;
    case 'aport':
    $sql = " AND `engine` LIKE '%aport%'";
    $n='aport.ru';
    break;
    case 'gogo':
    $sql = " AND `engine` LIKE '%gogo%'";
    $n='gogo.ru';
    break;
    case 'yahoo':
    $sql = " AND `engine` LIKE '%yahoo%'";
    $n='yahoo.ru';
    break;
    }
echo'<div class="phdr">'.lng('statistics_on').' '.$n.'</div>';
/////// Вычисляем время /////////
$time = strtotime(date("d F y", time()));
$time1 = $time-86400;
$time7 = $time-604800;
///// Выбираем нужный период ////
switch ($day){
         ///// Весь период /////
         case "all":
         $sql = str_replace('AND', 'WHERE', $sql);
         $total = mysql_result(mysql_query("SELECT COUNT(*) FROM `stat_robots` ".$sql.""), 0);
         if($total > 0)
         $req = mysql_query("SELECT * FROM `stat_robots` ".$sql." ORDER BY `date` DESC ". Vars::db_pagination());
         break;
         ///// Семь дней /////
         case "seven":
         $total = mysql_result(mysql_query("SELECT COUNT(*) FROM `stat_robots` WHERE `date` > '".$time7."' and `date` < '".$time."' ".$sql.""), 0);
         if($total > 0)
         $req = mysql_query("SELECT * FROM `stat_robots` WHERE `date` > '".$time7."' and `date` < '".$time."'".$sql." ORDER BY `date` DESC " . Vars::db_pagination());
         break;
         ///// За прошедший день (вчера) /////
         case "two":
         $total = mysql_result(mysql_query("SELECT COUNT(*) FROM `stat_robots` WHERE `date` > '".$time1."' and `date` < '".$time."'".$sql.""), 0);
         if($total > 0)
         $req = mysql_query("SELECT * FROM `stat_robots` WHERE `date` > '".$time1."' and `date` < '".$time."'".$sql." ORDER BY `date` DESC ". Vars::db_pagination());
         break;
         /////// Стандарт за сутки /////
         default:
         $total = mysql_result(mysql_query("SELECT COUNT(*) FROM `stat_robots` WHERE `date` > '".$time."'".$sql.""), 0);
         if($total > 0)
         $req = mysql_query("SELECT * FROM `stat_robots` WHERE `date` > '".$time."'".$sql." ORDER BY `date` DESC ". Vars::db_pagination());

         break;
         }
////// Выводим ссылки для выбора периода ////////
         echo'<div class="menu">'.lng('period').': ';
if($day !== 'seven' && $day !== 'two' && $day !== 'all'){
echo'<b>'.lng('today').'</b> | <a href="?act=search_engine&amp;sday=two">'.lng('yesterday').'</a> | <a href="?act=search_engine&amp;sday=seven">'.lng('week').'</a> | <a href="?act=search_engine&amp;sday=all">'.lng('all_along').'</a>';         
}elseif($day == 'two'){
echo'<a href="?act=search_engine&amp;sday=one">'.lng('today').'</a> | <b>'.lng('yesterday').'</b> | <a href="?act=search_engine&amp;sday=seven">'.lng('week').'</a> | <a href="?act=search_engine&amp;sday=all">'.lng('all_along').'</a>';    
}elseif($day == 'seven'){
echo'<a href="?act=search_engine&amp;sday=one">'.lng('today').'</a> | <a href="?act=search_engine&amp;sday=two">'.lng('yesterday').'</a> | <b>'.lng('week').'</b> | <a href="?act=search_engine&amp;sday=all">'.lng('all_along').'</a>';    
}elseif($day == 'all'){
echo'<a href="?act=search_engine&amp;sday=one">'.lng('today').'</a> | <a href="?act=search_engine&amp;sday=two">'.lng('yesterday').'</a> | <a href="?act=search_engine&amp;sday=seven">'.lng('week').'</a> | <b>'.lng('all_along').'</b>';    
}
echo'</div>';

//////// Выводим полученный результат или сообщение об отсутствии ///////

if($total > 0){
$i = 0;
while ($arr = mysql_fetch_array($req)){
    echo ($i % 2) ? '<div class="list1">' : '<div class="list2">';
    ++$i;
    echo '<a href="'.Validate::filterString($arr['url']).'">'.Validate::filterString($arr['query']).'</a> ['.Functions::displayDate($arr['date']).']<br/>
    <small>IP: <a href="'.Vars::$HOME_URL.'/admin?act=search_ip&amp;ip='.long2ip($arr['ip']).'">'.long2ip($arr['ip']).'</a>';
    if($day !== 'seven' && $day !== 'two'){ echo' '.lng('movies').' '.lng('today').': '.$arr['today']; }
    echo' '.lng('total').': '.$arr['count'];
    echo'<br/>UA: '.$arr['ua'].'</small>
    </div>';
}

        echo '<div class="phdr">'.lng('total').': ' . $total . '</div>';
		if ($total > Vars::$USER_SET['page_size']){
    	echo '<div class="topmenu">';
    	echo Functions::displayPagination('?act=search_engine&amp;', Vars::$START, $total, Vars::$USER_SET['page_size']) . '</div>';
    	echo '<p><form action="'.Vars::$URI.'" method="get"><input type="hidden" name="act" value="search_engine"/><input type="text" name="page" size="2"/><input type="submit" value="'.lng('to_page').' &gt;&gt;"/></form></p>';}
 }else{
    echo'<div class="rmenu">'.lng('no_data').'!</div>';
 }      
   echo'<div class="menu"><a href="?act=stat_search">'.lng('back').'</a></div>';     

?>