<?php

/**
 * @package     JohnCMS
 * @link        http://johncms.com
 * @copyright   Copyright (C) 2008-2012 JohnCMS Community
 * @license     LICENSE.txt (see attached file)
 * @version     VERSION.txt (see attached file)
 * @author      http://johncms.com/about
 */

class Forum
{
    public static function settings()
    {
        if (!Vars::$USER_ID || ($set_forum = Vars::getUserData('set_forum')) === FALSE) {
            return array(
                'farea'    => 0,
                'upfp'     => 0,
                'preview'  => 1,
                'postclip' => 1,
                'postcut'  => 2
            );
        }

        return $set_forum;
    }

    public static function forum_link($m)
    {
        if (!isset($m[3])) {
            return '[url=' . $m[1] . ']' . $m[2] . '[/url]';
        } else {
            $p = parse_url($m[3]);
            if ('http://' . $p['host'] . $p['path'] . '?id=' == Vars::$HOME_URL . 'forum/?id=') {
                $thid = abs(intval(preg_replace('/(.*?)id=/si', '', $m[3])));
                $req = DB::PDO()->query("SELECT `text` FROM `forum` WHERE `id`= '$thid' AND `type` = 't' AND `close` != '1'");
                if ($req->rowCount()) {
                    $res = $req->fetch();
                    $name = strtr($res['text'], array(
                        '&quot;' => '',
                        '&amp;'  => '',
                        '&lt;'   => '',
                        '&gt;'   => '',
                        '&#039;' => '',
                        '['      => '',
                        ']'      => ''
                    ));
                    if (mb_strlen($name) > 40)
                        $name = mb_substr($name, 0, 40) . '...';

                    return '[url=' . $m[3] . ']' . $name . '[/url]';
                } else {
                    return $m[3];
                }
            } else
                return $m[3];
        }
    }
}
