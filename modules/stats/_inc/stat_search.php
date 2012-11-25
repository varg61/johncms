<?php

/**
 * @author simba
 * @copyright 2012
 */
defined('_IN_JOHNCMS') or die('Restricted access');
$where_time = strtotime(date("d F y", time()));
$sql = "
(SELECT COUNT(*) FROM `stat_robots` WHERE `date` > '".$where_time."' AND `engine` LIKE '%yandex%') UNION ALL
(SELECT COUNT(*) FROM `stat_robots` WHERE `date` > '".$where_time."' AND `engine` LIKE '%mail%') UNION ALL
(SELECT COUNT(*) FROM `stat_robots` WHERE `date` > '".$where_time."' AND `engine` LIKE '%rambler%') UNION ALL
(SELECT COUNT(*) FROM `stat_robots` WHERE `date` > '".$where_time."' AND `engine` LIKE '%google%') UNION ALL
(SELECT COUNT(*) FROM `stat_robots` WHERE `date` > '".$where_time."' AND `engine` LIKE '%gogo%') UNION ALL
(SELECT COUNT(*) FROM `stat_robots` WHERE `date` > '".$where_time."' AND `engine` LIKE '%yahoo%') UNION ALL
(SELECT COUNT(*) FROM `stat_robots` WHERE `date` > '".$where_time."' AND `engine` LIKE '%bing%') UNION ALL
(SELECT COUNT(*) FROM `stat_robots` WHERE `date` > '".$where_time."' AND `engine` LIKE '%nigma%') UNION ALL
(SELECT COUNT(*) FROM `stat_robots` WHERE `date` > '".$where_time."' AND `engine` LIKE '%qip%') UNION ALL
(SELECT COUNT(*) FROM `stat_robots` WHERE `date` > '".$where_time."' AND `engine` LIKE '%aport%') UNION ALL
(SELECT COUNT(*) FROM `stat_robots` WHERE `date` > '".$where_time."')";

$query = mysql_query($sql);
$count_query = array();
while($result_array = mysql_fetch_array($query)) {
        $count_query[] = $result_array[0];
        }
////// Выводим ссылки и количество переходов ///
echo'<div class="phdr">'.__('from_search_engines1').'</div>';
echo'<div class="menu">'.Functions::loadModuleImage('yandex.png') .' <a href="?act=search_engine&amp;sengine=yandex">Yandex.ru</a> ('.$count_query[0].')<br/>';
echo Functions::loadModuleImage('mailru.png') .' <a href="?act=search_engine&amp;sengine=mail">Mail.ru</a> ('.$count_query[1].')<br/>';
echo Functions::loadModuleImage('rambler.png') .' <a href="?act=search_engine&amp;sengine=rambler">Rambler.ru</a> ('.$count_query[2].')<br/>';
echo Functions::loadModuleImage('google.png') .' <a href="?act=search_engine&amp;sengine=google">Google.ru</a> ('.$count_query[3].')<br/>';
echo Functions::loadModuleImage('gogo.png') .' <a href="?act=search_engine&amp;sengine=gogo">Gogo.ru</a> ('.$count_query[4].')<br/>';
echo Functions::loadModuleImage('yahoo.png') .' <a href="?act=search_engine&amp;sengine=yahoo">Yahoo.ru</a> ('.$count_query[5].')<br/>';
echo Functions::loadModuleImage('bing.png') .' <a href="?act=search_engine&amp;sengine=bing">Bing.ru</a> ('.$count_query[6].')<br/>';
echo Functions::loadModuleImage('nigma.png') .' <a href="?act=search_engine&amp;sengine=nigma">Nigma.ru</a> ('.$count_query[7].')<br/>';
echo Functions::loadModuleImage('qip.png') .' <a href="?act=search_engine&amp;sengine=qip">Search.QIP.ru</a> ('.$count_query[8].')<br/>';
echo Functions::loadModuleImage('aport.png') .' <a href="?act=search_engine&amp;sengine=aport">Aport.ru</a> ('.$count_query[9].')</div>';
echo '<div class="bmenu">' . Functions::loadImage('all1.png') .' <a href="?act=search_engine&amp;sengine=all">'.__('total').'</a> ('.$count_query[10].')</div>';