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
$id = isset($_POST['admin_id']) ? abs(intval($_POST['admin_id'])) : false;
$act = isset($_POST['admin_act']) ? trim($_POST['admin_act']) : '';
if($act == 'clean')
	header('Location: ' . Vars::$URI . '?act=scan_dir&do=clean&id=' . $id);
else
	header('Location: ' . Vars::$URI . '?act=' . $act . '&id=' . $id);