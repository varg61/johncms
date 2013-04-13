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
$set_mail = unserialize($user['set_mail']);
$out = '';
$total = 0;
$ch = 0;
$mod = isset($_REQUEST['mod']) ? $_REQUEST['mod'] : '';

if ($id) {
    $req = mysql_query("SELECT * FROM `users` WHERE `id` = '$id' LIMIT 1;");
    if (mysql_num_rows($req) == 0) {
        $textl = $lng['mail'];
        require_once('../incfiles/head.php');
        echo functions::display_error($lng['error_user_not_exist']);
        require_once("../incfiles/end.php");
        exit;
    }
    $qs = mysql_fetch_assoc($req);
    if ($mod == 'clear') {
        $textl = $lng['mail'];
        require_once('../incfiles/head.php');

        if (isset($_POST['clear'])) {
            $count_message = mysql_result(mysql_query("SELECT COUNT(*) FROM `cms_mail` WHERE ((`user_id`='$id' AND `from_id`='$user_id') OR (`user_id`='$user_id' AND `from_id`='$id')) AND `delete`!='$user_id';"), 0);
            if ($count_message) {
                $req = mysql_query("SELECT `cms_mail`.* FROM `cms_mail` WHERE ((`cms_mail`.`user_id`='$id' AND `cms_mail`.`from_id`='$user_id') OR (`cms_mail`.`user_id`='$user_id' AND `cms_mail`.`from_id`='$id')) AND `cms_mail`.`delete`!='$user_id' LIMIT " . $count_message);
                while (($row = mysql_fetch_assoc($req)) !== FALSE) {
                    if ($row['delete']) {
                        if ($row['file_name']) {
                            if (file_exists('../files/mail/' . $row['file_name']) !== FALSE)
                                @unlink('../files/mail/' . $row['file_name']);
                        }
                        mysql_query("DELETE FROM `cms_mail` WHERE `id`='{$row['id']}' LIMIT 1");
                    } else {
                        if ($row['read'] == 0 && $row['user_id'] == $user_id) {
                            if ($row['file_name']) {
                                if (file_exists('../files/mail/' . $row['file_name']) !== FALSE)
                                    @unlink('../files/mail/' . $row['file_name']);
                            }
                            mysql_query("DELETE FROM `cms_mail` WHERE `id`='{$row['id']}' LIMIT 1");
                        } else {
                            mysql_query("UPDATE `cms_mail` SET `delete` = '" . $user_id . "' WHERE `id` = '" . $row['id'] . "' LIMIT 1");
                        }
                    }
                }
            }
            echo '<div class="gmenu">' . $lng_mail['messages_are_removed'] . '</div>';
        } else {
            echo '
			<div class="rmenu">' . $lng_mail['really_messages_removed'] . '</div>
			<div class="gmenu">
			<form action="index.php?act=write&amp;mod=clear&amp;id=' . $id . '" method="post"><div>
			<input type="submit" name="clear" value="' . $lng['delete'] . '"/>
			</div></form>
			</div>';
        }
        echo '<div class="menu"><a href="index.php?act=write&amp;id=' . $id . '">' . $lng['back'] . '</a></div>';
        echo '<div class="menu"><a href="../users/profile.php?act=office">' . $lng_mail['in_office'] . '</a></div>';
        require_once('../incfiles/end.php');
        exit;
    }
}

if (empty($_SESSION['error'])) {
    $_SESSION['error'] = '';
}

$out .= '<div class="phdr"><b>' . $lng['mail'] . '</b></div>';

if (isset($_POST['submit']) && empty($ban['1']) && empty($ban['3'])) {
    if (!$id) {
        $name = isset($_POST['nick']) ? functions::rus_lat(mb_strtolower($_POST['nick'])) : '';
    }
    $text = isset($_POST['text']) ? trim($_POST['text']) : '';
    if ($set_user['translit'] && isset($_POST['msgtrans']))
        $text = functions::trans($text);
    $newfile = '';
    $sizefile = 0;
    $do_file = FALSE;
    $do_file_mini = FALSE;

    $error = array();

    if (!$id && empty($name))
        $error[] = $lng_mail['indicate_login_grantee'];
    if (empty($text))
        $error[] = $lng_mail['message_not_empty'];
    elseif (mb_strlen($text) < 2 || mb_strlen($text) > 5000)
        $error[] = $lng_mail['error_long_message'];
    if (($id && $id == $user_id) || !$id && $datauser['name_lat'] == $name)
        $error[] = $lng_mail['impossible_add_message'];
    $flood = functions::antiflood();
    if ($flood)
        $error[] = $lng['error_flood'] . ' ' . $flood . $lng['sec'];
    if (empty($error)) {
        if (!$id) {
            $query = mysql_query("SELECT * FROM `users` WHERE `name_lat`='" . mysql_real_escape_string($name) . "' LIMIT 1;");
            if (mysql_num_rows($query) == 0) {
                $error[] = $lng['error_user_not_exist'];
            } else {
                $user = mysql_fetch_assoc($query);
                $id = $user['id'];
                $set_mail = unserialize($user['set_mail']);
            }
        } else {
            $set_mail = unserialize($qs['set_mail']);
        }
        if (empty($error)) {
            if ($set_mail) {
                if ($rights < 1) {
                    if ($set_mail['access']) {
                        if ($set_mail['access'] == 1) {
                            $query = mysql_query("SELECT * FROM `cms_contact` WHERE `user_id`='" . $id . "' AND `from_id`='" . $user_id . "' LIMIT 1;");
                            if (mysql_num_rows($query) == 0) {
                                $error[] = $lng_mail['write_contacts'];
                            }
                        } else if ($set_mail['access'] == 2) {
                            $query = mysql_query("SELECT * FROM `cms_contact` WHERE `user_id`='" . $id . "' AND `from_id`='" . $user_id . "' AND `friends`='1' LIMIT 1;");
                            if (mysql_num_rows($query) == 0) {
                                $error[] = $lng_mail['write_friends'];
                            }
                        }
                    }
                }
            }
        }
    }

    function parseFileName($var = '')
    {
        if (empty($var))
            return FALSE;
        $file_ext = pathinfo($var, PATHINFO_EXTENSION);
        $file_body = mb_substr($var, 0, mb_strripos($var, '.'));
        $info['filename'] = mb_strtolower(mb_substr(str_replace('.', '_', $file_body), 0, 38));
        $info['fileext'] = mb_strtolower($file_ext);

        return $info;
    }

    $info = array();
    if (isset($_FILES['fail']['size']) && $_FILES['fail']['size'] > 0) {
        $do_file = TRUE;
        $fname = $_FILES['fail']['name'];
        $fsize = $_FILES['fail']['size'];
        if (!empty($_FILES['fail']['error']))
            $error[] = $lng['error_load_file'];

    } else if (isset($_POST['fail']) && mb_strlen($_POST['fail']) > 0) {
        $do_file_mini = TRUE;
        $array = explode('file=', $_POST['fail']);
        $fname = mb_strtolower($array[0]);
        $filebase64 = $array[1];
        $fsize = strlen(base64_decode($filebase64));
        if (empty($fsize))
            $error[] = $lng['error_load_file'];
    }

    if (empty($error) && ($do_file || $do_file_mini)) {
        // Файлы Windows
        $ext_win = array(
            'exe',
            'msi'
        );
        // Файлы Java
        $ext_java = array(
            'jar',
            'jad'
        );
        // Файлы SIS
        $ext_sis = array(
            'sis',
            'sisx'
        );
        // Файлы документов и тексты
        $ext_doc = array(
            'txt',
            'pdf',
            'doc',
            'rtf',
            'djvu',
            'xls'
        );
        // Файлы картинок
        $ext_pic = array(
            'jpg',
            'jpeg',
            'gif',
            'png',
            'bmp',
            'wmf'
        );
        // Файлы архивов
        $ext_zip = array(
            'zip',
            'rar',
            '7z',
            'tar',
            'gz'
        );
        // Файлы видео
        $ext_video = array(
            '3gp',
            'avi',
            'flv',
            'mpeg',
            'mp4'
        );
        // Звуковые файлы
        $ext_audio = array(
            'mp3',
            'amr'
        );
        $ext = array_merge($ext_win, $ext_java, $ext_sis, $ext_doc, $ext_pic, $ext_zip, $ext_video, $ext_audio);
        $info = parseFileName($fname);
        if (empty($info['filename']))
            $error[] = $lng_mail['error_empty_name_file'];
        if (empty($info['fileext']))
            $error[] = $lng_mail['error_empty_ext_file'];
        if ($fsize > (1024 * $set['flsz']))
            $error[] = $lng_mail['error_max_file_size'];
        if (preg_match("/[^a-z0-9.()+_-]/", $info['filename']))
            $error[] = $lng_mail['error_simbol'];
        if (!in_array($info['fileext'], $ext))
            $error[] = $lng_mail['error_ext_type'] . ': ' . implode(', ', $ext);
        $newfile = $info['filename'] . '.' . $info['fileext'];
        $sizefile = $fsize;
    }

    if (empty($error)) {
        $ignor = mysql_result(mysql_query("SELECT COUNT(*) FROM `cms_contact`
		WHERE `user_id`='" . $user_id . "'
		AND `from_id`='" . $id . "'
		AND `ban`='1';"), 0);
        if ($ignor)
            $error[] = $lng_mail['error_user_ignor_in'];
        if (empty($error)) {
            $ignor_m = mysql_result(mysql_query("SELECT COUNT(*) FROM `cms_contact`
			WHERE `user_id`='" . $id . "'
			AND `from_id`='" . $user_id . "'
			AND `ban`='1';"), 0);
            if ($ignor_m)
                $error[] = $lng_mail['error_user_ignor_out'];
        }
    }

    if (empty($error)) {
        $q = mysql_query("SELECT * FROM `cms_contact`
		WHERE `user_id`='" . $user_id . "' AND `from_id`='" . $id . "';");
        if (mysql_num_rows($q) == 0) {
            mysql_query("INSERT INTO `cms_contact` SET
			`user_id` = '" . $user_id . "',
			`from_id` = '" . $id . "',
			`time` = '" . time() . "'");
            $ch = 1;
        }
        $q1 = mysql_query("SELECT * FROM `cms_contact`
		WHERE `user_id`='" . $id . "' AND `from_id`='" . $user_id . "';");
        if (mysql_num_rows($q1) == 0) {
            mysql_query("INSERT INTO `cms_contact` SET
			`user_id` = '" . $id . "',
			`from_id` = '" . $user_id . "',
			`time` = '" . time() . "'");
            $ch = 1;
        }

    }

    // Проверка наличия файла с таким же именем
    if (!empty($newfile) && file_exists('../files/mail/' . $newfile) !== FALSE) {
        $newfile = $realtime . $newfile;
    }

    if (empty($error) && $do_file) {
        if ((move_uploaded_file($_FILES['fail']['tmp_name'], '../files/mail/' . $newfile)) === TRUE) {
            @ chmod('../files/mail/' . $newfile, 0666);
            @unlink($_FILES['fail']['tmp_name']);
        } else {
            $error[] = $lng['error_load_file'];
        }
    }

    if (empty($error) && $do_file_mini) {
        if (strlen($filebase64) > 0) {
            $FileName = '../files/mail/' . $newfile;
            $filedata = base64_decode($filebase64);
            $fid = @fopen($FileName, "wb");
            if ($fid) {
                if (flock($fid, LOCK_EX)) {
                    fwrite($fid, $filedata);
                    flock($fid, LOCK_UN);
                }
                fclose($fid);
            }
            if (file_exists($FileName) && filesize($FileName) == strlen($filedata)) {
                @ chmod($FileName, 0666);
                unset($FileName);
            } else {
                $error[] = $lng['error_load_file'];
            }
        } else {
            $error[] = $lng['error_load_file'];
        }
    }

    if (empty($error)) {
        mysql_query("INSERT INTO `cms_mail` SET
		`user_id` = '" . $user_id . "',
		`from_id` = '" . $id . "',
		`text` = '" . mysql_real_escape_string($text) . "',
		`time` = '" . time() . "',
		`file_name` = '" . mysql_real_escape_string($newfile) . "',
		`size` = '" . $sizefile . "'") or die(mysql_error());

        mysql_query("UPDATE `users` SET `lastpost` = '" . time() . "' WHERE `id` = '$user_id';");
        if ($ch == 0) {
            mysql_query("UPDATE `cms_contact` SET `time` = '" . time() . "' WHERE `user_id` = '" . $user_id . "' AND
			`from_id` = '" . $id . "';");
            mysql_query("UPDATE `cms_contact` SET `time` = '" . time() . "' WHERE `user_id` = '" . $id . "' AND
			`from_id` = '" . $user_id . "';");
        }
        Header('Location: index.php?act=write' . ($id ? '&id=' . $id : ''));
        exit;
    } else {
        $out .= '<div class="rmenu">' . implode('<br />', $error) . '</div>';
    }
}

if (empty($ban['1']) && empty($ban['3'])) {
    $out .= isset($_SESSION['error']) ? $_SESSION['error'] : '';
    $out .= '<div class="gmenu">
	<form name="form" action="index.php?act=write' . ($id ? '&amp;id=' . $id : '') . '" method="post"  enctype="multipart/form-data"><div>
	' . ($id ? '' : '<b>' . $lng_mail['to_whom'] . ':</b><br/>
	<input type="text" name="nick" maxlength="15" value="' . (!empty($_POST['nick']) ? functions::check($_POST['nick']) : '') . '" /><br />') . '
	<b>' . $lng['message'] . ':</b><br/>';
    if (!$is_mobile)
        $out .= bbcode::auto_bb('form', 'text');
    $out .= '<textarea rows="' . $set_user['field_h'] . '" name="text"></textarea><br/>';
    $out .= '<span style="font-size: x-small;">' . $lng_mail['recommended_long_message'] . '</span><p>';
    if ($set_user['translit'])
        $out .= '<input type="checkbox" name="msgtrans" value="1" ' . (isset($_POST['msgtrans']) ? 'checked="checked" ' : '') . '/> ' . $lng['translit'] . '<br />';
    $out .= '<b>' . $lng_mail['file'] . ':</b><br />';
    if (stristr($agn, 'Opera/8.01') && stristr($agn, 'Opera Mini/2')) {
        $out .= '<input name="fail1" value =""/>&#160;<br/><a href="op:fileselect">' . $lng_mail['select_file'] . '</a>';
    } else {
        $out .= '<input type="file" name="fail"/>';
    }
    $out .= '
	<br /><span style="font-size: x-small;">' . $lng_mail['max_file_size'] . ': ' . formatsize(1024 * $set['flsz']) . '</span>
	</p><input type="submit" name="submit" value="' . $lng['sent'] . '"/>
	</div></form>
	</div>' .
    '<div class="phdr"><b>' . ($id && isset($qs) ? $lng_mail['personal_correspondence'] . ' ' . $qs['name'] : $lng_mail['sending_the_message']) . '</b></div>';
} else {
    $out .= '<div class="rmenu">' . $lng_mail['user_blocked'] . '</div>';
}
if ($id) {
    $total = mysql_result(mysql_query("SELECT COUNT(*) FROM `cms_mail` WHERE ((`user_id`='$id' AND `from_id`='$user_id') OR (`user_id`='$user_id' AND `from_id`='$id')) AND `sys`!='1' AND `delete`!='$user_id' AND `spam`='0'"), 0);
    if ($total) {
        if ($total > $kmess) $out .= '<div class="topmenu">' . functions::display_pagination('index.php?act=write&amp;id=' . $id . '&amp;', $start, $total, $kmess) . '</div>';
        $req = mysql_query("SELECT `cms_mail`.*, `cms_mail`.`id` as `mid`, `cms_mail`.`time` as `mtime`, `users`.* FROM `cms_mail` LEFT JOIN `users` ON `cms_mail`.`user_id`=`users`.`id` WHERE ((`cms_mail`.`user_id`='$id' AND `cms_mail`.`from_id`='$user_id') OR (`cms_mail`.`user_id`='$user_id' AND `cms_mail`.`from_id`='$id')) AND `cms_mail`.`delete`!='$user_id' AND `cms_mail`.`sys`!='1' AND `cms_mail`.`spam`='0' ORDER BY `cms_mail`.`time` DESC LIMIT "
            . $start . "," . $kmess);
        $i = 1;
        $mass_read = array();
        while (($row = mysql_fetch_assoc($req)) !== FALSE) {
            $out .= $i % 2 ? '<div class="list1">' : '<div class="list2">';
            if ($row['read'] == 0 && $row['from_id'] == $user_id)
                $mass_read[] = $row['mid'];
            $post = $row['text'];
            $post = functions::checkout($post, 1, 1);
            if ($set_user['smileys'])
                $post = functions::smileys($post, $row['rights'] >= 1 ? 1 : 0);
            if ($row['file_name'])
                $post .= '<div class="func">' . $lng_mail['file'] . ': <a href="index.php?act=load&amp;id=' . $row['mid'] . '">' . $row['file_name'] . '</a> (' . formatsize($row['size']) . ')(' . $row['count'] . ')</div>';
            $subtext = '<a href="index.php?act=delete&amp;id=' . $row['mid'] . '">' . $lng['delete'] . '</a>';
            $arg = array(
                'header' => '(' . functions::display_date($row['mtime']) . ')',
                'body'   => $post,
                'sub'    => $subtext
            );
            $out .= functions::display_user($row, $arg);
            $out .= '</div>';
            ++$i;
        }
        //Ставим метку о прочтении
        if ($mass_read) {
            $result = implode(',', $mass_read);
            mysql_query("UPDATE `cms_mail` SET `read`='1' WHERE `from_id`='$user_id' AND `id` IN (" . $result . ")");
        }
    } else {
        $out .= '<div class="menu"><p>' . $lng['list_empty'] . '</p></div>';
    }

    $out .= '<div class="phdr">' . $lng['total'] . ': ' . $total . '</div>';
    if ($total > $kmess) {
        $out .= '<div class="topmenu">' . functions::display_pagination('index.php?act=write&amp;id=' . $id . '&amp;', $start, $total, $kmess) . '</div>';
        $out .= '<p><form action="index.php" method="get">
			<input type="hidden" name="act" value="write"/>
			<input type="hidden" name="id" value="' . $id . '"/>
			<input type="text" name="page" size="2"/>
			<input type="submit" value="' . $lng['to_page'] . ' &gt;&gt;"/></form></p>';
    }
}

$textl = $lng['mail'];
require_once('../incfiles/head.php');
echo $out;
echo '<p>';
if ($total) {
    echo '<a href="index.php?act=write&amp;mod=clear&amp;id=' . $id . '">' . $lng_mail['clear_messages'] . '</a><br/>';
}
echo '<a href="../users/profile.php?act=office">' . $lng['personal'] . '</a></p>';
unset($_SESSION['error']);