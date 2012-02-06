<?php

/**
 * @package     JohnCMS
 * @link        http://johncms.com
 * @copyright   Copyright (C) 2008-2012 JohnCMS Community
 * @license     LICENSE.txt (see attached file)
 * @version     VERSION.txt (see attached file)
 * @author      http://johncms.com/about
 */

defined('_IN_JOHNCMS') or die('Error: restricted access');

echo '<div class="phdr"><a href="index.php"><b>' . Vars::$LNG['downloads'] . '</b></a> | ' . Vars::$LNG['search'] . '</div>';
if (isset($_GET['srh']) && !empty ($_GET['srh'])) {
    $srh = Validate::filterString($_GET['srh']);
} elseif (isset($_POST['srh']) && !empty($_POST['srh'])) {
    $srh = Validate::filterString($_POST['srh']);
} else {
    if ($_POST['srh'] == "") {
        echo Functions::displayError($lng_dl['search_string_empty'], '<a href="index.php">' . Vars::$LNG['back'] . '</a>');
        exit;
    }
}

$psk = mysql_query("select * from `download` where  type='file' ;");
if (empty ($_GET['start']))
    $start = 0;
else
    $start = $_GET['start'];

while ($array = mysql_fetch_array($psk)) {
    if (stristr($array [name], $srh)) {
        $res[] = $lng_dl['found_by_name'] . ":<br/><a href='?act=view&amp;file=" . $array [id] . "'>$array[name]</a><br/>";
    }
    if (stristr($array [text], $srh)) {
        $res[] = $lng_dl['found_by_description'] . ":<br/><a href='?act=view&amp;file=" . $array [id] . "'>$array[name]</a><br/>$array[text]<br/>";
    }
}
$g = count($res);
if ($g == 0) {
    echo '<p>' . Vars::$LNG['search_results_empty'] . '</p>';
}
else {
    echo '<p>' . Vars::$LNG['search_results'] . '</p>';
}
if (empty ($_GET['page'])) {
    $page = 1;
}
else {
    $page = intval($_GET['page']);
}
$start = $page * 10 - 10;
if ($g < $start + 10) {
    $end = $g;
}
else {
    $end = $start + 10;
}
for ($i = $start; $i < $end; $i++) {
    $d = $i / 2;
    $d1 = ceil($d);
    $d2 = $d1 - $d;
    $d3 = ceil($d2);
    if ($d3 == 0) {
        $div = "<div class='c'>";
    }
    else {
        $div = "<div class='b'>";
    }
    echo "$div $res[$i]</div>";
}
if ($g > 10) {
    echo "<hr/>";

    $ba = ceil($g / 10);
    echo "Pages:<br/>"; //TODO: Переделать на новый листинг по страницам
    $asd = $start - 10;
    $asd2 = $start + 20;

    if ($start != 0) {
        echo '<a href="index.php?act=search&amp;srh=' . $srh . '&amp;page=' . ($page - 1) . '">&lt;&lt;</a> ';
    }
    if ($asd < $g && $asd > 0) {
        echo ' <a href="index.php?act=search&amp;srh=' . $srh . '&amp;page=1&amp;">1</a> .. ';
    }
    $page2 = $ba - $page;
    $pa = ceil($page / 2);
    $paa = ceil($page / 3);
    $pa2 = $page + floor($page2 / 2);
    $paa2 = $page + floor($page2 / 3);
    $paa3 = $page + (floor($page2 / 3) * 2);
    if ($page > 13) {
        echo ' <a href="index.php?act=search&amp;srh=' . $srh . '&amp;page=' . $paa . '">' . $paa . '</a> <a href="index.php?act=search&amp;srh=' . $srh . '&amp;page=' . ($paa + 1) . '">' . ($paa + 1) .
            '</a> .. <a href="index.php?act=search&amp;srh=' . $srh . '&amp;page=' . ($paa * 2) . '">' . ($paa * 2) . '</a> <a href="index.php?act=search&amp;srh=' . $srh . '&amp;page=' . ($paa * 2 + 1) . '">' . ($paa * 2 + 1) . '</a> .. ';
    }
    elseif ($page > 7) {
        echo ' <a href="?id=' . Vars::$ID . '&amp;page=' . $pa . '">' . $pa . '</a> <a href="index.php?act=search&amp;srh=' . $srh . '&amp;page=' . ($pa + 1) . '">' . ($pa + 1) . '</a> .. ';
    }
    for ($i = $asd; $i < $asd2;) {
        if ($i < $g && $i >= 0) {
            $ii = floor(1 + $i / 10);

            if ($start == $i) {
                echo " <b>$ii</b>";
            }
            else {
                echo ' <a href="index.php?act=search&amp;srh=' . $srh . '&amp;page=' . $ii . '">' . $ii . '</a> ';
            }
        }
        $i = $i + 10;
    }
    if ($page2 > 12) {
        echo ' .. <a href="index.php?act=search&amp;srh=' . $srh . '&amp;page=' . $paa2 . '">' . $paa2 . '</a> <a href="index.php?act=search&amp;srh=' . $srh . '&amp;page=' . ($paa2 + 1) . '">' . ($paa2 + 1) .
            '</a> .. <a href="index.php?act=search&amp;srh=' . $srh . '&amp;page=' . ($paa3) . '">' . ($paa3) . '</a> <a href="index.php?act=search&amp;srh=' . $srh . '&amp;page=' . ($paa3 + 1) . '">' . ($paa3 + 1) . '</a> ';
    }
    elseif ($page2 > 6) {
        echo ' .. <a href="index.php?act=search&amp;srh=' . $srh . '&amp;page=' . $pa2 . '">' . $pa2 . '</a> <a href="index.php?act=search&amp;srh=' . $srh . '&amp;page=' . ($pa2 + 1) . '">' . ($pa2 + 1) . '</a> ';
    }
    if ($asd2 < $g) {
        echo ' .. <a href="index.php?act=search&amp;srh=' . $srh . '&amp;page=' . $ba . '">' . $ba . '</a>';
    }
    if ($g > $start + 10) {
        echo ' <a href="index.php?act=search&amp;srh=' . $srh . '&amp;page=' . ($page + 1) . '">&gt;&gt;</a>';
    }
    echo "<form action='index.php'>To Page:<br/><input type='hidden' name='act' value='search'/><input type='hidden' name='srh' value='" . $srh .
        "'/><input type='text' name='page' /><br/><input type='submit' value='Go!'/></form>";
}
if ($g != 0) {
    echo "<div class='phdr'>" . Vars::$LNG['total'] . ": $g</div>";
}
echo '<p><a href="?">' . Vars::$LNG['downloads'] . '</a></p>';