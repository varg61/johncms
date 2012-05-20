<?php

/**
 * @package     JohnCMS
 * @link        http://johncms.com
 * @copyright   Copyright (C) 2008-2011 JohnCMS Community
 * @license     LICENSE.txt (see attached file)
 * @version     VERSION.txt (see attached file)
 * @author      http://johncms.com/about
 */

if (!isset($_GET['img']))
    exit;
function format($name)
{
    $f1 = strrpos($name, ".");
    $f2 = substr($name, $f1 + 1, 999);
    $fname = strtolower($f2);
    return $fname;
}

$copyright = '';
$type = isset($_GET['type']) ? abs(intval($_GET['type'])) : 0;
$image = htmlspecialchars(rawurldecode($_GET['img']));
$image = dirname(dirname(dirname(dirname(__FILE__)))) . DIRECTORY_SEPARATOR . strtr($image, array('../' => '', './'  => '_'));
if ($image && file_exists($image)) {
    $att_ext = strtolower(format($image));
    $pic_ext = array('gif', 'jpg', 'jpeg', 'png');
    if (in_array($att_ext, $pic_ext)) {
        $info_file = GetImageSize($image);
        $w_or = $info_file[0];
        $h_or = $info_file[1];
        $type_file = $info_file['mime'];
        switch ($type) {
            case 3:
                $w = $w_or;
                $h = $h_or;
                break;

            case 1:
                $w = 40;
                $h = 40;
                break;

            default:
                if ($w_or > 240) {
                    $w = 240;
                    $h = ceil($h_or / ($w_or / 240));
                } else {
                    $w = $w_or;
                    $h = $h_or;
                }
        }
        switch ($type_file) {
            case "image/gif":
                $image_file = ImageCreateFromGIF($image);
                break;

            case "image/jpg":
                $image_file = ImageCreateFromJPEG($image);
                break;

            case "image/jpeg":
                $image_file = ImageCreateFromJPEG($image);
                break;

            case "image/png":
                $image_file = ImageCreateFromPNG($image);
                break;
            default:
                exit;
        }
        $two_image = imagecreatetruecolor($w, $h);
        imagecopyresampled($two_image, $image_file, 0, 0, 0, 0, $w, $h, $w_or, $h_or);
        if ($w > 30 && $h > 30) {
            if ($type != 1 && $w > 100 && $h > 50) {
                $file_copyright = 'copyright_2.png';
                $w_copyright = 100;
                $h_copyright = 20;
            } else {
                $file_copyright = 'copyright.png';
                $w_copyright = 16;
                $h_copyright = 16;
            }
            $copyright = ImageCreateFromPng($file_copyright);
            imagecopy($two_image, $copyright, 1, ($h - $h_copyright - 2), 0, 0, $w_copyright, $h_copyright);
        }
        ob_start();
        imageJpeg($two_image, NULL, 80);
        ImageDestroy($image_file);
        imagedestroy($two_image);
        if ($copyright)
            imagedestroy($copyright);
        header("Content-Type: image/jpeg");
        header('Content-Disposition: inline; filename=preview.jpg');
        header('Content-Length: ' . ob_get_length());
        ob_end_flush();
    }
}