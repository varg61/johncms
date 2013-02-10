<?php

/*
 * Для примера используется код для вывода списка новостей.
 * В дальнейших разработках желательно следовать данному шаблону проектирования.
 *
 * Используемые переменные
 * $tpl->total      INT             Счетчик общего к-ва новостей (total)
 * $tpl->list       ARRAY (MIXED)   Спосок новостей в массиве, для вывода в шаблон
 */

$tpl = Template::getInstance();
$tpl->uri = Router::getUri(2);

$tpl->total = DB::PDO()->query('SELECT COUNT(*) FROM `cms_news`')->fetchColumn();

if ($tpl->total) {
    $query = DB::PDO()->query('SELECT * FROM `cms_news` ORDER BY `id` DESC ' . Vars::db_pagination());
    foreach($query as $val){
        $val['text'] = Functions::checkout($val['text'], 1, 1, 1);
        $tpl->list[] = $val;
    }
}

$tpl->contents = $tpl->includeTpl('index');