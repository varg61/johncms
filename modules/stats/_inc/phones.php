<?php

/**
 * @author simba
 * @copyright 2011
 */
 
defined('_IN_JOHNCMS') or die('Restricted access');

echo'<div class="phdr">'.lng('phones_browsers').'</div>';
    $sql = "(SELECT COUNT(DISTINCT `ip`, `browser`) FROM `counter` WHERE `browser` LIKE '%nokia%') UNION ALL
    (SELECT COUNT(DISTINCT `ip`, `browser`) FROM `counter` WHERE `browser` LIKE 'SIE%' OR `browser` LIKE '%benq%') UNION ALL
    (SELECT COUNT(DISTINCT `ip`, `browser`) FROM `counter` WHERE `browser` LIKE '%sony%' OR `browser` LIKE '%sonyeric%') UNION ALL
    (SELECT COUNT(DISTINCT `ip`, `browser`) FROM `counter` WHERE `browser` LIKE '%sec%' OR `browser` LIKE '%samsung%') UNION ALL
    (SELECT COUNT(DISTINCT `ip`, `browser`) FROM `counter` WHERE `browser` LIKE '%lg%') UNION ALL
    (SELECT COUNT(DISTINCT `ip`, `browser`) FROM `counter` WHERE `browser` LIKE '%mot%' OR `browser` LIKE '%motorol%') UNION ALL
    (SELECT COUNT(DISTINCT `ip`, `browser`) FROM `counter` WHERE `browser` LIKE '%nec%') UNION ALL
    (SELECT COUNT(DISTINCT `ip`, `browser`) FROM `counter` WHERE `browser` LIKE '%philips%') UNION ALL
    (SELECT COUNT(DISTINCT `ip`, `browser`) FROM `counter` WHERE `browser` LIKE '%pantech%') UNION ALL
    (SELECT COUNT(DISTINCT `ip`, `browser`) FROM `counter` WHERE `browser` LIKE '%sagem%') UNION ALL
    (SELECT COUNT(DISTINCT `ip`, `browser`) FROM `counter` WHERE `browser` LIKE '%fly%') UNION ALL
    (SELECT COUNT(DISTINCT `ip`, `browser`) FROM `counter` WHERE `browser` LIKE '%panasonic%') UNION ALL
    (SELECT COUNT(DISTINCT `ip`, `browser`) FROM `counter` WHERE `browser` LIKE '%opera mini%') UNION ALL
    (SELECT COUNT(DISTINCT `ip`, `browser`) FROM `counter` WHERE `browser` LIKE '%windows%' OR `browser` LIKE '%linux%')";
    $query = mysql_query($sql);
    $phones = array();

    while($result_array = mysql_fetch_array($query)) {
        $phones[] = $result_array[0];
        }    

  	echo'<div class="menu"><img src="../files/temp/stat_model.png" alt="loading..."/></div>';
    echo '<div class="gmenu"><h3>'.lng('details').'</h3>';
    
        $col = array();
        $name = array();
		if($phones[0] > 0){
		  $col[] = $phones[0];
          $name[] = 'Nokia';
          echo'<li> <a href="?act=phone&amp;model=Nokia">Nokia</a> ('.$phones[0].')</li>'; }
		if($phones[1] > 0){
		  $col[] = $phones[1];
		    $name[] = 'Siemens';
			echo'<li> <a href="?act=phone&amp;model=Siemens">Siemens</a> ('.$phones[1].')</li>'; }
		if($phones[2] > 0){
		  $col[] = $phones[2];
		    $name[] = 'Sony Ericsson';
			echo'<li> <a href="?act=phone&amp;model=SE">Sony Ericsson</a> ('.$phones[2].')</li>'; }
		if($phones[3] > 0){
		  $col[] = $phones[3];
		    $name[] = 'Samsung';
			echo'<li> <a href="?act=phone&amp;model=Samsung">Samsung</a> ('.$phones[3].')</li>'; }
		if($phones[4] > 0){
		  $col[] = $phones[4];
		    $name[] = 'LG';
			echo'<li> <a href="?act=phone&amp;model=LG">LG</a> ('.$phones[4].')</li>'; }
		if($phones[5] > 0){
		  $col[] = $phones[5];
		    $name[] = 'Motorola';
			echo'<li> <a href="?act=phone&amp;model=Motorola">Motorola</a> ('.$phones[5].')</li>'; }
		if($phones[6] > 0){
		  $col[] = $phones[6];
		    $name[] = 'NEC';
			echo'<li> <a href="?act=phone&amp;model=NEC">NEC</a> ('.$phones[6].')</li>'; }
        if($phones[7] > 0){
            $col[] = $phones[7];
		    $name[] = 'Philips';
			echo'<li> <a href="?act=phone&amp;model=Philips">Philips</a> ('.$phones[7].')</li>'; }
		if($phones[8] > 0){
		  $col[] = $phones[8];
		    $name[] = 'Pantech';
			echo'<li> <a href="?act=phone&amp;model=Pantech">Pantech</a> ('.$phones[8].')</li>'; }
        if($phones[9] > 0){
            $col[] = $phones[9];
		    $name[] = 'Sagem';
			echo'<li> <a href="?act=phone&amp;model=Sagem">SAGEM</a> ('.$phones[9].')</li>'; }
		if($phones[10] > 0){
		  $col[] = $phones[10];
		    $name[] = 'Fly';
			echo'<li> <a href="?act=phone&amp;model=Fly">Fly</a> ('.$phones[10].')</li>'; }
		if($phones[11] > 0){
		  $col[] = $phones[11];
		    $name[] = 'Panasonic';
			echo'<li> <a href="?act=phone&amp;model=Panasonic">Panasonic</a> ('.$phones[11].')</li>'; }
        if($phones[12] > 0){
            $col[] = $phones[12];
		    $name[] = 'Opera Mini';
			echo'<li> <a href="?act=phone&amp;model=Opera">Opera Mini</a> ('.$phones[12].')</li>'; }
		if($phones[13] > 0){
		  $col[] = $phones[13];
		    $name[] = lng('computer');
			echo'<li> <a href="?act=phone&amp;model=komp">'.lng('computer').'</a> ('.$phones[13].')</li>'; }
    
    echo '</div>';
    ////// График //////

 	// Dataset definition
 	$DataSet = new pData;

 	$DataSet->AddPoint($col,"Serie1");
 	$DataSet->AddPoint($name,"Serie2");
 	$DataSet->AddAllSeries();
 	$DataSet->SetAbsciseLabelSerie("Serie2");

 	// Initialise the graph
 	$Test = new pChart(235,161);
 	$Test->setFontProperties(MODPATH . Vars::$MODULE . DIRECTORY_SEPARATOR . 'Fonts/tahoma.ttf',6);
 	$Test->drawFilledRoundedRectangle(7,7,235,193,5,240,240,240);
 	$Test->drawRoundedRectangle(5,5,234,160,5,20,230,230);

 	// Draw the pie chart
 	$Test->AntialiasQuality = 0;
 	$Test->setShadowProperties(2,2,200,200,200);
 	$Test->drawFlatPieGraphWithShadow($DataSet->GetData(),$DataSet->GetDataDescription(),80,80,40,PIE_PERCENTAGE,8);
 	$Test->clearShadow();
	$Test->drawPieLegend(150,6,$DataSet->GetData(),$DataSet->GetDataDescription(),250,250,250);
	$Test->Render('files/temp/stat_model.png');
 
?>