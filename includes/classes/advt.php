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
        $layout = empty(static::$PLACE) && !Vars::$ACT ? 1 : 2;
        $STH = DB::PDO()->query("SELECT * FROM `cms_ads` WHERE `to` = '0' AND (`layout` = '$layout' or `layout` = '0') AND (`view` = '$view' or `view` = '0') ORDER BY  `mesto` ASC");
        if ($STH->rowCount()) {
            while ($result = $STH->fetch()) {
                $name = explode("|", $result['name']);
                $name = htmlentities($name[mt_rand(0, (count($name) - 1))], ENT_QUOTES, 'UTF-8');
                if (!empty($result['color'])) $name = '<span style="color:#' . $result['color'] . '">' . $name . '</span>';
                // Если было задано начертание шрифта, то применяем
                $font = $result['bold'] ? 'font-weight: bold;' : FALSE;
                $font .= $result['italic'] ? ' font-style:italic;' : FALSE;
                $font .= $result['underline'] ? ' text-decoration:underline;' : FALSE;
                if ($font) $name = '<span style="' . $font . '">' . $name . '</span>';
                //TODO: Переделать ссылку редиректа
                @$ads[$result['type']] .= '<a href="' . ($result['show'] ? Functions::checkout($result['link']) : Vars::$HOME_URL . '/go.php?id=' . $result['id']) . '">' . $name . '</a><br/>';
                if (($result['day'] != 0 && time() >= ($result['time'] + $result['day'] * 3600 * 24)) || ($result['count_link'] != 0 && $result['count'] >= $result['count_link']))
                    DB::PDO()->exec("UPDATE `cms_ads` SET `to` = '1'  WHERE `id` = '" . $result['id'] . "'");
            }
        }

        return $ads;
    }
}
