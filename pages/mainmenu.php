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
$mp = new mainpage();
echo '<div class="phdr"><b>' . $lng['information'] . '</b></div>';
echo $mp->news;
echo '<div class="menu"><a href="news/index.php">' . $lng['news_archive'] . '</a> (' . $mp->newscount . ')</div>' .
    '<div class="menu"><a href="index.php?act=info">' . $lng['information'] . '</a> <a href="pages/faq.php">FAQ</a></div>' .
    '<div class="phdr"><b>' . $lng['dialogue'] . '</b></div>' .
    '<div class="menu"><a href="guestbook/index.php">' . $lng['guestbook'] . '</a> (' . functions::stat_guestbook() . ')</div>' .
    '<div class="menu"><a href="forum/">' . $lng['forum'] . '</a> (' . functions::stat_forum() . ')</div>' .
    '<div class="phdr"><b>' . $lng['useful'] . '</b></div>' .
    '<div class="menu"><a href="download/">' . $lng['downloads'] . '</a> (' . functions::stat_download() . ')</div>' .
    '<div class="menu"><a href="library/">' . $lng['library'] . '</a> (' . functions::stat_library() . ')</div>' .
    '<div class="menu"><a href="gallery/">' . $lng['gallery'] . '</a> (' . functions::stat_gallery() . ')</div>';
if ($user_id || $set['active']) {
    echo '<div class="phdr"><b>' . $lng['community'] . '</b></div>' .
        '<div class="menu"><a href="users/index.php">' . $lng['users'] . '</a> (' . functions::stat_users() . ')</div>' .
        '<div class="menu"><a href="users/album.php">' . $lng['photo_albums'] . '</a> (' . functions::count_photo() . ')</div>' .
        '<div class="menu">' . $lng['blogs'] . '</div>';
}
echo '<div class="phdr"><a href="http://gazenwagen.com">Gazenwagen</a></div>';
?>
