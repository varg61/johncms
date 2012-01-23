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
    function __construct()
    {
        $contents = ob_get_contents();
        if (parent::$TEMPLATE !== false && !empty($contents)) {
            ob_end_clean();
            if (parent::$SYSTEM_SET['gzip'] && @extension_loaded('zlib')) {
                @ini_set('zlib.output_compression_level', 3);
                ob_start('ob_gzhandler');
            }

            $this->_loadTemplate($contents);
        }
    }

    private function _loadTemplate($contents)
    {
        $system_template = SYSPATH . 'templates' . DIRECTORY_SEPARATOR . parent::$TEMPLATE . '.php';
        $user_template = ROOTPATH . 'theme' . DIRECTORY_SEPARATOR . parent::$USER_SET['skin'] . DIRECTORY_SEPARATOR . parent::$TEMPLATE . '.php';
        if (is_file($user_template)) {
            include_once($user_template);
        } elseif (is_file($system_template)) {
            include_once($system_template);
        } else {
            echo Functions::displayError('Template &laquo;<b>' . parent::$TEMPLATE . '</b>&raquo; not found');
        }
    }

    public function httpHeaders($expires = 60)
    {
        if (stristr(parent::$USERAGENT, "msie") && stristr(parent::$USERAGENT, "windows")) {
            header("Cache-Control: no-store, no-cache, must-revalidate");
            header('Content-type: text/html; charset=UTF-8');
        } else {
            header("Cache-Control: public");
            header('Content-type: application/xhtml+xml; charset=UTF-8');
        }
        header("Expires: " . date("r", time() + $expires));
    }

    public function htmlHeaders()
    {
        echo'<?xml version="1.0" encoding="utf-8"?>' . "\n" .
            '<!DOCTYPE html PUBLIC "-//WAPFORUM//DTD XHTML Mobile 1.0//EN" "http://www.wapforum.org/DTD/xhtml-mobile10.dtd">' . "\n" .
            '<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="ru">' . "\n" .
            '<head>' . "\n" .
            '<meta http-equiv="content-type" content="application/xhtml+xml; charset=utf-8"/>' . "\n" .
            '<meta http-equiv="Content-Style-Type" content="text/css"/>' . "\n" .
            '<meta name="Generator" content="JohnCMS, http://johncms.com"/>' . "\n" . // Данный копирайт удалять нельзя!
            '<meta name="keywords" content="' . parent::$SYSTEM_SET['meta_key'] . '"/>' . "\n" .
            '<meta name="description" content="' . parent::$SYSTEM_SET['meta_desc'] . '"/>' . "\n" .
            '<link rel="stylesheet" href="' . parent::$HOME_URL . '/theme/' . parent::$USER_SET['skin'] . '/style.css" type="text/css"/>' . "\n" .
            '<link rel="shortcut icon" href="' . parent::$HOME_URL . '/favicon.ico"/>' . "\n" .
            '<link rel="alternate" type="application/rss+xml" title="RSS | ' . parent::$LNG['site_news'] . '" href="' . parent::$HOME_URL . '/rss/rss.php"/>' . "\n" .
            '<title>' . parent::$TITLE . '</title>' . "\n" .
            '</head>' . "\n" .
            '<body>' . "\n";
    }

    public function htmlEnd()
    {
        /*
        -----------------------------------------------------------------
        ВНИМАНИЕ!!!
        Данный копирайт нельзя убирать в течение 60 дней с момента установки скриптов
        -----------------------------------------------------------------
        ATTENTION!!!
        The copyright could not be removed within 60 days of installation scripts
        -----------------------------------------------------------------
        */
        echo '<div style="text-align:center"><small>&copy; <a href="http://johncms.com">JohnCMS</a></small></div>';
        echo"\n" . '</body>' .
            "\n" . '</html>';
    }

    public function displayLogo()
    {
        echo '<table style="width: 100%;"><tr>' .
            '<td valign="bottom"><a href="' . parent::$HOME_URL . '">' . Functions::getImage('logo.gif', parent::$SYSTEM_SET['copyright']) . '</a></td>' .
            (parent::$PLACE == 'index.php' && count(parent::$LNG_LIST) > 1 ? '<td align="right"><a href="' . parent::$HOME_URL . '/go.php?lng"><b>' . strtoupper(parent::$LNG_ISO) . '</b></a>&#160;<img src="' . parent::$HOME_URL . '/images/flags/' . parent::$LNG_ISO . '.gif" alt=""/>&#160;</td>' : '') .
            '</tr></table>';
    }

    public function displayUserGreeting($class)
    {
        echo'<div class="' . $class . '">' . parent::$LNG['hi'] . ', ';
        if (parent::$USER_ID) {
            echo'<b>' . parent::$USER_DATA['nickname'] . '</b>';
        } else {
            echo parent::$LNG['guest'];
        }
        echo'!</div>';
    }

    public function displayTopMenu($class)
    {
        echo'<div class="' . $class . '">' .
            (isset($_GET['err']) || parent::$PLACE != 'index.php' || (parent::$PLACE == 'index.php' && parent::$ACT) ? '<a href=\'' . parent::$SYSTEM_SET['homeurl'] . '\'>' . parent::$LNG['homepage'] . '</a> | ' : '') .
            (parent::$USER_ID ? '<a href="' . parent::$HOME_URL . '/users/profile.php?act=office">' . parent::$LNG['personal'] . '</a> | ' : '') .
            (parent::$USER_ID ? '<a href="' . parent::$HOME_URL . '/exit.php">' . parent::$LNG['exit'] . '</a>' : '<a href="' . parent::$HOME_URL . '/login.php">' . parent::$LNG['login'] . '</a> | <a href="' . parent::$HOME_URL . '/registration.php">' . parent::$LNG['registration'] . '</a>') .
            '</div>';
    }

    public function displayUserBan($class)
    {
        if (!empty(parent::$USER_BAN)) {
            echo '<div class="' . $class . '">' . parent::$LNG['ban'] . '&#160;<a href="' . parent::$HOME_URL . '/users/profile.php?act=ban">' . parent::$LNG['in_detail'] . '</a></div>';
        }
    }

    public function displayNotifications()
    {
        if (Vars::$USER_ID) {
            //$list = array();
            //$new_mail = mysql_result(mysql_query("SELECT COUNT(*) FROM `privat` WHERE `user` = '$login' AND `type` = 'in' AND `chit` = 'no'"), 0);
            //if ($new_mail) $list[] = '<a href="' . Vars::$system_set['homeurl'] . '/users/pradd.php?act=in&amp;new">' . Vars::$lng['mail'] . '</a>&#160;(' . $new_mail . ')';
            //if ($datauser['comm_count'] > $datauser['comm_old']) $list[] = '<a href="' . Vars::$system_set['homeurl'] . '/users/profile.php?act=guestbook&amp;user=' . $user_id . '">' . Vars::$lng['guestbook'] . '</a> (' . ($datauser['comm_count'] - $datauser['comm_old']) . ')';
            //$new_album_comm = mysql_result(mysql_query("SELECT COUNT(*) FROM `cms_album_files` WHERE `user_id` = '" . Vars::$user_id . "' AND `unread_comments` = 1"), 0);
            //if($new_album_comm) $list[] = '<a href="' . Vars::$system_set['homeurl'] . '/users/album.php?act=top&amp;mod=my_new_comm">' . Vars::$lng['albums_comments'] . '</a>';

            //if (!empty($list)) echo '<div class="rmenu">' . Vars::$lng['unread'] . ': ' . Functions::display_menu($list, ', ') . '</div>';
        }
    }

    public function displayBottomMenu($class)
    {
        echo '</div><div class="fmenu">';

        // Ссылка на главную
        if (parent::$PLACE != 'index.php' || (parent::$PLACE == 'index.php' && parent::$ACT)) {
            echo '<a href="' . parent::$HOME_URL . '">' . parent::$LNG['homepage'] . '</a><br/>';
        }

        // Меню быстрого перехода
        if (parent::$USER_SET['quick_go']) {
            echo'<form action="' . parent::$HOME_URL . '/go.php" method="post">';
            echo'<div><select name="adres" style="font-size:x-small">' .
                '<option selected="selected">' . parent::$LNG['quick_jump'] . '</option>' .
                '<option value="guest">' . parent::$LNG['guestbook'] . '</option>' .
                '<option value="forum">' . parent::$LNG['forum'] . '</option>' .
                '<option value="news">' . parent::$LNG['news'] . '</option>' .
                '<option value="gallery">' . parent::$LNG['gallery'] . '</option>' .
                '<option value="down">' . parent::$LNG['downloads'] . '</option>' .
                '<option value="lib">' . parent::$LNG['library'] . '</option>' .
                '<option value="gazen">Gazenwagen :)</option>' .
                '</select><input type="submit" value="Go!" style="font-size:x-small"/>';
            echo '</div></form>';
        }
    }

    public function displayUsersOnline($class)
    {
        echo '<div class="' . $class . '">' . Counters::usersOnline() . '</div>';
    }
}
