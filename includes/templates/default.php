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

$this->httpHeaders(); // HTTP заголовки кэширования страниц
$this->htmlHeaders(); // HTML пролог

$this->displayLogo(); // Логотип и переключатель языков
$this->displayUserGreeting('header'); // Верхний блок с приветствием
$this->displayTopMenu('tmn'); // Верхнее меню сайта

echo '<div class="maintxt">';
$this->displayUserBan('alarm');
$this->displayNotifications('rmenu'); // Выводим различные оповещения

/*
-----------------------------------------------------------------
Выводим основное содержание
-----------------------------------------------------------------
*/
echo $contents;

$this->displayBottomMenu('fmenu');
echo '</div>';
$this->displayUsersOnline('footer');

// Копирайт сайта
echo '<div style="text-align:center"><p><b>' . Vars::$SYSTEM_SET['copyright'] . '</b></p>';

// Счетчики каталогов
Functions::displayCounters();

$this->htmlEnd(); // HTML окончание страницы