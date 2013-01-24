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
    './assets/smilies/index.php',
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
    './includes/classes/db.php',
    './includes/classes/fields.php',
    './includes/classes/form.php',
    './includes/classes/functions.php',
    './includes/classes/languages.php',
    './includes/classes/network.php',
    './includes/classes/router.php',
    './includes/classes/session.php',
    './includes/classes/sitemap.php',
    './includes/classes/system.php',
    './includes/classes/template.php',
    './includes/classes/textparser.php',
    './includes/classes/users.php',
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

    './modules/album/index.php',
    './modules/album/top.php',
    './modules/album/_classes/album.php',
    './modules/album/_inc/comments.php',
    './modules/album/_inc/delete.php',
    './modules/album/_inc/edit.php',
    './modules/album/_inc/image_delete.php',
    './modules/album/_inc/image_download.php',
    './modules/album/_inc/image_edit.php',
    './modules/album/_inc/image_move.php',
    './modules/album/_inc/image_upload.php',
    './modules/album/_inc/list.php',
    './modules/album/_inc/new.php',
    './modules/album/_inc/show.php',
    './modules/album/_inc/sort.php',
    './modules/album/_inc/users.php',
    './modules/album/_inc/vote.php',
    './modules/album/_tpl/album_edit.php',
    './modules/album/_tpl/image_edit.php',
    './modules/album/_tpl/index.php',

    './modules/contacts/index.php',
    './modules/contacts/_inc/archive.php',
    './modules/contacts/_inc/banned.php',
    './modules/contacts/_inc/search.php',
    './modules/contacts/_inc/select.php',
    './modules/contacts/_tpl/archive.php',
    './modules/contacts/_tpl/banned.php',
    './modules/contacts/_tpl/contacts.php',
    './modules/contacts/_tpl/index.php',
    './modules/contacts/_tpl/search.php',
    './modules/contacts/_tpl/select.php',

    './modules/library/contents.php',
    './modules/library/index.php',
    './modules/library/search.php',
    './modules/library/_inc/del.php',
    './modules/library/_inc/edit.php',
    './modules/library/_inc/java.php',
    './modules/library/_inc/load.php',
    './modules/library/_inc/mkcat.php',
    './modules/library/_inc/moder.php',
    './modules/library/_inc/new.php',
    './modules/library/_inc/topread.php',
    './modules/library/_inc/write.php',

    './modules/mail/index.php',
    './modules/mail/_classes/mail.php',
    './modules/mail/_classes/uploadmail.php',
    './modules/mail/_classes/validmail.php',
    './modules/mail/_inc/add.php',
    './modules/mail/_inc/basket.php',
    './modules/mail/_inc/delete.php',
    './modules/mail/_inc/edit.php',
    './modules/mail/_inc/elected.php',
    './modules/mail/_inc/files.php',
    './modules/mail/_inc/inmess.php',
    './modules/mail/_inc/load.php',
    './modules/mail/_inc/messages.php',
    './modules/mail/_inc/new.php',
    './modules/mail/_inc/outmess.php',
    './modules/mail/_inc/read.php',
    './modules/mail/_inc/restore.php',
    './modules/mail/_inc/search.php',
    './modules/mail/_inc/send.php',
    './modules/mail/_inc/settings.php',
    './modules/mail/_tpl/add.php',
    './modules/mail/_tpl/basket.php',
    './modules/mail/_tpl/contacts.php',
    './modules/mail/_tpl/edit.php',
    './modules/mail/_tpl/elected.php',
    './modules/mail/_tpl/files.php',
    './modules/mail/_tpl/inout.php',
    './modules/mail/_tpl/list.php',
    './modules/mail/_tpl/messages.php',
    './modules/mail/_tpl/new.php',
    './modules/mail/_tpl/read.php',
    './modules/mail/_tpl/search.php',
    './modules/mail/_tpl/select.php',
    './modules/mail/_tpl/settings.php',
    './modules/mail/_tpl/time.php',
    './modules/mail/_tpl/_index.php',

    './modules/avatars/index.php',
    './modules/avatars/_tpl/list_avatars.php',
    './modules/avatars/_tpl/list_categories.php',
);