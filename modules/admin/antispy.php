<?php

/**
 * @package     JohnCMS
 * @link        http://johncms.com
 * @copyright   Copyright (C) 2008-2012 JohnCMS Community
 * @license     LICENSE.txt (see attached file)
 * @version     VERSION.txt (see attached file)
 * @author      http://johncms.com/about
 */

define('ROOT_DIR', '.');

// Проверяем права доступа
if (Vars::$USER_RIGHTS < 7) {
    echo Functions::displayError(lng('access_forbidden'));
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
        '/files',
        '/images',
        '/includes',
        '/install',
        '/modules',
        '/templates',
    );
    public $good_files = array(
        './.htaccess',
        './index.php',

        './files/.htaccess',
        './files/forum/index.php',
        './files/library/index.php',
        './files/users/album/index.php',
        './files/users/avatar/index.php',
        './files/users/index.php',
        './files/users/photo/index.php',
        './files/users/pm/index.php',

        './images/avatars/index.php',
        './images/captcha/.htaccess',
        './images/index.php',
        './images/smileys/index.php',

        './includes/.htaccess',
        './includes/classes/advt.php',
        './includes/classes/captcha.php',
        './includes/classes/comments.php',
        './includes/classes/counters.php',
        './includes/classes/functions.php',
        './includes/classes/homepage.php',
        './includes/classes/network.php',
        './includes/classes/session.php',
        './includes/classes/sitemap.php',
        './includes/classes/system.php',
        './includes/classes/template.php',
        './includes/classes/textparser.php',
        './includes/classes/validate.php',
        './includes/classes/vars.php',
        './includes/config/config.php',
        './includes/core.php',
        './includes/lib/class.upload.php',
        './includes/lib/mp3.php',
        './includes/lib/pclerror.lib.php',
        './includes/lib/pcltar.lib.php',
        './includes/lib/pcltrace.lib.php',
        './includes/lib/pclzip.lib.php',
        './includes/lib/pear.php',
        './includes/old_vars.php',
        './includes/template_default.php',

        './modules/.htaccess',
        '',
    );
    public $snap_base = 'scan_snapshot.dat';
    public $snap_files = array();
    public $bad_files = array();
    public $snap = FALSE;
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
        if (file_exists(CACHEPATH . $this->snap_base)) {
            $filecontents = file(CACHEPATH . $this->snap_base);
            foreach ($filecontents as $name => $value) {
                $filecontents[$name] = explode("|", trim($value));
                $this->track_files[$filecontents[$name][0]] = $filecontents[$name][1];
            }
            $this->snap = TRUE;
        }

        foreach ($this->scan_folders as $data) {
            $this->scan_files(ROOT_DIR . $data);
        }
    }

    function snap()
    {
        // Добавляем снимок надежных файлов в базу
        foreach ($this->scan_folders as $data) {
            $this->scan_files(ROOT_DIR . $data, TRUE);
        }
        $filecontents = "";

        foreach ($this->snap_files as $idx => $data) {
            $filecontents .= $data['file_path'] . "|" . $data['file_crc'] . "\r\n";
        }
        $filehandle = fopen(CACHEPATH . $this->snap_base, "w+");
        fwrite($filehandle, $filecontents);
        fclose($filehandle);
        @chmod(CACHEPATH . $this->snap_base, 0666);
    }

    function scan_files($dir, $snap = FALSE)
    {
        // Служебная функция сканирования
        if (!isset($file))
            $file = FALSE;
        $this->checked_folders[] = $dir . '/' . $file;

        if ($dh = @opendir($dir)) {
            while (FALSE !== ($file = readdir($dh))) {
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
                            $this->snap_files[] = array(
                                'file_path' => $folder . '/' . $file,
                                'file_crc'  => $file_crc
                            );
                        } else {
                            if ($this->snap) {
                                if ($this->track_files[$folder . '/' . $file] != $file_crc and !in_array($folder . '/' . $file, $this->cache_files))
                                    $this->bad_files[] = array(
                                        'file_path' => $folder . '/' . $file,
                                        'file_name' => $file,
                                        'file_date' => $file_date,
                                        'type'      => 1,
                                        'file_size' => $file_size
                                    );
                            } else {
                                if (!in_array($folder . '/' . $file, $this->good_files) or $file_size > 300000)
                                    $this->bad_files[] = array(
                                        'file_path' => $folder . '/' . $file,
                                        'file_name' => $file,
                                        'file_date' => $file_date,
                                        'type'      => 0,
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
$tpl = Template::getInstance();

switch (Vars::$ACT) {
    case 'scan':
        /*
        -----------------------------------------------------------------
        Сканируем на соответствие дистрибутиву
        -----------------------------------------------------------------
        */
        $scaner->scan();
        echo '<div class="phdr"><a href="' . Vars::$URI . '"><b>' . lng('antispy') . '</b></a> | ' . lng('antispy_dist_scan') . '</div>';
        if (count($scaner->bad_files)) {
            echo '<div class="rmenu"><small>' . lng('antispy_dist_scan_bad') . '</small></div>';
            echo '<div class="menu">';
            foreach ($scaner->bad_files as $idx => $data) {
                echo $data['file_path'] . '<br />';
            }
            echo'</div>';
        } else {
            echo '<div class="gmenu"><p>' . lng('antispy_dist_scan_good') . '</p></div>';
        }
        echo'<div class="phdr">' . lng('total') . ': ' . count($scaner->bad_files) . '</div>' .
            '<p><a href="' . Vars::$URI . '?act=scan">' . lng('antispy_rescan') . '</a></p>';
        break;

    case 'snapscan':
        /*
        -----------------------------------------------------------------
        Сканируем на соответствие ранее созданному снимку
        -----------------------------------------------------------------
        */
        $scaner->snapscan();
        echo '<div class="phdr"><a href="' . Vars::$URI . '"><b>' . lng('antispy') . '</b></a> | ' . lng('antispy_snapshot_scan') . '</div>';
        if (count($scaner->track_files) == 0) {
            echo Functions::displayError(lng('antispy_no_snapshot'), '<a href="' . Vars::$URI . '?act=snap">' . lng('antispy_snapshot_create') . '</a>');
        } else {
            if (count($scaner->bad_files)) {
                echo '<div class="rmenu">' . lng('antispy_snapshot_scan_bad') . '</div>';
                echo '<div class="menu">';
                foreach ($scaner->bad_files as $idx => $data) {
                    echo $data['file_path'] . '<br />';
                }
                echo '</div>';
            } else {
                echo '<div class="gmenu"><p>' . lng('antispy_snapshot_scan_ok') . '</p></div>';
            }
            echo '<div class="phdr">' . lng('total') . ': ' . count($scaner->bad_files) . '</div>';
        }
        break;

    case 'snap':
        /*
        -----------------------------------------------------------------
        Создаем снимок файлов
        -----------------------------------------------------------------
        */
        echo '<div class="phdr"><a href="' . Vars::$URI . '"><b>' . lng('antispy') . '</b></a> | ' . lng('antispy_snapshot_create') . '</div>';
        if (isset($_POST['submit'])) {
            $scaner->snap();
            echo'<div class="gmenu"><p>' . lng('antispy_snapshot_create_ok') . '</p></div>' .
                '<div class="phdr"><a href="' . Vars::$URI . '">' . lng('continue') . '</a></div>';
        } else {
            echo'<form action="' . Vars::$URI . '?act=snap" method="post">' .
                '<div class="menu"><p>' . lng('antispy_snapshot_warning') . '</p>' .
                '<p><input type="submit" name="submit" value="' . lng('antispy_snapshot_create') . '" /></p>' .
                '</div></form>' .
                '<div class="phdr"><small>' . lng('antispy_snapshot_help') . '</small></div>';
        }
        break;

    default:
        /*
        -----------------------------------------------------------------
        Главное меню Сканера
        -----------------------------------------------------------------
        */
        $tpl->contents = $tpl->includeTpl('antispy_menu');
}