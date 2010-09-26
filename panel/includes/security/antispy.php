<?php
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

//TODO: Разобраться с подкаталогом /incfiles/lib
defined('_IN_JOHNADM') or die('Error: restricted access');
define('ROOT_DIR', '..');

// Проверяем права доступа
if ($rights < 7) {
    header('Location: http://johncms.com/?err');
    exit;
}

class scaner {
    /*
    -----------------------------------------------------------------
    Сканер - антишпион
    -----------------------------------------------------------------
    */
    public $scan_folders = array (
        '',
        '/cache',
        '/chat',
        '/download',
        '/forum',
        '/gallery',
        '/incfiles',
        '/library',
        '/pages',
        '/pratt',
        '/rss',
        '/smileys',
        '/str',
        '/theme',
        '/panel',
        '/install'
    );
    public $good_files = array (
        '../.htaccess',
        '../login.php',
        '../captcha.php',
        '../exit.php',
        '../go.php',
        '../index.php',
        '../read.php',
        '../registration.php',
        '../cache/.htaccess',
        '../chat/chat_footer.php',
        '../chat/chat_header.php',
        '../chat/hall.php',
        '../chat/index.php',
        '../chat/room.php',
        '../chat/who.php',
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
        '../download/upl/index.php',
        '../download/upl.php',
        '../download/view.php',
        '../download/zip.php',
        '../forum/addfile.php',
        '../forum/addvote.php',
        '../forum/close.php',
        '../forum/deltema.php',
        '../forum/delvote.php',
        '../forum/editpost.php',
        '../forum/editvote.php',
        '../forum/faq.php',
        '../forum/file.php',
        '../forum/files/.htaccess',
        '../forum/files/index.php',
        '../forum/files.php',
        '../forum/filter.php',
        '../forum/index.php',
        '../forum/loadtem.php',
        '../forum/massdel.php',
        '../forum/moders.php',
        '../forum/new.php',
        '../forum/nt.php',
        '../forum/per.php',
        '../forum/post.php',
        '../forum/read.php',
        '../forum/ren.php',
        '../forum/restore.php',
        '../forum/say.php',
        '../forum/search.php',
        '../forum/tema.php',
        '../forum/temtemp/index.php',
        '../forum/thumbinal.php',
        '../forum/trans.php',
        '../forum/users.php',
        '../forum/vip.php',
        '../forum/vote.php',
        '../forum/vote_img.php',
        '../forum/who.php',
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
        '../incfiles/.htaccess',
        '../incfiles/ban.php',
        '../incfiles/class_ipinit.php',
        '../incfiles/class_mainpage.php',
        '../incfiles/pclzip.lib.php',
        '../incfiles/lib/class.upload.php',
        '../incfiles/core.php',
        '../incfiles/db.php',
        '../incfiles/end.php',
        '../incfiles/func.php',
        '../incfiles/head.php',
        '../incfiles/index.php',
        '../incfiles/lib/mp3.php',
        '../incfiles/lib/pear.php',
        '../library/addkomm.php',
        '../library/del.php',
        '../library/edit.php',
        '../library/files/index.php',
        '../library/index.php',
        '../library/java.php',
        '../library/komm.php',
        '../library/load.php',
        '../library/mkcat.php',
        '../library/moder.php',
        '../library/new.php',
        '../library/search.php',
        '../library/symb.php',
        '../library/temp/index.php',
        '../library/topread.php',
        '../library/trans.php',
        '../library/write.php',
        '../pages/index.php',
        '../pages/mainmenu.php',
        '../pratt/.htaccess',
        '../pratt/index.php',
        '../rss/rss.php',
        '../smileys/admin/index.php',
        '../smileys/index.php',
        '../smileys/simply/index.php',
        '../smileys/user/index.php',
        '../panel/index.php',
        '../panel/mod_ads.php',
        '../panel/mod_chat.php',
        '../panel/mod_counters.php',
        '../panel/mod_karma.php',
        '../panel/mod_news.php',
        '../panel/sys_access.php',
        '../panel/antispy.php',
        '../panel/sys_flood.php',
        '../panel/sys_smileys.php',
        '../panel/usr_adm.php',
        '../panel/usr_ban.php',
        '../panel/usr_del.php',
        '../panel/usr_list.php',
        '../panel/usr_reg.php',
        '../panel/usr_search_ip.php',
        '../panel/usr_search_nick.php',
        '../panel/sys_ipban.php',
        '../panel/mod_forum.php',
        '../panel/sys_set.php'
    );
    public $snap_base = 'scan_snapshot.dat';
    public $snap_files = array ();
    public $bad_files = array ();
    public $snap = false;
    public $track_files = array ();
    private $checked_folders = array ();
    private $cache_files = array ();
    function scan() {
        // Сканирование на соответствие дистрибутиву
        foreach ($this->scan_folders as $data) {
            $this->scan_files(ROOT_DIR . $data);
        }
    }
    function snapscan() {
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
    function snap() {
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
    function scan_files($dir, $snap = false) {
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
                    if ($dir != ROOT_DIR)
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
                            $this->snap_files[] = array (
                                'file_path' => $folder . '/' . $file,
                                'file_crc' => $file_crc
                            );
                        } else {
                            if ($this->snap) {
                                if ($this->track_files[$folder . '/' . $file] != $file_crc and !in_array($folder . '/' . $file, $this->cache_files))
                                    $this->bad_files[] = array (
                                        'file_path' => $folder . '/' . $file,
                                        'file_name' => $file,
                                        'file_date' => $file_date,
                                        'type' => 1,
                                        'file_size' => $file_size
                                    );
                            } else {
                                if (!in_array($folder . '/' . $file, $this->good_files) or $file_size > 300000)
                                    $this->bad_files[] = array (
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
switch ($mod) {
    case 'scan':
        /*
        -----------------------------------------------------------------
        Сканируем на соответствие дистрибутиву
        -----------------------------------------------------------------
        */
        $scaner->scan();
        echo '<div class="phdr"><a href="index.php?act=antispy"><b>' . $lng['antispy'] . '</b></a> | ' . $lng['antispy_dist_scan'] . '</div>';
        if (count($scaner->bad_files)) {
            echo '<div class="rmenu">' . $lng['antispy_dist_scan_bad'] . '</small></div>';
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
            echo display_error($lng['antispy_no_snapshot'], '<a href="index.php?act=antispy&amp;mod=snap">' . $lng['antispy_snapshot_create'] . '</a>');
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
echo '<p>' . ($mod ? '<a href="index.php?act=antispy">' . $lng['antispy_menu'] . '</a><br />' : '') . '<a href="index.php">' . $lng['admin_panel'] . '</a></p>';
?>