<?php

define('_IN_JOHNCMS', 1);

require_once('includes/core.php');

echo'<div class="content">';
echo'<h3>Проверка валидатора</h3><br/><br/>';

$val = '192.168.0.100';
$valid = new Validate('ip', $val);

if($valid->is){
    echo'Done';
} else {
    echo implode('<br/>', $valid->error);
}

echo'</div>';
