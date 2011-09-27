<?php

/**
 * @package     JohnCMS
 * @link        http://johncms.com
 * @copyright   Copyright (C) 2008-2011 JohnCMS Community
 * @license     LICENSE.txt (see attached file)
 * @version     VERSION.txt (see attached file)
 * @author      http://johncms.com/about
 */

define('_IN_JOHNCMS', 1);
require('../incfiles/core.php');
$lng_faq = core::load_lng('faq');
$textl = 'FAQ';
$headmod = 'faq';
require('../incfiles/head.php');

// Обрабатываем ссылку для возврата
if (empty($_SESSION['ref'])) {
    $_SESSION['ref'] = htmlspecialchars($_SERVER['HTTP_REFERER']);
}

// Сколько смайлов разрешено выбрать пользователям?
$user_smileys = 20;

switch ($act) {
    case 'forum':
        /*
        -----------------------------------------------------------------
        Правила Форума
        -----------------------------------------------------------------
        */
        echo '<div class="phdr"><a href="faq.php"><b>F.A.Q.</b></a> | ' . $lng_faq['forum_rules'] . '</div>' .
             '<div class="menu"><p>' . $lng_faq['forum_rules_text'] . '</p></div>' .
             '<div class="phdr"><a href="' . $_SESSION['ref'] . '">' . $lng['back'] . '</a></div>';
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
             '<tr><td align="right">[php]...[/php]</td><td>' . $lng['tag_code'] . '</td></tr>' .
             '<tr><td align="right"><a href="#">' . $lng['link'] . '</a></td><td>[url=http://site_url] .]<span style="color:blue">' . $lng_faq['tags_link_name'] . '</span>[/url]</td></tr>' .
             '<tr><td align="right">[b]...[/b]</td><td><b>' . $lng['tag_bold'] . '</b></td></tr>' .
             '<tr><td align="right">[i]...[/i]</td><td><i>' . $lng['tag_italic'] . '</i></td></tr>' .
             '<tr><td align="right">[u]...[/u]</td><td><u>' . $lng['tag_underline'] . '</u></td></tr>' .
             '<tr><td align="right">[s]...[/s]</td><td><strike>' . $lng['tag_strike'] . '</strike></td></tr>' .
             '<tr><td align="right">[red]...[/red]</td><td><span style="color:red">' . $lng['tag_red'] . '</span></td></tr>' .
             '<tr><td align="right">[green]...[/green]</td><td><span style="color:green">' . $lng['tag_green'] . '</span></td></tr>' .
             '<tr><td align="right">[blue]...[/blue]</td><td><span style="color:blue">' . $lng['tag_blue'] . '</span></td></tr>' .
             '<tr><td align="right">[color=]...[/color]</td><td>' . $lng['color_text'] . '</td></tr>' .
             '<tr><td align="right">[bg=][/bg]</td><td>' . $lng['color_bg'] . '</td></tr>' .
             '<tr><td align="right">[c]...[/c]</td><td><span class="quote">' . $lng['tag_quote'] . '</span></td></tr>' .
             '<tr><td align="right" valign="top">[*]...[/*]</td><td><span class="bblist">' . $lng['tag_list'] . '</span></td></tr>' .
             '</table>' .
             '</p></div>' .
             '<div class="phdr"><a href="' . $_SESSION['ref'] . '">' . $lng['back'] . '</a></div>';
        break;

    case 'trans':
        /*
        -----------------------------------------------------------------
        Справка по Транслиту
        -----------------------------------------------------------------
        */
        echo '<div class="phdr"><a href="faq.php"><b>F.A.Q.</b></a> | ' . $lng_faq['translit_help'] . '</div>' .
             '<div class="menu"><p>' . $lng_faq['translit_help_text'] . '</p></div>' .
             '<div class="phdr"><a href="' . $_SESSION['ref'] . '">' . $lng['back'] . '</a></div>';
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
        if (core::$user_set['translit']) echo '<div class="menu"><a href="faq.php?act=trans">' . $lng_faq['translit_help'] . '</a></div>';
        echo '<div class="menu"><a href="avatars.php">' . $lng['avatars'] . '</a></div>' .
             '<div class="menu"><a href="smileys.php">' . $lng['smileys'] . '</a></div>' .
             '<div class="phdr"><a href="' . $_SESSION['ref'] . '">' . $lng['back'] . '</a></div>';
}

require('../incfiles/end.php');