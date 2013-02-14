<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Oleg Kasyanov
 * Date: 20.01.13
 * Time: 20:08
 * To change this template use File | Settings | File Templates.
 */

define('_IN_JOHNCMS', 1);
require('includes/core.php');

$STH = DB::PDO()->prepare('
    INSERT INTO `test`
    (`name`, `text`, `more`)
    VALUES (?, ?, ?)
');

$STH->execute(array(
    'TestName',
    'SOME Text',
    'm'
));

echo DB::PDO()->lastInsertId();