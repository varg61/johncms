<?php

/**
 * @author simba
 * @copyright 2011
 */
defined('_IN_JOHNCMS') or die('Restricted access');

// Если нужны графики за неделю и по поисковикам, присваиваем переменной $tpl->grafics - TRUE вместо FALSE.

$tpl = Template::getInstance();

$tpl->grafics = TRUE;

$days = isset($_GET['days']) ? intval($_GET['days']) : 1;
$tpl->start_time_stat = strtotime(date("d F y", time() - $days * 86400));
$stop_stat_time = $tpl->start_time_stat+86400;
$tpl->count_stat = mysql_result(mysql_query("SELECT COUNT(*) FROM `countersall` WHERE `date` > '".$tpl->start_time_stat."' AND `date` < '".$stop_stat_time."';"), 0);

if($tpl->count_stat > 0){
$tpl->day_array = mysql_fetch_assoc(mysql_query("SELECT * FROM `countersall` WHERE `date` > '".$tpl->start_time_stat."' AND `date` < '".$stop_stat_time."' LIMIT 1;"));
$tpl->searchc = mysql_fetch_row(mysql_query("SELECT SUM(hits), sum(host), sum(yandex), sum(rambler), sum(google), sum(mail), sum(gogo), sum(yahoo), sum(bing), sum(nigma), sum(qip), sum(aport) FROM countersall"));

if($tpl->grafics){
///////////////////////////////
/// График хостов за неделю ///
///////////////////////////////
$filetime = date("d.m.y", @filemtime('files/temp/stat_we_host.png'));
$daytime = date("d.m.y", time());

if(!is_file('files/temp/we_se.png') || $filetime != $daytime){
$q = time() - 604800;
$req = mysql_query("SELECT * FROM `countersall` WHERE `date` > '".$q."' ORDER BY `date` ASC LIMIT 7;");
$a = array(); // Массив с хитами
$b = array(); // Массив с хостами
$c = array(); // Массив с датами
while($arr = mysql_fetch_array($req)){
$a[] = $arr['hits']; // Добавляем хит
$b[] = $arr['host']; // Добавляем хост
$c[] = $arr['date']; // Добавляем дату
}
$DataSet = new pData;
$DataSet->AddPoint($a,"Serie1"); // Передаём массив с хитами
$DataSet->AddPoint($b,"Serie2"); // Передаём массив с хостами
$DataSet->AddPoint($c,"Serie3"); // Передаём массив с датами
$DataSet->AddSerie("Serie1");
$DataSet->AddSerie("Serie2");
$DataSet->SetAbsciseLabelSerie("Serie3");
$DataSet->SetSerieName("Хиты","Serie1"); // Пояснительные надписи
$DataSet->SetSerieName("Хосты","Serie2");
//$DataSet->SetSerieName("Дата","Serie3");
$DataSet->SetXAxisFormat("date"); // Как обрабатывать массив с датами (в виде даты)

$Test = new pChart(170,140); // Размер графика
$Test->setFontProperties(MODPATH . Vars::$MODULE . DIRECTORY_SEPARATOR . 'Fonts/tahoma.ttf',5); // Шрифт боковых надписей
$Test->setGraphArea(30,10,164,110); // Положение самого графика
$Test->drawFilledRoundedRectangle(3,3,167,136,5,240,240,240); // Обводка
$Test->drawRoundedRectangle(1,1,169,138,5,138,230,230);  // Обводка
$Test->drawGraphArea(252,252,252,TRUE); // Цвет фона на котором расположен график
$Test->setDateFormat("d"); // Формат вывода даты по оси Х
$Test->drawScale($DataSet->GetData(),$DataSet->GetDataDescription(),SCALE_NORMAL,150,150,150,TRUE,0,2);
$Test->drawGrid(4,TRUE,230,230,230,50);
$Test->drawLineGraph($DataSet->GetData(),$DataSet->GetDataDescription());
$Test->drawPlotGraph($DataSet->GetData(),$DataSet->GetDataDescription(),3,2,255,255,255);

$Test->setFontProperties(MODPATH . Vars::$MODULE . DIRECTORY_SEPARATOR . 'Fonts/tahoma.ttf',8); // Шрифт заголовка
$Test->drawLegend(31,10,$DataSet->GetDataDescription(),230,255,255, -1,-1,-1, TRUE); // Подложка с пояснениями к линиям
$Test->drawTitle(1,9,"За неделю",50,50,50,195); // Заголовок графика
$Test->Render('files/temp/stat_we_host.png'); //Место хранения картинки

}

//////////////////////////
/// График поисковиков ///
//////////////////////////
$filetime = date("d.m.y", @filemtime('files/temp/stat_we_se.png'));
$daytime = date("d.m.y", time());
if(!is_file('files/temp/stat_we_se.png') || $filetime != $daytime){
// Dataset definition
$DataSet = new pData;
$DataSet->AddPoint(array($tpl->searchc[2],$tpl->searchc[3],$tpl->searchc[4],$tpl->searchc[5],$tpl->searchc[6],$tpl->searchc[7],$tpl->searchc[8],$tpl->searchc[9],$tpl->searchc[10],$tpl->searchc[11]),"Serie1");
$DataSet->AddPoint(array("Яндекс","Рамблер","Google","Mail","Gogo", "Yahoo", "Bing", "Nigma", "QIP", "Апорт"),"Serie2");
$DataSet->AddAllSeries();
$DataSet->SetAbsciseLabelSerie("Serie2");
// Initialise the graph
$Test = new pChart(235,161);
$Test->setFontProperties(MODPATH . Vars::$MODULE . DIRECTORY_SEPARATOR . 'Fonts/tahoma.ttf',7);
$Test->drawFilledRoundedRectangle(7,7,235,193,5,240,240,240);
$Test->drawRoundedRectangle(5,5,234,160,5,20,230,230);
// Draw the pie chart
$Test->AntialiasQuality = 0;
$Test->setShadowProperties(2,2,200,200,200);
$Test->drawFlatPieGraphWithShadow($DataSet->GetData(),$DataSet->GetDataDescription(),70,80,50,PIE_PERCENTAGE,8);
$Test->clearShadow();
$Test->drawPieLegend(158,8,$DataSet->GetData(),$DataSet->GetDataDescription(),250,250,250);
$Test->Render('files/temp/stat_we_se.png');
}
}
}

++$days;
$tpl->days = $days;
$tpl->contents = $tpl->includeTpl('allstats');