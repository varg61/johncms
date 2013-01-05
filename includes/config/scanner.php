<?php

/**
 * @package     JohnCMS
 * @link        http://johncms.com
 * @copyright   Copyright (C) 2008-2012 JohnCMS Community
 * @license     LICENSE.txt (see attached file)
 * @version     VERSION.txt (see attached file)
 * @author      http://johncms.com/about
 */

$scanFolders = array(
    '',
    '/assets',
    '/files',
    '/images',
    '/includes',
    '/install',
    '/modules',
    '/templates',
);

$whiteList = array(
    './.htaccess',
    './index.php',

    './assets/avatars/index.php',
    './assets/captcha/.htaccess',
    './assets/index.php',
    './assets/misc/album_image.php',
    './assets/misc/forum_thumbinal.php',
    './assets/misc/rating.php',
    './assets/misc/thumbinal.php',
    './assets/misc/vote_img.php',
    './assets/smileys/index.php',
    './assets/template/modules/message.php',
    './assets/template/modules/template.php',
    './assets/template/modules/toolbar-bottom.php',
    './assets/template/modules/toolbar-top.php',

    './files/.htaccess',
    './files/download/about/.htaccess',
    './files/download/files/index.php',
    './files/download/screen/index.php',
    './files/download/temp/.htaccess',
    './files/download/temp/created_java/index.php',
    './files/download/temp/created_zip/.htaccess',
    './files/download/temp/index.php',
    './files/forum/index.php',
    './files/library/index.php',
    './files/users/album/index.php',
    './files/users/avatar/index.php',
    './files/users/index.php',
    './files/users/photo/index.php',
    './files/users/pm/index.php',

    './includes/.htaccess',
    './includes/core.php',
    './includes/config/db.php',
    './includes/config/scanner.php',
    './includes/classes/advt.php',
    './includes/classes/captcha.php',
    './includes/classes/comments.php',
    './includes/classes/counters.php',
    './includes/classes/fields.php',
    './includes/classes/form.php',
    './includes/classes/functions.php',
    './includes/classes/languages.php',
    './includes/classes/network.php',
    './includes/classes/robotsdetect.php',
    './includes/classes/session.php',
    './includes/classes/sitemap.php',
    './includes/classes/statistic.php',
    './includes/classes/system.php',
    './includes/classes/template.php',
    './includes/classes/textparser.php',
    './includes/classes/validate.php',
    './includes/classes/vars.php',
    './includes/lib/class.upload.php',
    './includes/lib/getid3/getid3.lib.php',
    './includes/lib/getid3/getid3.php',
    './includes/lib/getid3/module.audio.mp3.php',
    './includes/lib/getid3/module.tag.id3v1.php',
    './includes/lib/getid3/module.tag.id3v2.php',
    './includes/lib/getid3/write.id3v1.php',
    './includes/lib/getid3/write.id3v2.php',
    './includes/lib/getid3/write.php',
    './includes/lib/mp3.php',
    './includes/lib/pclerror.lib.php',
    './includes/lib/pcltar.lib.php',
    './includes/lib/pcltrace.lib.php',
    './includes/lib/pclzip.lib.php',
    './includes/lib/pear.php',
    './includes/lib/Tar.php',

    './modules/.htaccess',
    '',

    '',
);