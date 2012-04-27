<?php

/**
 * @package     JohnCMS
 * @link        http://johncms.com
 * @copyright   Copyright (C) 2008-2012 JohnCMS Community
 * @license     LICENSE.txt (see attached file)
 * @version     VERSION.txt (see attached file)
 * @author      http://johncms.com/about
 */

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
        'includes',
    );
    public $good_files = array(
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
            $this->scan_files(ROOTPATH . $data);
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
            $this->scan_files(ROOTPATH . $data);
        }
    }

    function snap()
    {
        // Добавляем снимок надежных файлов в базу
        foreach ($this->scan_folders as $data) {
            $this->scan_files(ROOTPATH . $data, true);
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
            echo '</div><div class="phdr">' . lng('total') . ': ' . count($scaner->bad_files) . '</div>';
        } else {
            echo '<div class="gmenu">' . lng('antispy_dist_scan_good') . '</div>';
        }
        echo '<p><a href="index.php?act=antispy&amp;mod=scan">' . lng('antispy_rescan') . '</a></p>';
        break;

    case 'snapscan':
        /*
        -----------------------------------------------------------------
        Сканируем на соответствие ранее созданному снимку
        -----------------------------------------------------------------
        */
        $scaner->snapscan();
        echo '<div class="phdr"><a href="index.php?act=antispy"><b>' . lng('antispy') . '</b></a> | ' . lng('antispy_snapshot_scan') . '</div>';
        if (count($scaner->track_files) == 0) {
            echo Functions::displayError(lng('antispy_no_snapshot'), '<a href="index.php?act=antispy&amp;mod=snap">' . lng('antispy_snapshot_create') . '</a>');
        } else {
            if (count($scaner->bad_files)) {
                echo '<div class="rmenu">' . lng('antispy_snapshot_scan_bad') . '</div>';
                echo '<div class="menu">';
                foreach ($scaner->bad_files as $idx => $data) {
                    echo $data['file_path'] . '<br />';
                }
                echo '</div>';
            } else {
                echo '<div class="gmenu">' . lng('antispy_snapshot_scan_ok') . '</div>';
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
        echo '<div class="phdr"><a href="index.php?act=antispy"><b>' . lng('antispy') . '</b></a> | ' . lng('antispy_snapshot_create') . '</div>';
        if (isset($_POST['submit'])) {
            $scaner->snap();
            echo '<div class="gmenu"><p>' . lng('antispy_snapshot_create_ok') . '</p></div>' .
                 '<div class="phdr"><a href="index.php?act=antispy">' . lng('continue') . '</a></div>';
        } else {
            echo '<form action="index.php?act=antispy&amp;mod=snap" method="post">' .
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