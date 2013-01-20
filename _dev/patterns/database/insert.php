<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Oleg Kasyanov
 * Date: 20.01.13
 * Time: 18:23
 * To change this template use File | Settings | File Templates.
 */

$stmt = $db->prepare('INSERT INTO ids VALUES (0, :url)');

try {
    $db->beginTransaction();
    foreach ($ursl as $url) {
        $stmt->bindValue(':url', $url);
        $stmt->execute();
    }
    $db->commit();
} catch (PDOException $e) {
    $db->rollBack();
}