<?php

/**
 * @package     JohnCMS
 * @link        http://johncms.com
 * @copyright   Copyright (C) 2008-2012 JohnCMS Community
 * @license     LICENSE.txt (see attached file)
 * @version     VERSION.txt (see attached file)
 * @author      http://johncms.com/about
 */

$file = isset($_GET['file']) ? dirname(dirname(dirname(__FILE__))) . DIRECTORY_SEPARATOR . 'files' . DIRECTORY_SEPARATOR . 'forum' . DIRECTORY_SEPARATOR . htmlspecialchars(urldecode($_GET['file'])) : NULL;

if ($file && file_exists($file)) {
    $imageinfo = GetImageSize($file);
    if ($imageinfo) {
        $im = '';
        $razm = 70;
        $width = $imageinfo[0];
        $height = $imageinfo[1];
        $x_ratio = $razm / $width;
        $y_ratio = $razm / $height;
        if (($width <= $razm) && ($height <= $razm)) {
            $tn_width = $width;
            $tn_height = $height;
        } else if (($x_ratio * $height) < $razm) {
            $tn_height = ceil($x_ratio * $height);
            $tn_width = $razm;
        } else {
            $tn_width = ceil($y_ratio * $width);
            $tn_height = $razm;
        }

        switch ($imageinfo['mime']) {
            case 'image/gif':
                $im = ImageCreateFromGIF($file);
                break;

            case 'image/jpeg':
                $im = ImageCreateFromJPEG($file);
                break;

            case 'image/png':
                $im = ImageCreateFromPNG($file);
                break;
        }

        $im1 = imagecreatetruecolor($tn_width, $tn_height);
        imagecopyresized($im1, $im, 0, 0, 0, 0, $tn_width, $tn_height, $width, $height);
        // Передача изображения в Браузер
        ob_start();
        imageJpeg($im1, NULL, 60);
        ImageDestroy($im);
        imagedestroy($im1);
        header('Content-Type: image/jpeg');
        header('Content-Disposition: inline; filename=thumbinal.jpg');
        header('Content-Length: ' . ob_get_length());
        ob_end_flush();
    }
}