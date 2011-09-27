<?php

/**
 * @package     JohnCMS
 * @link        http://johncms.com
 * @copyright   Copyright (C) 2008-2011 JohnCMS Community
 * @license     LICENSE.txt (see attached file)
 * @version     VERSION.txt (see attached file)
 * @author      http://johncms.com/about
 */

session_name('SESID');
session_start();
setcookie('cuid', '');
setcookie('cups', '');
session_destroy();
header('Location: index.php');