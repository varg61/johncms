<?php

/**
 * @package     JohnCMS
 * @link        http://johncms.com
 * @copyright   Copyright (C) 2008-2012 JohnCMS Community
 * @license     LICENSE.txt (see attached file)
 * @version     VERSION.txt (see attached file)
 * @author      http://johncms.com/about
 */

class Template extends Vars
{
    private $contents;

    function __construct()
    {
        $this->contents = ob_get_contents();
        if (Vars::$TEMPLATE !== false && !empty($this->contents)) {
            ob_end_clean();
            $this->_startBuffering();
            $this->_getHttpHeaders();
            $this->_getHtmlProlog();

            $this->_getTemplate($this->contents);

            $this->_getHtmlEpilog();
        }
    }

    private function _getTemplate($contents)
    {
        $system = SYSPATH . 'templates' . DIRECTORY_SEPARATOR . parent::$TEMPLATE . '.php';
        $template = ROOTPATH . 'theme' . DIRECTORY_SEPARATOR . Vars::$USER_SET['skin'] . DIRECTORY_SEPARATOR . parent::$TEMPLATE . '.php';
        if (is_file($template)) {
            include_once($template);
        } elseif (is_file($system)) {
            include_once($system);
        } else {
            echo Functions::displayError('Template &laquo;<b>' . parent::$TEMPLATE . '</b>&raquo; not found');
        }
    }

    private function _startBuffering()
    {
        if (parent::$SYSTEM_SET['gzip'] && @extension_loaded('zlib')) {
            @ini_set('zlib.output_compression_level', 3);
            ob_start('ob_gzhandler');
        }
    }

    private function _getHttpHeaders()
    {
        if (stristr(parent::$USERAGENT, "msie") && stristr(parent::$USERAGENT, "windows")) {
            header("Cache-Control: no-store, no-cache, must-revalidate");
            header('Content-type: text/html; charset=UTF-8');
        } else {
            header("Cache-Control: public");
            header('Content-type: application/xhtml+xml; charset=UTF-8');
        }
        header("Expires: " . date("r", time() + 60));
    }

    private function _getHtmlProlog()
    {
        echo'<?xml version="1.0" encoding="utf-8"?>' . "\n" .
            '<!DOCTYPE html PUBLIC "-//WAPFORUM//DTD XHTML Mobile 1.0//EN" "http://www.wapforum.org/DTD/xhtml-mobile10.dtd">' . "\n" .
            '<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="ru">' . "\n" .
            '<head>' . "\n" .
            '<meta http-equiv="content-type" content="application/xhtml+xml; charset=utf-8"/>' . "\n" .
            '<meta http-equiv="Content-Style-Type" content="text/css"/>' . "\n" .
            '<meta name="Generator" content="JohnCMS, http://johncms.com"/>' . "\n" .
            '<meta name="keywords" content="' . parent::$SYSTEM_SET['meta_key'] . '"/>' . "\n" .
            '<meta name="description" content="' . parent::$SYSTEM_SET['meta_desc'] . '"/>' . "\n" .
            '<link rel="stylesheet" href="' . parent::$HOME_URL . '/theme/' . parent::$USER_SET['skin'] . '/style.css" type="text/css"/>' . "\n" .
            '<link rel="shortcut icon" href="' . parent::$HOME_URL . '/favicon.ico"/>' . "\n" .
            '<link rel="alternate" type="application/rss+xml" title="RSS | ' . parent::$LNG['site_news'] . '" href="' . parent::$HOME_URL . '/rss/rss.php"/>' . "\n" .
            '<title>' . parent::$TITLE . '</title>' . "\n" .
            '</head>' . "\n" .
            '<body>' . "\n";
    }

    private function _getHtmlEpilog()
    {
        echo"\n" . '</body>' .
            "\n" . '</html>';
    }
}
