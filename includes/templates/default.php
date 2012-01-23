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

/*
-----------------------------------------------------------------
Шаблон по-умолчанию
Используется всегда, когда в модуле не задан собственный шаблон

Вместо вызова любого из системных методов, можно использовать свой код
-----------------------------------------------------------------
*/

$this->httpHeaders();                                                          // HTTP заголовки кэширования страниц
$this->htmlHeaders();                                                          // HTML пролог

$this->displayLogo();                                                          // Логотип и переключатель языков
$this->displayUserGreeting('header');                                          // Верхний блок с приветствием
$this->displayTopMenu('tmn');                                                  // Верхнее меню сайта

echo '<div class="maintxt">';                                                  // Начало блока контента
$this->displayUserBan('alarm');                                                // Если пользователь в бане, выводим информацию
$this->displayNotifications('rmenu');                                          // Выводим различные оповещения

/*
-----------------------------------------------------------------
Выводим основное содержание
-----------------------------------------------------------------
*/
echo $contents;

$this->displayBottomMenu('fmenu');                                             // Нижнее меню сайта
echo '</div>';                                                                 // Окончание блока контента <div class="maintxt">
$this->displayUsersOnline('footer');
echo '<div style="text-align:center"><p><b>' . Vars::$SYSTEM_SET['copyright'] . '</b></p>'; // Копирайт сайта
Functions::displayCounters();                                                  // Счетчики каталогов
$this->htmlEnd();                                                              // HTML окончание страницы