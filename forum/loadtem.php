<?php

/*
////////////////////////////////////////////////////////////////////////////////
// JohnCMS                Mobile Content Management System                    //
// Project site:          http://johncms.com                                  //
// Support site:          http://gazenwagen.com                               //
////////////////////////////////////////////////////////////////////////////////
// Lead Developer:        Oleg Kasyanov   (AlkatraZ)  alkatraz@gazenwagen.com //
// Development Team:      Eugene Ryabinin (john77)    john77@gazenwagen.com   //
//                        Dmitry Liseenko (FlySelf)   flyself@johncms.com     //
////////////////////////////////////////////////////////////////////////////////
*/

defined('_IN_JOHNCMS') or die('Error: restricted access');

if (empty($_GET['n'])) {
    require_once('../incfiles/head.php');
    echo "Ошибка!<br/><a href='?'>В форум</a><br/>";
    require_once('../incfiles/end.php');
    exit;
}
$n = $_GET['n'];
$o = opendir("temtemp");
while ($f = readdir($o)) {
    if ($f != "." && $f != ".." && $f != "index.php" && $f != ".htaccess") {
        $ff = format($f);
        $f1 = str_replace(".$ff", "", $f);
        $a[] = $f;
        $b[] = $f1;
    }
}
$tt = count($a);
if (!in_array($n, $b)) {
    require_once('../incfiles/head.php');
    echo "Ошибка!<br/><a href='?'>В форум</a><br/>";
    require_once('../incfiles/end.php');
    exit;
}
for ($i = 0; $i < $tt; $i++) {
    $tf = format($a[$i]);
    $tf1 = str_replace(".$tf", "", $a[$i]);
    if ($n == $tf1) {
        header("Location: temtemp/$n.$tf");
    }
}

?>