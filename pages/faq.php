<?php

/**
 * @package     JohnCMS
 * @link        http://johncms.com
 * @copyright   Copyright (C) 2008-2012 JohnCMS Community
 * @license     LICENSE.txt (see attached file)
 * @version     VERSION.txt (see attached file)
 * @author      http://johncms.com/about
 */

define('_IN_JOHNCMS', 1);
require_once('../includes/core.php');
$lng_faq = Vars::loadLanguage('faq');
$textl = 'FAQ';
$headmod = 'faq';
require_once('../includes/head.php');

// Обрабатываем ссылку для возврата
if (empty($_SESSION['ref'])) {
    $_SESSION['ref'] = htmlspecialchars($_SERVER['HTTP_REFERER']);
}

// Сколько смайлов разрешено выбрать пользователям?
$user_smileys = 20;

switch (Vars::$ACT) {
    case 'forum':
        /*
        -----------------------------------------------------------------
        Правила Форума
        -----------------------------------------------------------------
        */
        echo '<div class="phdr"><a href="faq.php"><b>F.A.Q.</b></a> | ' . $lng_faq['forum_rules'] . '</div>' .
             '<div class="menu"><p>' . $lng_faq['forum_rules_text'] . '</p></div>' .
             '<div class="phdr"><a href="' . $_SESSION['ref'] . '">' . Vars::$LNG['back'] . '</a></div>';
        break;

    case 'tags':
        /*
        -----------------------------------------------------------------
        Справка по BBcode
        -----------------------------------------------------------------
        */
        echo '<div class="phdr"><a href="faq.php"><b>F.A.Q.</b></a> | ' . $lng_faq['tags'] . '</div>' .
             '<div class="menu"><p>' .
             '<table cellpadding="3" cellspacing="0">' .
             '<tr><td align="right"><h3>BBcode</h3></td><td></td></tr>' .
             '<tr><td align="right">[php]...[/php]</td><td>' . Vars::$LNG['tag_code'] . '</td></tr>' .
             '<tr><td align="right"><a href="#">' . Vars::$LNG['link'] . '</a></td><td>[url=http://site_url] .]<span style="color:blue">' . $lng_faq['tags_link_name'] . '</span>[/url]</td></tr>' .
             '<tr><td align="right">[b]...[/b]</td><td><b>' . Vars::$LNG['tag_bold'] . '</b></td></tr>' .
             '<tr><td align="right">[i]...[/i]</td><td><i>' . Vars::$LNG['tag_italic'] . '</i></td></tr>' .
             '<tr><td align="right">[u]...[/u]</td><td><u>' . Vars::$LNG['tag_underline'] . '</u></td></tr>' .
             '<tr><td align="right">[s]...[/s]</td><td><strike>' . Vars::$LNG['tag_strike'] . '</strike></td></tr>' .
             '<tr><td align="right">[red]...[/red]</td><td><span style="color:red">' . Vars::$LNG['tag_red'] . '</span></td></tr>' .
             '<tr><td align="right">[green]...[/green]</td><td><span style="color:green">' . Vars::$LNG['tag_green'] . '</span></td></tr>' .
             '<tr><td align="right">[blue]...[/blue]</td><td><span style="color:blue">' . Vars::$LNG['tag_blue'] . '</span></td></tr>' .
             '<tr><td align="right">[color=]...[/color]</td><td>' . Vars::$LNG['color_text'] . '</td></tr>' .
             '<tr><td align="right">[bg=][/bg]</td><td>' . Vars::$LNG['color_bg'] . '</td></tr>' .
             '<tr><td align="right">[c]...[/c]</td><td><span class="quote">' . Vars::$LNG['tag_quote'] . '</span></td></tr>' .
             '<tr><td align="right" valign="top">[*]...[/*]</td><td><span class="bblist">' . Vars::$LNG['tag_list'] . '</span></td></tr>' .
             '</table>' .
             '</p></div>' .
             '<div class="phdr"><a href="' . $_SESSION['ref'] . '">' . Vars::$LNG['back'] . '</a></div>';
        break;

    case 'trans':
        /*
        -----------------------------------------------------------------
        Справка по Транслиту
        -----------------------------------------------------------------
        */
        echo '<div class="phdr"><a href="faq.php"><b>F.A.Q.</b></a> | ' . $lng_faq['translit_help'] . '</div>' .
             '<div class="menu"><p>' . $lng_faq['translit_help_text'] . '</p></div>' .
             '<div class="phdr"><a href="' . $_SESSION['ref'] . '">' . Vars::$LNG['back'] . '</a></div>';
        break;

    default:
        /*
        -----------------------------------------------------------------
        Главное меню FAQ
        -----------------------------------------------------------------
        */
        echo '<div class="phdr"><b>F.A.Q.</b></div>' .
             '<div class="menu"><a href="faq.php?act=forum">' . $lng_faq['forum_rules'] . '</a></div>' .
             '<div class="menu"><a href="faq.php?act=tags">' . $lng_faq['tags'] . '</a></div>';
        if (Vars::$USER_SET['translit']) echo '<div class="menu"><a href="faq.php?act=trans">' . $lng_faq['translit_help'] . '</a></div>';
        echo '<div class="menu"><a href="avatars.php">' . Vars::$LNG['avatars'] . '</a></div>' .
             '<div class="menu"><a href="smileys.php">' . Vars::$LNG['smileys'] . '</a></div>' .
             '<div class="phdr"><a href="' . $_SESSION['ref'] . '">' . Vars::$LNG['back'] . '</a></div>';
}

require_once('../includes/end.php');