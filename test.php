<?php

function koescale($percent) {
// тут процент получаем и фильтруем
    if ($percent && $percent > 0 && $percent<101) {
        $percent = intval($percent);
    } else {
        die('ПНХ');
    }

// тут первый квадратик
    $img_one = imagecreatetruecolor(100, 20);
    $color_one = imagecolorallocate($img_one, 0, 0, 0);
    imagefilledrectangle ($img_one, 0, 0, 100, 20, $color_one);

// тут второй квадратик
    $img_two = imagecreatetruecolor($percent, 20);
    $color_two = imagecolorallocate($img_two, 255, 255, 255);
    imagefilledrectangle ($img_two, 0, 0, $percent, 20, $color_two);

// второй квадратик зависит от процента из GET
// накладываем квадратики с прозрачностью
    imagecopymerge($img_one, $img_two, 0, 0, 0, 0, 100, 20, 25);

// создаем прозрачный квадратик и пишем на нем цифру
    $print_percent = imagecreatetruecolor(100, 20);
    $white = imagecolorallocate($print_percent, 0, 0, 0);
    $red = imagecolorallocate($print_percent, 255, 0, 0);
    imagecolortransparent($print_percent, $white);
    imagestring($print_percent, 12, 40, 2, $percent . '%', $red);

// накладываемцифру на квадратики
    imagecopymerge($img_one, $print_percent, 0, 0, 0, 0, 100, 20, 100);

// рисуем результат
    imagegif($img_one, 'tmp.gif');
    $fil = file_get_contents('tmp.gif');
    unlink('tmp.gif');
    imagedestroy($img_one);
    imagedestroy($img_two);
    imagedestroy($print_percent);

    return '<img width="100" height="20" src="data:image/gif;base64,' . chunk_split(base64_encode($fil)) . '" alt="koescale" />';
}

echo 'сила';
echo '<br/>';
echo koescale(56) ;
echo '<br/>';
echo 'ловкость';
echo '<br/>';
echo koescale(76) ;
echo '<br/>';
echo 'дух';
echo '<br/>';
echo koescale(46) ;
echo '<br/>';
echo 'разум';
echo '<br/>';
echo koescale(26) ;