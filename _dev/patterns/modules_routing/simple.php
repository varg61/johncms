<?php

/*
 * Пример простого роутинга для модулей
 * Все подключаемые файлы должны находиться в каталоге _inc модуля
 */

$actions = array(
    'assets' => 'profile_assets.php',
    'whois'  => 'whois.php',
);

if (Vars::$ROUTE_FIRST) {
    $include = isset($actions[Vars::$ROUTE_FIRST]) ? : FALSE;
} else {
    $include = 'mainmenu.php';
}

if ($include && is_file(MODPATH . Router::$PATH . DIRECTORY_SEPARATOR . '_inc' . DIRECTORY_SEPARATOR . $include)) {
    require_once(MODPATH . Router::$PATH . DIRECTORY_SEPARATOR . '_inc' . DIRECTORY_SEPARATOR . $include);
} else {
    header('Location: ' . Vars::$HOME_URL . '/404');
}