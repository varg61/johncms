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
/*
-----------------------------------------------------------------
Скачка изображения в особом размере
-----------------------------------------------------------------
*/
$req_down = mysql_query("SELECT * FROM `cms_download_files` WHERE `id` = '" . Vars::$ID . "' AND (`type` = 2 OR `type` = 3)  LIMIT 1");
$res_down = mysql_fetch_assoc($req_down);
$format_file = functions::format($res_down['name']);
$pic_ext = array ('gif', 'jpg', 'jpeg', 'png');
$array = array('101x80', '128x128', '128x160', '176x176', '176x208', '176x220', '208x208', '208x320', '240x266', '240x320', '240x432', '352x416', '480x800');
$size_img = isset($_GET['img_size']) ? abs(intval($_GET['img_size'])) : 0;
$proportion = isset($_GET['proportion']) ? abs(intval($_GET['proportion'])) : 0;
$val = isset($_GET['val']) ? abs(intval($_GET['val'])) : 100;
if ($val < 50 || $val > 100) $val = 100;
if (mysql_num_rows($req_down) == 0 || !is_file($res_down['dir'] . '/' . $res_down['name']) || !in_array($format_file, $pic_ext) || ($res_down['type'] == 3 && Vars::$USER_RIGHTS < 6 && Vars::$USER_RIGHTS != 4) || empty($array[$size_img])) {
    echo Functions::displayError(__('not_found_file'), '<a href="' . Router::getUri(2) . '">' . __('download_title') . '</a>');
    exit;
}
$sizs = GetImageSize($res_down['dir'] . '/' . $res_down['name']);
$explode = explode('x', $array[$size_img]);
$width = $sizs[0];
$height = $sizs[1];
if ($proportion) {
    $x_ratio = $explode[0] / $width;
    $y_ratio = $explode[0] / $height;
    if (($width <= $explode[0]) && ($height <= $explode[0])) {
        $tn_width = $width;
        $tn_height = $height;
    } else if (($x_ratio * $height) < $explode[0]) {
        $tn_height = ceil($x_ratio * $height);
        $tn_width = $explode[0];
    } else {
        $tn_width = ceil($y_ratio * $width);
        $tn_height = $explode[0];
    }
} else {
    $tn_height = $explode[1];
    $tn_width = $explode[0];
}
switch ($format_file) {
    case "gif":
        $image_create = ImageCreateFromGIF($res_down['dir'] . '/' . $res_down['name']);
        break;

    case "jpg":
        $image_create = ImageCreateFromJPEG($res_down['dir'] . '/' . $res_down['name']);
        break;

    case "jpeg":
        $image_create = ImageCreateFromJPEG($res_down['dir'] . '/' . $res_down['name']);
        break;

    case "png":
        $image_create = ImageCreateFromPNG($res_down['dir'] . '/' . $res_down['name']);
        break;
}



if (!isset($_SESSION['down_' . Vars::$ID])) {
    mysql_query("UPDATE `cms_download_files` SET `field`=`field`+1 WHERE `id`='" . Vars::$ID . "'");
    $_SESSION['down_' . Vars::$ID] = 1;
}
$image = imagecreatetruecolor($tn_width, $tn_height);
imagecopyresized($image, $image_create, 0, 0, 0, 0, $tn_width, $tn_height, $width, $height);
$tpl = Template::getInstance();
$tpl->template = false;
ob_end_clean();
ob_start();
imageJpeg($image, NULL, $val);
ImageDestroy($image);
imagedestroy($image_create);
header('Content-Type: image/jpeg');
header('Content-Disposition: inline; filename=image.jpg');
header('Content-Length: ' . ob_get_length());
flush();