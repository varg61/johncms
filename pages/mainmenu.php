<?

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

////////////////////////////////////////////////////////////
// Главное меню сайта                                     //
////////////////////////////////////////////////////////////
require_once ('incfiles/class_mainpage.php');
$mp = new mainpage();
// Блок новостей
echo $mp->news;
echo '<div class="phdr"><b>Информация</b></div>';
echo '<div class="menu"><a href="str/news.php">Архив новостей</a> (' . $mp->newscount . ')</div>';
echo '<div class="menu"><a href="index.php?act=info">Доп. информация</a></div>';
echo '<div class="menu"><a href="index.php?act=users">Актив Сайта</a></div>';
echo '<div class="phdr"><b>Общение</b></div>';
echo '<div class="menu"><a href="str/guest.php">Гостевая</a> (' . stat_guestbook() . ')</div>';
echo '<div class="menu"><a href="forum/">' . $lng['forum'] . '</a> (' . stat_forum() . ')</div>';
echo '<div class="menu"><a href="chat/">' . $lng['chat'] . '</a> (' . stat_chat() . ')</div>';
echo '<div class="phdr"><b>Полезное</b></div>';
echo '<div class="menu"><a href="download/">Загрузки</a> (' . stat_download() . ')</div>';
echo '<div class="menu"><a href="library/">Библиотека</a> (' . stat_library() . ')</div>';
echo '<div class="menu"><a href="gallery/">Галерея</a> (' . stat_gallery() . ')</div>';
echo '<div class="phdr"><a href="http://gazenwagen.com">Ф Газенвагенъ</a></div>';

?>