<?php

/**
 * @package     JohnCMS
 * @link        http://johncms.com
 * @copyright   Copyright (C) 2008-2012 JohnCMS Community
 * @license     LICENSE.txt (see attached file)
 * @version     VERSION.txt (see attached file)
 * @author      http://johncms.com/about
 */

class Scanner
{
    public $folders = array();
    public $whiteList = array();
    public $snap_base = 'scan_snapshot.dat';
    public $snap_files = array();
    public $bad_files = array();
    public $snap = FALSE;
    public $track_files = array();

    /**
     * Сканирование на соответствие дистрибутиву
     */
    function scan()
    {
        foreach ($this->folders as $data) {
            $this->scan_files(ROOT_DIR . $data);
        }
    }

    /**
     * Сканирование по образу
     */
    function snapscan()
    {
        if (file_exists(CACHEPATH . $this->snap_base)) {
            $filecontents = file(CACHEPATH . $this->snap_base);
            foreach ($filecontents as $name => $value) {
                $filecontents[$name] = explode("|", trim($value));
                $this->track_files[$filecontents[$name][0]] = $filecontents[$name][1];
            }
            $this->snap = TRUE;
        }

        foreach ($this->folders as $data) {
            $this->scan_files(ROOT_DIR . $data);
        }
    }

    /**
     * Добавляем снимок надежных файлов в базу
     */
    function snap()
    {
        foreach ($this->folders as $data) {
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

    /**
     * Служебная функция рекурсивного сканирования файловой системы
     *
     * @param $dir
     * @param bool $snap
     */
    function scan_files($dir, $snap = FALSE)
    {
        if ($dh = @opendir($dir)) {
            while (FALSE !== ($file = readdir($dh))) {
                if ($file == '.' || $file == '..' || $file == '.svn') {
                    continue;
                }
                if (is_dir($dir . '/' . $file)) {
                    if ($dir != ROOT_DIR) {
                        $this->scan_files($dir . '/' . $file, $snap);
                    }
                } else {
                    if ($this->snap or $snap) {
                        $templates = "|tpl";
                    } else
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
                                if (!isset($this->track_files[$folder . '/' . $file]) || $this->track_files[$folder . '/' . $file] != $file_crc)
                                    $this->bad_files[] = array(
                                        'file_path' => $folder . '/' . $file,
                                        'file_name' => $file,
                                        'file_date' => $file_date,
                                        'type'      => 1,
                                        'file_size' => $file_size
                                    );
                            } else {
                                if (!in_array($folder . '/' . $file, $this->whiteList) or $file_size > 300000)
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
