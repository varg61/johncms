<?php

/**
 * @package     JohnCMS
 * @link        http://johncms.com
 * @copyright   Copyright (C) 2008-2012 JohnCMS Community
 * @license     LICENSE.txt (see attached file)
 * @version     VERSION.txt (see attached file)
 * @author      http://johncms.com/about
 */

class Album
{
    public static function vote($arg = NULL)
    {
        if (!$arg) {
            return FALSE;
        }

        $rating = $arg['vote_plus'] - $arg['vote_minus'];

        if ($rating > 0) {
            $color = 'C0FFC0';
        } elseif ($rating < 0) {
            $color = 'F196A8';
        } else {
            $color = 'CCC';
        }

        $out = '<div class="gray">' . __('rating') . ': <span style="color:#000;background-color:#' . $color . '">&#160;&#160;<big><b>' . $rating . '</b></big>&#160;&#160;</span> ' .
            '(' . __('vote_against') . ': ' . $arg['vote_minus'] . ', ' . __('vote_for') . ': ' . $arg['vote_plus'] . ')';

        if (Vars::$USER_ID
            && Vars::$USER_ID != $arg['user_id']
            && empty(Vars::$USER_BAN)
            && Vars::$USER_DATA['count_forum'] > 10
        ) {
            // Проверяем, имеет ли юзер право голоса
            $req = mysql_query("SELECT * FROM `cms_album_votes` WHERE `user_id` = " . Vars::$USER_ID . " AND `file_id` = '" . $arg['id'] . "' LIMIT 1");
            if (!mysql_num_rows($req)) {
                $out .= '<br />' . __('vote') . ': <a href="' . Vars::$URI . '?act=vote&amp;mod=minus&amp;img=' . $arg['id'] . '">&lt;&lt; -1</a> | ';
                $out .= '<a href="' . Vars::$URI . '?act=vote&amp;mod=plus&amp;img=' . $arg['id'] . '">+1 &gt;&gt;</a>';
            }
        }

        $out .= '</div>';
        return $out;
    }
}
