<?php

/**
 * @package     JohnCMS
 * @link        http://johncms.com
 * @copyright   Copyright (C) 2008-2011 JohnCMS Community
 * @license     LICENSE.txt (see attached file)
 * @version     VERSION.txt (see attached file)
 * @author      http://johncms.com/about
 */

defined('_IN_JOHNCMS') or die('Error: restricted access');
$url = Router::getUri(2);

/*
-----------------------------------------------------------------
Перенос файла
-----------------------------------------------------------------
*/
$req_down = DB::PDO()->query("SELECT * FROM `cms_download_files` WHERE `id` = '" . VARS::$ID . "' AND (`type` = 2 OR `type` = 3)  LIMIT 1");
$res_down = $req_down->fetch();
if (!$req_down->rowCount() || !is_file($res_down['dir'] . '/' . $res_down['name'])) {
    echo Functions::displayError(__('not_found_file'), '<a href="' . $url . '">' . __('download_title') . '</a>');
    exit;
}
$do = isset($_GET['do']) ? trim($_GET['do']) : '';
if (Vars::$USER_RIGHTS > 6 ) {
	$catId = isset($_GET['catId']) ? abs(intval($_GET['catId'])) : 0;
	if($catId) {
		$queryDir = DB::PDO()->query("SELECT * FROM `cms_download_category` WHERE `id` = '$catId' LIMIT 1");
		if(!$queryDir->rowCount()) $catId = 0;
	}
	echo '<div class="phdr"><a href="' . $url . '?act=view&amp;id=' . Vars::$ID . '">' . __('back') . '</a> | <b>' . __('transfer_file') . '</b></div>';
	switch($do) {
    	case 'transfer':
        	if($catId) {
        		if($catId == $res_down['refid']) {
        			echo Functions::displayError('<a href="' . $url . '?act=transfer_file&amp;id=' . Vars::$ID . '&amp;catId=' . $catId . '">' . __('back') . '</a>');
        			exit;
        		}
				if(isset($_GET['yes'])) {
					$resDir = $queryDir->fetch();
					$req_file_more = DB::PDO()->query("SELECT * FROM `cms_download_more` WHERE `refid` = '" . VARS::$ID . "'");
                	if ($req_file_more->rowCount()) {
                		while ($res_file_more = $req_file_more->fetch()) {
							copy($res_down['dir'] . '/' . $res_file_more['name'], $resDir['dir'] . '/' . $res_file_more['name']);
                       		unlink($res_down['dir'] . '/' . $res_file_more['name']);
						}
                	}

					$name = $res_down['name'];
					$newFile =  $resDir['dir'] . '/' . $res_down['name'];
                    if(is_file($newFile)) {
                    	$name = time() . '_' .$res_down['name'];
                    	$newFile =  $resDir['dir'] . '/' . $name;

                    }
					copy($res_down['dir'] . '/' . $res_down['name'], $newFile);
                    unlink($res_down['dir'] . '/' . $res_down['name']);
					mysql_query("UPDATE `cms_download_files` SET `name`='" . mysql_real_escape_string($name) . "', `dir`='" . mysql_real_escape_string($resDir['dir']) . "', `refid`='$catId'  WHERE `id`='" . VARS::$ID . "'");
					echo  '<div class="menu"><p>' . __('transfer_file_ok') . '</p></div>' .
                	'<div class="phdr"><a href="' . $url . '?act=recount">' . __('download_recount') . '</a></div>';
               	} else {
                	echo  '<div class="menu"><p><a href="' . $url . '?act=transfer_file&amp;id=' . Vars::$ID . '&amp;catId=' . $catId . '&amp;do=transfer&amp;yes"><b>' . __('transfer_file') . '</b></a></p></div>' .
                	'<div class="phdr"><br /></div>';
				}
			}
			break;
		default:
			$queryCat = DB::PDO()->query("SELECT * FROM `cms_download_category` WHERE `refid` = '$catId'");
			$totalCat = $queryCat->rowCount();
			$i = 0;
    		if($totalCat > 0) {
        		while ($resCat = $queryCat->fetch()) {
                	echo ($i++ % 2) ? '<div class="list2">' : '<div class="list1">';
                	echo Functions::loadModuleImage('folder.png') . '&#160;' .
                	'<a href="' . $url . '?act=transfer_file&amp;id=' . VARS::$ID . '&amp;catId=' . $resCat['id'] . '">' . Validate::checkout($resCat['rus_name']) . '</a>';
                	if($resCat['id'] != $res_down['refid'])
                		echo '<br /><small><a href="' . $url . '?act=transfer_file&amp;id=' . VARS::$ID . '&amp;catId=' . $resCat['id'] . '&amp;do=transfer">' . __('move_this_folder') . '</a></small>';
                	echo '</div>';
				}
			} else
				echo '<div class="rmenu"><p>' . __('list_empty') . '</p></div>';
			echo '<div class="phdr">' . __('total') . ': ' . $totalCat .'</div>';
            if($catId && $catId != $res_down['refid'])
            	echo '<p><div class="func"><a href="' . $url . '?act=transfer_file&amp;id=' . VARS::$ID . '&amp;catId=' . $catId . '&amp;do=transfer">' . __('move_this_folder') . '</a></div></p>';
	}
	echo '<p><a href="' . $url . '?act=view&amp;id=' . Vars::$ID . '">' . __('back') . '</a></p>';
} else {
    header('Location: ' . Vars::$HOME_URL . '404');
}