<?php

/**
 * @package     JohnCMS
 * @link        http://johncms.com
 * @copyright   Copyright (C) 2008-2012 JohnCMS Community
 * @license     LICENSE.txt (see attached file)
 * @version     VERSION.txt (see attached file)
 * @author      http://johncms.com/about
 */

defined('_IN_JOHNADM') or die('Error: restricted access');

//TODO: Доработать под новый список файлов!

// Проверяем права доступа
if (Vars::$USER_RIGHTS < 7) {
    header('Location: http://mobicms.net/404.php');
    exit;
}
class scaner
{
    /*
    -----------------------------------------------------------------
    Сканер - антишпион
    -----------------------------------------------------------------
    */
    public $scan_folders = array(
        '',
        '/download',
        '/files',
        '/forum',
        '/gallery',
        '/guestbook',
        '/images',
        '/includes',
        '/install',
        '/library',
        '/news',
        '/pages',
        '/panel',
        '/rss',
        '/sitemap',
        '/theme',
        '/users'
    );
    public $good_files = array(
        '../.htaccess',
        '../login.php',
        '../captcha.php',
        '../exit.php',
        '../go.php',
        '../index.php',
        '../registration.php',
        '../download/addkomm.php',
        '../download/arc.php',
        '../download/arctemp/index.php',
        '../download/cut.php',
        '../download/delcat.php',
        '../download/delmes.php',
        '../download/dfile.php',
        '../download/down.php',
        '../download/files/.htaccess',
        '../download/files/index.php',
        '../download/fonts/index.php',
        '../download/graftemp/index.php',
        '../download/img/index.php',
        '../download/import.php',
        '../download/index.php',
        '../download/komm.php',
        '../download/makdir.php',
        '../download/mp3temp/index.php',
        '../download/new.php',
        '../download/opis.php',
        '../download/preview.php',
        '../download/rat.php',
        '../download/refresh.php',
        '../download/ren.php',
        '../download/renf.php',
        '../download/screen/index.php',
        '../download/screen.php',
        '../download/search.php',
        '../download/select.php',
        '../download/trans.php',
        '../download/upl.php',
        '../download/view.php',
        '../download/zip.php',
        '../files/.htaccess',
        '../files/cache/.htaccess',
        '../files/forum/attach/index.php',
        '../files/forum/index.php',
        '../files/forum/topics/index.php',
        '../files/library/index.php',
        '../files/users/album/index.php',
        '../files/users/avatar/index.php',
        '../files/users/index.php',
        '../files/users/photo/index.php',
        '../files/users/pm/index.php',
        '../forum/includes/addfile.php',
        '../forum/includes/addvote.php',
        '../forum/includes/close.php',
        '../forum/includes/deltema.php',
        '../forum/includes/delvote.php',
        '../forum/includes/editpost.php',
        '../forum/includes/editvote.php',
        '../forum/includes/file.php',
        '../forum/includes/files.php',
        '../forum/includes/filter.php',
        '../forum/includes/loadtem.php',
        '../forum/includes/massdel.php',
        '../forum/includes/moders.php',
        '../forum/includes/new.php',
        '../forum/includes/nt.php',
        '../forum/includes/per.php',
        '../forum/includes/post.php',
        '../forum/includes/ren.php',
        '../forum/includes/restore.php',
        '../forum/includes/say.php',
        '../forum/includes/tema.php',
        '../forum/includes/users.php',
        '../forum/includes/vip.php',
        '../forum/includes/vote.php',
        '../forum/includes/who.php',
        '../forum/contents.php',
        '../forum/index.php',
        '../forum/search.php',
        '../forum/thumbinal.php',
        '../forum/vote_img.php',
        '../gallery/addkomm.php',
        '../gallery/album.php',
        '../gallery/cral.php',
        '../gallery/del.php',
        '../gallery/delf.php',
        '../gallery/delmes.php',
        '../gallery/edf.php',
        '../gallery/edit.php',
        '../gallery/foto/.htaccess',
        '../gallery/foto/index.php',
        '../gallery/index.php',
        '../gallery/komm.php',
        '../gallery/load.php',
        '../gallery/new.php',
        '../gallery/preview.php',
        '../gallery/razd.php',
        '../gallery/temp/index.php',
        '../gallery/trans.php',
        '../gallery/upl.php',
        '../guestbook/index.php',
        '../images/avatars/index.php',
        '../images/captcha/.htaccess',
        '../images/index.php',
        '../images/smileys/admin/index.php',
        '../images/smileys/index.php',
        '../images/smileys/simply/index.php',
        '../images/smileys/user/index.php',
        '../includes/.htaccess',
        '../includes/classes/bbcode.php',
        '../includes/classes/comments.php',
        '../includes/classes/core.php',
        '../includes/classes/counters.php',
        '../includes/classes/functions.php',
        '../includes/classes/mainpage.php',
        '../includes/classes/sitemap.php',
        '../includes/core.php',
        '../includes/db.php',
        '../includes/end.php',
        '../includes/func.php',
        '../includes/head.php',
        '../includes/index.php',
        '../includes/lib/class.upload.php',
        '../includes/lib/mp3.php',
        '../includes/lib/pclerror.lib.php',
        '../includes/lib/pcltar.lib.php',
        '../includes/lib/pcltrace.lib.php',
        '../includes/lib/pclzip.lib.php',
        '../includes/lib/pear.php',
        '../library/contents.php',
        '../library/includes/addkomm.php',
        '../library/includes/del.php',
        '../library/includes/edit.php',
        '../library/includes/java.php',
        '../library/includes/komm.php',
        '../library/includes/load.php',
        '../library/includes/mkcat.php',
        '../library/includes/moder.php',
        '../library/includes/new.php',
        '../library/includes/topread.php',
        '../library/includes/write.php',
        '../library/index.php',
        '../library/search.php',
        '../news/index.php',
        '../pages/faq.php',
        '../pages/index.php',
        '../pages/mainmenu.php',
        '../panel/includes/ads.php',
        '../panel/includes/access.php',
        '../panel/includes/antiflood.php',
        '../panel/includes/antispy.php',
        '../panel/includes/ban_panel.php',
        '../panel/includes/counters.php',
        '../panel/includes/forum.php',
        '../panel/includes/ipban.php',
        '../panel/includes/karma.php',
        '../panel/includes/languages.php',
        '../panel/includes/news.php',
        '../panel/includes/reg.php',
        '../panel/includes/search_ip.php',
        '../panel/includes/settings.php',
        '../panel/includes/sitemap.php',
        '../panel/includes/smileys.php',
        '../panel/includes/usr.php',
        '../panel/includes/usr_adm.php',
        '../panel/includes/usr_clean.php',
        '../panel/includes/usr_del.php',
        '../panel/index.php',
        '../rss/rss.php',
        '../sitemap/forum.php',
        '../sitemap/index.php',
        '../sitemap/library.php',
        '../users/album.php',
        '../users/cont.php',
        '../users/ignor.php',
        '../users/image.php',
        '../users/includes/admlist.php',
        '../users/includes/album/comments.php',
        '../users/includes/album/delete.php',
        '../users/includes/album/edit.php',
        '../users/includes/album/image_delete.php',
        '../users/includes/album/image_download.php',
        '../users/includes/album/image_edit.php',
        '../users/includes/album/image_move.php',
        '../users/includes/album/image_upload.php',
        '../users/includes/album/list.php',
        '../users/includes/album/show.php',
        '../users/includes/album/sort.php',
        '../users/includes/album/top.php',
        '../users/includes/album/users.php',
        '../users/includes/album/vote.php',
        '../users/includes/birth.php',
        '../users/includes/online.php',
        '../users/includes/profile/activity.php',
        '../users/includes/profile/ban.php',
        '../users/includes/profile/edit.php',
        '../users/includes/profile/guestbook.php',
        '../users/includes/profile/images.php',
        '../users/includes/profile/info.php',
        '../users/includes/profile/ip.php',
        '../users/includes/profile/karma.php',
        '../users/includes/profile/office.php',
        '../users/includes/profile/password.php',
        '../users/includes/profile/reset.php',
        '../users/includes/profile/settings.php',
        '../users/includes/profile/stat.php',
        '../users/search.php',
        '../users/includes/top.php',
        '../users/includes/userlist.php',
        '../users/index.php',
        '../users/pradd.php',
        '../users/profile.php',
        '../users/skl.php'
    );
    public $snap_base = 'scan_snapshot.dat';
    public $snap_files = array();
    public $bad_files = array();
    public $snap = false;
    public $track_files = array();
    private $checked_folders = array();
    private $cache_files = array();

    function scan()
    {
        // Сканирование на соответствие дистрибутиву
        foreach ($this->scan_folders as $data) {
            $this->scan_files(ROOT_DIR . $data);
        }
    }

    function snapscan()
    {
        // Сканирование по образу
        if (file_exists('../files/cache/' . $this->snap_base)) {
            $filecontents = file('../files/cache/' . $this->snap_base);
            foreach ($filecontents as $name => $value) {
                $filecontents[$name] = explode("|", trim($value));
                $this->track_files[$filecontents[$name][0]] = $filecontents[$name][1];
            }
            $this->snap = true;
        }

        foreach ($this->scan_folders as $data) {
            $this->scan_files(ROOT_DIR . $data);
        }
    }

    function snap()
    {
        // Добавляем снимок надежных файлов в базу
        foreach ($this->scan_folders as $data) {
            $this->scan_files(ROOT_DIR . $data, true);
        }
        $filecontents = "";

        foreach ($this->snap_files as $idx => $data) {
            $filecontents .= $data['file_path'] . "|" . $data['file_crc'] . "\r\n";
        }
        $filehandle = fopen('../files/cache/' . $this->snap_base, "w+");
        fwrite($filehandle, $filecontents);
        fclose($filehandle);
        @chmod('../files/cache/' . $this->snap_base, 0666);
    }

    function scan_files($dir, $snap = false)
    {
        // Служебная функция сканирования
        if (!isset($file))
            $file = false;
        $this->checked_folders[] = $dir . '/' . $file;

        if ($dh = @opendir($dir)) {
            while (false !== ($file = readdir($dh))) {
                if ($file == '.' or $file == '..' or $file == '.svn' or $file == '.DS_store') {
                    continue;
                }
                if (is_dir($dir . '/' . $file)) {
                    if ($dir != ROOTPATH)
                        $this->scan_files($dir . '/' . $file, $snap);
                } else {
                    if ($this->snap or $snap)
                        $templates = "|tpl";
                    else
                        $templates = "";
                    if (preg_match("#.*\.(php|cgi|pl|perl|php3|php4|php5|php6|phtml|py|htaccess" . $templates . ")$#i", $file)) {
                        $folder = str_replace("../..", ".", $dir);
                        $file_size = filesize($dir . '/' . $file);
                        $file_crc = strtoupper(dechex(crc32(file_get_contents($dir . '/' . $file))));
                        $file_date = date("d.m.Y H:i:s", filectime($dir . '/' . $file));
                        if ($snap) {
                            $this->snap_files[] = array(
                                'file_path' => $folder . '/' . $file,
                                'file_crc' => $file_crc
                            );
                        } else {
                            if ($this->snap) {
                                if ($this->track_files[$folder . '/' . $file] != $file_crc and !in_array($folder . '/' . $file, $this->cache_files))
                                    $this->bad_files[] = array(
                                        'file_path' => $folder . '/' . $file,
                                        'file_name' => $file,
                                        'file_date' => $file_date,
                                        'type' => 1,
                                        'file_size' => $file_size
                                    );
                            } else {
                                if (!in_array($folder . '/' . $file, $this->good_files) or $file_size > 300000)
                                    $this->bad_files[] = array(
                                        'file_path' => $folder . '/' . $file,
                                        'file_name' => $file,
                                        'file_date' => $file_date,
                                        'type' => 0,
                                        'file_size' => $file_size
                                    );
                            }
                        }
                    }
                }
            }
        }
    }
}

$scaner = new scaner();
switch (Vars::$MOD) {
    case 'scan':
        /*
        -----------------------------------------------------------------
        Сканируем на соответствие дистрибутиву
        -----------------------------------------------------------------
        */
        $scaner->scan();
        echo '<div class="phdr"><a href="index.php?act=antispy"><b>' . $lng['antispy'] . '</b></a> | ' . $lng['antispy_dist_scan'] . '</div>';
        if (count($scaner->bad_files)) {
            echo '<div class="rmenu"><small>' . $lng['antispy_dist_scan_bad'] . '</small></div>';
            echo '<div class="menu">';
            foreach ($scaner->bad_files as $idx => $data) {
                echo $data['file_path'] . '<br />';
            }
            echo '</div><div class="phdr">' . $lng['total'] . ': ' . count($scaner->bad_files) . '</div>';
        } else {
            echo '<div class="gmenu">' . $lng['antispy_dist_scan_good'] . '</div>';
        }
        echo '<p><a href="index.php?act=antispy&amp;mod=scan">' . $lng['antispy_rescan'] . '</a></p>';
        break;

    case 'snapscan':
        /*
        -----------------------------------------------------------------
        Сканируем на соответствие ранее созданному снимку
        -----------------------------------------------------------------
        */
        $scaner->snapscan();
        echo '<div class="phdr"><a href="index.php?act=antispy"><b>' . $lng['antispy'] . '</b></a> | ' . $lng['antispy_snapshot_scan'] . '</div>';
        if (count($scaner->track_files) == 0) {
            echo Functions::displayError($lng['antispy_no_snapshot'], '<a href="index.php?act=antispy&amp;mod=snap">' . $lng['antispy_snapshot_create'] . '</a>');
        } else {
            if (count($scaner->bad_files)) {
                echo '<div class="rmenu">' . $lng['antispy_snapshot_scan_bad'] . '</div>';
                echo '<div class="menu">';
                foreach ($scaner->bad_files as $idx => $data) {
                    echo $data['file_path'] . '<br />';
                }
                echo '</div>';
            } else {
                echo '<div class="gmenu">' . $lng['antispy_snapshot_scan_ok'] . '</div>';
            }
            echo '<div class="phdr">' . $lng['total'] . ': ' . count($scaner->bad_files) . '</div>';
        }
        break;

    case 'snap':
        /*
        -----------------------------------------------------------------
        Создаем снимок файлов
        -----------------------------------------------------------------
        */
        echo '<div class="phdr"><a href="index.php?act=antispy"><b>' . $lng['antispy'] . '</b></a> | ' . $lng['antispy_snapshot_create'] . '</div>';
        if (isset($_POST['submit'])) {
            $scaner->snap();
            echo '<div class="gmenu"><p>' . $lng['antispy_snapshot_create_ok'] . '</p></div>' .
                 '<div class="phdr"><a href="index.php?act=antispy">' . $lng['continue'] . '</a></div>';
        } else {
            echo '<form action="index.php?act=antispy&amp;mod=snap" method="post">' .
                 '<div class="menu"><p>' . $lng['antispy_snapshot_warning'] . '</p>' .
                 '<p><input type="submit" name="submit" value="' . $lng['antispy_snapshot_create'] . '" /></p>' .
                 '</div></form>' .
                 '<div class="phdr"><small>' . $lng['antispy_snapshot_help'] . '</small></div>';
        }
        break;

    default:
        /*
        -----------------------------------------------------------------
        Главное меню Сканера
        -----------------------------------------------------------------
        */
        echo '<div class="phdr"><a href="index.php"><b>' . $lng['admin_panel'] . '</b></a> | ' . $lng['antispy'] . '</div>' .
             '<div class="menu"><p><h3>' . $lng['antispy_scan_mode'] . '</h3><ul>' .
             '<li><a href="index.php?act=antispy&amp;mod=scan">' . $lng['antispy_dist_scan'] . '</a><br />' .
             '<small>' . $lng['antispy_dist_scan_help'] . '</small></li>' .
             '<li><a href="index.php?act=antispy&amp;mod=snapscan">' . $lng['antispy_snapshot_scan'] . '</a><br />' .
             '<small>' . $lng['antispy_snapshot_scan_help'] . '</small></li>' .
             '<li><a href="index.php?act=antispy&amp;mod=snap">' . $lng['antispy_snapshot_create'] . '</a><br />' .
             '<small>' . $lng['antispy_snapshot_create_help'] . '</small></li>' .
             '</ul></p></div><div class="phdr">&#160;</div>';
}
echo '<p>' . (Vars::$MOD ? '<a href="index.php?act=antispy">' . $lng['antispy_menu'] . '</a><br />' : '') . '<a href="index.php">' . $lng['admin_panel'] . '</a></p>';