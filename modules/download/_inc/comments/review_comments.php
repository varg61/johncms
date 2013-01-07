<?php

/**
 * @package     JohnCMS
 * @link        http://johncms.com
 * @copyright   Copyright (C) 2008-2011 JohnCMS Community
 * @license     LICENSE.txt (see attached file)
 * @version     VERSION.txt (see attached file)
 * @author      http://johncms.com/about
 */

defined('_IN_JOHNCMS') or die('Error: restricted access');
/*
-----------------------------------------------------------------
Обзор комментариев
-----------------------------------------------------------------
*/
if(!Vars::$SYSTEM_SET['mod_down_comm'] && Vars::$USER_RIGHTS < 7) {
	echo Functions::displayError(__('comments_cloded'), '<a href="' . Vars::$URI . '">' . __('download_title') . '</a>');
	exit;
}
$textl = __('review_comments');
if(!Vars::$SYSTEM_SET['mod_down_comm'])
	echo '<div class="rmenu">' . __('comments_cloded') . '</div>';
echo '<div class="phdr"><a href="' . Vars::$MODULE_URI . '"><b>' . __('downloads') . '</b></a> | ' . $textl . '</div>';
$total = mysql_result(mysql_query("SELECT COUNT(*) FROM `cms_download_comments`"), 0);
if ($total) {
    $req = mysql_query("SELECT `cms_download_comments`.*, `cms_download_comments`.`id` AS `cid`, `users`.`rights`, `users`.`last_visit`, `users`.`sex`, `users`.`status`, `users`.`join_date`, `users`.`id`, `cms_download_files`.`rus_name`
	FROM `cms_download_comments` LEFT JOIN `users` ON `cms_download_comments`.`user_id` = `users`.`id` LEFT JOIN `cms_download_files` ON `cms_download_comments`.`sub_id` = `cms_download_files`.`id` ORDER BY `cms_download_comments`.`time` DESC " . Vars::db_pagination());
    $i = 0;
	/*
	-----------------------------------------------------------------
	Навигация
	-----------------------------------------------------------------
	*/
	if ($total > Vars::$USER_SET['page_size'])
		echo '<div class="topmenu">' . Functions::displayPagination(Vars::$URI . '?act=review_comments&amp;', Vars::$START, $total, Vars::$USER_SET['page_size']) . '</div>';
	/*
	-----------------------------------------------------------------
	Выводим список
	-----------------------------------------------------------------
	*/
	while ($res = mysql_fetch_assoc($req)) {
        $text = '';
        echo ($i++ % 2) ? '<div class="list2">' : '<div class="list1">';
        $text = ' <span class="gray">(' . Functions::displayDate($res['time']) . ')</span>';
        $post = Validate::checkout($res['text'], 1, 1);
        if (Vars::$USER_SET['smileys']) $post = Functions::smilies($post, $res['rights'] >= 1 ? 1 : 0);
        $subtext = '<a href="index.php?act=view&amp;id=' . $res['sub_id'] . '">' . Validate::checkout($res['rus_name']) . '</a> | <a href="' . Vars::$URI . '?act=comments&amp;id=' . $res['sub_id'] . '">' . __('comments') . '</a>';
		$attributes = unserialize($res['attributes']);
		$res['nickname'] = $attributes['author_name'];
  		$res['ip'] = $attributes['author_ip'];
  		$res['ip_via_proxy'] = isset($attributes['author_ip_via_proxy']) ? $attributes['author_ip_via_proxy'] : 0;
  		$res['user_agent'] = $attributes['author_browser'];
 		if (isset($attributes['edit_count'])) {
        	$post .= '<br /><span class="gray"><small>Изменен: <b>' . $attributes['edit_name'] . '</b>' .
         	' (' . functions::displayDate($attributes['edit_time']) . ') <b>' .
          	'[' . $attributes['edit_count'] . ']</b></small></span>';
   		}
		if (!empty($res['reply'])) {
        	$reply = Validate::checkout($res['reply'], 1, 1);
         	if (Vars::$USER_SET['smileys']) $reply = functions::smilies($reply, $attributes['reply_rights'] >= 1 ? 1 : 0);
          	$post .= '<div class="reply"><small>' .
           	'<a href="' . Vars::$HOME_URL . '?profile.php?user=' . $attributes['reply_id'] . '"><b>' . $attributes['reply_name'] . '</b></a>' .
           	' (' . functions::displayDate($attributes['reply_time']) . ')</small><br/>' . $reply . '</div>';
  		}
		$arg = array (
            'header' => $text,
            'body' => $post,
            'sub' => $subtext
        );
        echo functions::displayUser($res, $arg) . '</div>';
    }
} else {
    echo '<div class="menu"><p>' . __('list_empty') . '</p></div>';
}
echo '<div class="phdr">' . __('total') . ': ' . $total . '</div>';
/*
-----------------------------------------------------------------
Навигация
-----------------------------------------------------------------
*/
if ($total > Vars::$USER_SET['page_size']) {
	echo '<div class="topmenu">' . Functions::displayPagination(Vars::$URI . '?act=review_comments&amp;', Vars::$START, $total, Vars::$USER_SET['page_size']) . '</div>' .
 	'<p><form action="' . Vars::$URI . '" method="get">' .
  	'<input type="hidden" value="review_comments" name="act" />' .
    '<input type="text" name="page" size="2"/><input type="submit" value="' . __('to_page') . ' &gt;&gt;"/></form></p>';
}
echo '<p><a href="' . Vars::$URI . '">' . __('download_title') . '</a></p>';