<?php

/**
 * @package     JohnCMS
 * @link        http://johncms.com
 * @copyright   Copyright (C) 2008-2012 JohnCMS Community
 * @license     LICENSE.txt (see attached file)
 * @version     VERSION.txt (see attached file)
 * @author      http://johncms.com/about
 *
 * Данный модуль построен на основе доработанного алгоритма KCAPTCHA v.1.2.6
 * KCAPTCHA PROJECT, Copyright by Kruglov Sergei, 2006, 2007, 2008
 * www.captcha.ru, www.kruglov.ru
 */

class Captcha
{
    /*
    -----------------------------------------------------------------
    Настройки модуля
    -----------------------------------------------------------------
    */
    private static $width = 100;                                               // Ширина картинки
    private static $height = 50;                                               // Высота картинки
    private static $lenght_min = 3;                                            // Минимальное число символов в CAPTCHA
    private static $lenght_max = 5;                                            // Максимальное число символов в CAPTCHA
    private static $alphabet = '0123456789abcdefghijklmnopqrstuvwxyz';         // Доступный алфавит
    private static $allowed_symbols = '23456789abcdeghkmnpqsuvxyz';            // Используемые символы. Не ставить похожие! (o=0, 1=l, i=j, t=f)

    /*
    -----------------------------------------------------------------
    Показываем картинку CAPTCHA и форму ввода кода
    -----------------------------------------------------------------
    */
    public static function display($input_field = false)
    {
        $captcha_path = FILEPATH . 'temp' . DIRECTORY_SEPARATOR;
        $img_file = md5(mt_rand(0, 1000) . microtime(true)) . '.png';

        // Удаляем старые картинки
        $garbage = glob($captcha_path . '*.png');
        foreach ($garbage as $val) {
            if (filemtime($val) < (time() - 10)) {
                unlink($val);
            }
        }

        // Генерируем CAPTCHA
        $code = self::render($captcha_path . $img_file);
        if (empty($code)) {
            $_SESSION['captcha'] = mt_rand(0, 1000);
            return 'ERROR: Captcha';
        }

        // Показываем картинку CAPTCHA
        $_SESSION['captcha'] = $code;
        $input = $input_field ? '<br /><input type="text" id="captcha" size="' . self::$lenght_max . '" maxlength="' . self::$lenght_max . '"  name="captcha"/>&#160;' . lng('captcha') : '';
        return '<img width="' . self::$width . '" height="' . self::$height . '" alt="' . lng('captcha_help') . '" src="' . Vars::$HOME_URL . '/files/temp/' . $img_file . '" border="1"/>' . $input;
    }

    /*
    -----------------------------------------------------------------
    Проверка введенного пользователем кода
    -----------------------------------------------------------------
    */
    public static function check()
    {
        $check = false;
        $user_code = isset($_POST['captcha']) ? trim($_POST['captcha']) : '';
        if (isset($_SESSION['captcha'])
            && strlen($user_code) >= self::$lenght_min
            && $_SESSION['captcha'] == mb_strtolower($user_code)
        ) {
            $check = true;
        }
        unset($_SESSION['captcha']);
        return $check;
    }

    /*
    -----------------------------------------------------------------
    Генерируем картинку CAPTCHA
    -----------------------------------------------------------------
    */
    private static function render($img_file)
    {
        $key = '';
        $length = mt_rand(self::$lenght_min, self::$lenght_max);
        $allowed_symbols = str_split(self::$allowed_symbols);
        $allowed_count = count($allowed_symbols) - 1;
        $fluctuation_amplitude = 5;
        $no_spaces = true;
        $foreground_color = array(
            mt_rand(0, 100),
            mt_rand(0, 100),
            mt_rand(0, 100)
        );
        $background_color = array(
            mt_rand(200, 255),
            mt_rand(200, 255),
            mt_rand(200, 255)
        );

        $fonts_dir = ROOTPATH . 'images' . DIRECTORY_SEPARATOR . 'captcha' . DIRECTORY_SEPARATOR;
        $fonts = glob($fonts_dir . '*.png');

        do {
            // Генерируем случайный код
            for ($i = 0; $i < $length; $i++) {
                $key .= $allowed_symbols[mt_rand(0, $allowed_count)];
            }

            $font_file = $fonts[mt_rand(0, count($fonts) - 1)];
            $font = imagecreatefrompng($font_file);
            imagealphablending($font, true);
            $fontfile_width = imagesx($font);
            $fontfile_height = imagesy($font) - 1;
            $font_metrics = array();
            $symbol = 0;
            $reading_symbol = false;
            // Загрузка шрифтов
            $alphabet_length = strlen(self::$alphabet);
            for ($i = 0; $i < $fontfile_width && $symbol < $alphabet_length; $i++) {
                $transparent = (imagecolorat($font, $i, 0) >> 24) == 127;
                if (!$reading_symbol && !$transparent) {
                    $font_metrics[self::$alphabet{$symbol}] = array('start' => $i);
                    $reading_symbol = true;
                    continue;
                }
                if ($reading_symbol && $transparent) {
                    $font_metrics[self::$alphabet{$symbol}]['end'] = $i;
                    $reading_symbol = false;
                    $symbol++;
                    continue;
                }
            }
            $img = imagecreatetruecolor(self::$width, self::$height);
            imagealphablending($img, true);
            $white = imagecolorallocate($img, 255, 255, 255);
            imagefilledrectangle($img, 0, 0, self::$width - 1, self::$height - 1, $white);

            // Формируем картинку кода CAPTCHA
            $x = 1;
            for ($i = 0; $i < $length; $i++) {
                $m = $font_metrics[$key{$i}];
                $y = mt_rand(-$fluctuation_amplitude, $fluctuation_amplitude) + (self::$height - $fontfile_height) / 2 + 2;
                if ($no_spaces) {
                    $shift = 0;
                    if ($i > 0) {
                        $shift = 10000;
                        for ($sy = 7; $sy < $fontfile_height - 20; $sy += 1) {
                            for ($sx = $m['start'] - 1; $sx < $m['end']; $sx += 1) {
                                $rgb = imagecolorat($font, $sx, $sy);
                                $opacity = $rgb >> 24;
                                if ($opacity < 127) {
                                    $left = $sx - $m['start'] + $x;
                                    $py = $sy + $y;
                                    if ($py > self::$height)
                                        break;
                                    for ($px = min($left, self::$width - 1); $px > $left - 12 && $px >= 0; $px -= 1) {
                                        $color = imagecolorat($img, $px, $py) & 0xff;
                                        if ($color + $opacity < 190) {
                                            if ($shift > $left - $px) {
                                                $shift = $left - $px;
                                            }
                                            break;
                                        }
                                    }
                                    break;
                                }
                            }
                        }
                        if ($shift == 10000) {
                            $shift = mt_rand(4, 6);
                        }
                    }
                } else {
                    $shift = 1;
                }
                imagecopy($img, $font, $x - $shift, $y, $m['start'], 1, $m['end'] - $m['start'], $fontfile_height);
                $x += $m['end'] - $m['start'] - $shift;
            }
        } while ($x >= (self::$width - 10));
        $center = $x / 2;

        $img2 = imagecreatetruecolor(self::$width, self::$height);
        $foreground = imagecolorallocate($img2, $foreground_color[0], $foreground_color[1], $foreground_color[2]);
        $background = imagecolorallocate($img2, $background_color[0], $background_color[1], $background_color[2]);
        imagefilledrectangle($img2, 0, 0, self::$width - 1, self::$height - 1, $background);
        imagefilledrectangle($img2, 0, self::$height, self::$width - 1, self::$height + 12, $foreground);

        $rand1 = mt_rand(750000, 1200000) / 10000000;
        $rand2 = mt_rand(750000, 1200000) / 10000000;
        $rand3 = mt_rand(750000, 1200000) / 10000000;
        $rand4 = mt_rand(750000, 1200000) / 10000000;

        $rand5 = mt_rand(0, 31415926) / 10000000;
        $rand6 = mt_rand(0, 31415926) / 10000000;
        $rand7 = mt_rand(0, 31415926) / 10000000;
        $rand8 = mt_rand(0, 31415926) / 10000000;

        $rand9 = mt_rand(330, 420) / 110;
        $rand10 = mt_rand(330, 450) / 110;

        // Вносим волновые искажения
        for ($x = 0; $x < self::$width; $x++) {
            for ($y = 0; $y < self::$height; $y++) {
                $sx = $x + (sin($x * $rand1 + $rand5) + sin($y * $rand3 + $rand6)) * $rand9 - self::$width / 2 + $center + 1;
                $sy = $y + (sin($x * $rand2 + $rand7) + sin($y * $rand4 + $rand8)) * $rand10;
                if ($sx < 0 || $sy < 0 || $sx >= self::$width - 1 || $sy >= self::$height - 1) {
                    continue;
                } else {
                    $color = imagecolorat($img, $sx, $sy) & 0xFF;
                    $color_x = imagecolorat($img, $sx + 1, $sy) & 0xFF;
                    $color_y = imagecolorat($img, $sx, $sy + 1) & 0xFF;
                    $color_xy = imagecolorat($img, $sx + 1, $sy + 1) & 0xFF;
                }
                if ($color == 255 && $color_x == 255 && $color_y == 255 && $color_xy == 255) {
                    continue;
                } else if ($color == 0 && $color_x == 0 && $color_y == 0 && $color_xy == 0) {
                    $newred = $foreground_color[0];
                    $newgreen = $foreground_color[1];
                    $newblue = $foreground_color[2];
                } else {
                    $frsx = $sx - floor($sx);
                    $frsy = $sy - floor($sy);
                    $frsx1 = 1 - $frsx;
                    $frsy1 = 1 - $frsy;

                    $newcolor = ($color * $frsx1 * $frsy1 + $color_x * $frsx * $frsy1 + $color_y * $frsx1 * $frsy + $color_xy * $frsx * $frsy);
                    if ($newcolor > 255)
                        $newcolor = 255;
                    $newcolor = $newcolor / 255;
                    $newcolor0 = 1 - $newcolor;
                    $newred = $newcolor0 * $foreground_color[0] + $newcolor * $background_color[0];
                    $newgreen = $newcolor0 * $foreground_color[1] + $newcolor * $background_color[1];
                    $newblue = $newcolor0 * $foreground_color[2] + $newcolor * $background_color[2];
                }
                imagesetpixel($img2, $x, $y, imagecolorallocate($img2, $newred, $newgreen, $newblue));
            }
        }
        imagepng($img2, $img_file);
        imagedestroy($img);
        imagedestroy($img2);

        return $key;
    }
}