<?php

/**
 * @package     JohnCMS
 * @link        http://johncms.com
 * @copyright   Copyright (C) 2008-2012 JohnCMS Community
 * @license     LICENSE.txt (see attached file)
 * @version     VERSION.txt (see attached file)
 * @author      http://johncms.com/about
 */

defined('_IN_ADMIN') or die('Error: restricted access');
define('ROOT_DIR', '.');

$tpl = Template::getInstance();

class scaner
{
    /*
    -----------------------------------------------------------------
    Сканер - антишпион
    -----------------------------------------------------------------
    */
    public $scan_folders = array(
        '',
        '/assets',
//      '/files',
        '/images',
        '/includes',
        '/install',
//      '/modules',
//      '/templates',
    );

    public $good_files = array(
        './.htaccess',
        './index.php',

        './includes/.htaccess',
        './includes/core.php',
        './includes/config/config.php',
        './includes/classes/advt.php',
        './includes/classes/captcha.php',
        './includes/classes/comments.php',
        './includes/classes/counters.php',
        './includes/classes/fields.php',
        './includes/classes/form.php',
        './includes/classes/functions.php',
        './includes/classes/languages.php',
        './includes/classes/network.php',
        './includes/classes/robotsdetect.php',
        './includes/classes/session.php',
        './includes/classes/sitemap.php',
        './includes/classes/statistic.php',
        './includes/classes/system.php',
        './includes/classes/template.php',
        './includes/classes/textparser.php',
        './includes/classes/validate.php',
        './includes/classes/vars.php',
        './includes/lib/class.upload.php',
        './includes/lib/getid3/getid3.lib.php',
        './includes/lib/getid3/getid3.php',
        './includes/lib/getid3/module.audio.mp3.php',
        './includes/lib/getid3/module.tag.id3v1.php',
        './includes/lib/getid3/module.tag.id3v2.php',
        './includes/lib/getid3/write.id3v1.php',
        './includes/lib/getid3/write.id3v2.php',
        './includes/lib/getid3/write.php',
        './includes/lib/mp3.php',
        './includes/lib/pclerror.lib.php',
        './includes/lib/pcltar.lib.php',
        './includes/lib/pcltrace.lib.php',
        './includes/lib/pclzip.lib.php',
        './includes/lib/pear.php',
        './includes/lib/Tar.php',
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

$form = new Form(Vars::$URI . '?act=scanner');

$form
    ->fieldsetStart(__('select_action'))

    ->add('radio', 'mode', array(
    'checked' => 1,
    'items'   => array(
        '1' => __('antispy_dist_scan'),
        '2' => __('antispy_snapshot_scan'),
        '3' => __('antispy_snapshot_create')
    )))

    ->fieldsetStart()

    ->add('submit', 'submit', array(
    'value' => __('do'),
    'class' => 'btn btn-primary btn-large'))

    ->addHtml('<a class="btn" href="' . Vars::$URI . '">' . __('back') . '</a>');

$tpl->form = $form->display();

if ($form->isSubmitted) {
    switch ($form->validOutput['mode']) {
        case 1:
            // Сканируем на соответствие дистрибутиву
            $scaner->scan();
            if (count($scaner->bad_files)) {
                $tpl->files = $scaner->bad_files;
                $tpl->bad = __('antispy_dist_inconsistency');
            } else {
                $tpl->ok = __('antispy_dist_scan_good');
            }
            break;

        case 2:
            // Сканируем на соответствие ранее созданному снимку
            $scaner->snapscan();
            if (count($scaner->track_files) == 0) {
                $tpl->bad = __('antispy_no_snapshot');
            } else {
                if (count($scaner->bad_files)) {
                    $tpl->files = $scaner->bad_files;
                    $tpl->bad = __('antispy_snp_inconsistency');
                } else {
                    $tpl->ok = __('antispy_snapshot_scan_ok');
                }
            }
            break;

        case 3:
            // Создаем снимок файлов
            $scaner->snap();
            $tpl->ok = __('antispy_snapshot_create_ok');
            break;
    }
}

$tpl->contents = $tpl->includeTpl('spy_scanner');