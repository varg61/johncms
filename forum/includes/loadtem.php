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

if (empty($_GET['n'])) {
    require_once('../includes/head.php');
    echo Functions::displayError(Vars::$LNG['error_wrong_data']);
    require_once('../includes/end.php');
    exit;
}
$n = trim($_GET['n']);
$o = opendir("../files/forum/topics");
while ($f = readdir($o)) {
    if ($f != "." && $f != ".." && $f != "index.php" && $f != ".htaccess") {
        $ff = Functions::format($f);
        $f1 = str_replace(".$ff", "", $f);
        $a[] = $f;
        $b[] = $f1;
    }
}
$tt = count($a);
if (!in_array($n, $b)) {
    require_once('../includes/head.php');
    echo Functions::displayError(Vars::$LNG['error_wrong_data']);
    require_once('../includes/end.php');
    exit;
}
for ($i = 0; $i < $tt; $i++) {
    $tf = Functions::format($a[$i]);
    $tf1 = str_replace(".$tf", "", $a[$i]);
    if ($n == $tf1) {
        header("Location: ../files/forum/topics/$n.$tf");
    }
}