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
require ('incfiles/class_mainpage.php');
$mp = new mainpage();
// Блок новостей
echo $mp->news;
echo '<div class="phdr"><b>' . $lng['information'] . '</b></div>';
echo '<div class="menu"><a href="str/news.php">' . $lng['news_archive'] . '</a> (' . $mp->newscount . ')</div>';
echo '<div class="menu"><a href="index.php?act=info">' . $lng['information'] . '</a> <a href="str/faq.php">FAQ</a></div>';
echo '<div class="menu"><a href="index.php?act=users">' . $lng['site_active'] . '</a></div>';
echo '<div class="phdr"><b>' . $lng['dialogue'] . '</b></div>';
echo '<div class="menu"><a href="str/guest.php">' . $lng['guestbook'] . '</a> (' . stat_guestbook() . ')</div>';
echo '<div class="menu"><a href="forum/">' . $lng['forum'] . '</a> (' . stat_forum() . ')</div>';
echo '<div class="menu"><a href="chat/">' . $lng['chat'] . '</a> (' . stat_chat() . ')</div>';
echo '<div class="phdr"><b>' . $lng['useful'] . '</b></div>';
echo '<div class="menu"><a href="download/">' . $lng['downloads'] . '</a> (' . stat_download() . ')</div>';
echo '<div class="menu"><a href="library/">' . $lng['library'] . '</a> (' . stat_library() . ')</div>';
echo '<div class="menu"><a href="gallery/">' . $lng['gallery'] . '</a> (' . stat_gallery() . ')</div>';
echo '<div class="phdr"><a href="http://gazenwagen.com">Gazenwagen</a></div>';

?>