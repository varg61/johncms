<?php

/**
 * @package     JohnCMS
 * @link        http://johncms.com
 * @copyright   Copyright (C) 2008-2012 JohnCMS Community
 * @license     LICENSE.txt (see attached file)
 * @version     VERSION.txt (see attached file)
 * @author      http://johncms.com/about
 */

class Advt extends Vars
{
    public static function getAds()
    {
        $ads = array();
        $view = Vars::$USER_ID ? 2 : 1;
        $layout = (parent::$PLACE == 'index.php' && !Vars::$ACT) ? 1 : 2;
        $req = mysql_query("SELECT * FROM `cms_ads` WHERE `to` = '0' AND (`layout` = '$layout' or `layout` = '0') AND (`view` = '$view' or `view` = '0') ORDER BY  `mesto` ASC");
        if (mysql_num_rows($req)) {
            while (($res = mysql_fetch_assoc($req)) !== false) {
                $name = explode("|", $res['name']);
                $name = htmlentities($name[mt_rand(0, (count($name) - 1))], ENT_QUOTES, 'UTF-8');
                if (!empty($res['color'])) $name = '<span style="color:#' . $res['color'] . '">' . $name . '</span>';
                // Если было задано начертание шрифта, то применяем
                $font = $res['bold'] ? 'font-weight: bold;' : false;
                $font .= $res['italic'] ? ' font-style:italic;' : false;
                $font .= $res['underline'] ? ' text-decoration:underline;' : false;
                if ($font) $name = '<span style="' . $font . '">' . $name . '</span>';
                @$ads[$res['type']] .= '<a href="' . ($res['show'] ? Validate::filterString($res['link']) : Vars::$HOME_URL . '/go.php?id=' . $res['id']) . '">' . $name . '</a><br/>';
                if (($res['day'] != 0 && time() >= ($res['time'] + $res['day'] * 3600 * 24)) || ($res['count_link'] != 0 && $res['count'] >= $res['count_link']))
                    mysql_query("UPDATE `cms_ads` SET `to` = '1'  WHERE `id` = '" . $res['id'] . "'");
            }
        }
        return $ads;
    }
}
