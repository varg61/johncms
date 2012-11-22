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

$tpl->total = mysql_result(mysql_query("SELECT COUNT(*) FROM `cms_news`"), 0);
if ($tpl->total) {
    $req = mysql_query("SELECT * FROM `cms_news` ORDER BY `id` DESC " . Vars::db_pagination());
    for ($i = 0; $tpl->list[$i] = mysql_fetch_assoc($req); ++$i) {
        $tpl->list[$i]['text'] = Validate::checkout($tpl->list[$i]['text'], 1, 1, 1);
    }
    unset($tpl->list[$i]);
}

$tpl->contents = $tpl->includeTpl('index');