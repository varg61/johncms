<?php

/*
////////////////////////////////////////////////////////////
// MobiCMS  система управления мобильным сайтом           //
// Copyright © 2009 Oleg Kasyanov aka AlkatraZ            //
// E-mail: alkatraz@gazenwagen.com ICQ: 267070            //
////////////////////////////////////////////////////////////
// Официальный сайт сайт проекта:	http://mobicms.net    //
// Дополнительный сайт поддержки:   http://gazenwagen.com //
////////////////////////////////////////////////////////////
// Данный модуль адаптирован для работы с JohnCMS         //
// для модуля действует основная лицензия JohnCMS         //
////////////////////////////////////////////////////////////
*/

// Каталоги, которые нужно сканировать
$scan_folders = array(
'/chat',
'/download',
'/forum',
'/gallery',
'/images',
'/incfiles',
'/install',
'/library',
'/pages',
'/panel',
'/pratt',
'/rss',
'/sm',
'/str',
'/theme',
''
);

// Список файлов, которые входят в дистрибутив
// и исключаются из процесса сканирования
$good_files = array(
'../chat/chat_footer.php',
'../chat/chat_header.php',
'../chat/hall.php',
'../chat/index.php',
'../chat/room.php',
'../chat/who.php',
'../download/addkomm.php',
'../download/arc.php',
'../download/arctemp/index.php',
'../download/cut.php',
'../download/delcat.php',
'../download/delmes.php',
'../download/dfile.php',
'../download/down.php',
'../download/files/.htaccess',
'../download/files/index.php',
'../download/fonts/index.php',
'../download/graftemp/index.php',
'../download/img/index.php',
'../download/import.php',
'../download/index.php',
'../download/komm.php',
'../download/makdir.php',
'../download/mp3.php',
'../download/mp3temp/index.php',
'../download/new.php',
'../download/opis.php',
'../download/preview.php',
'../download/rat.php',
'../download/refresh.php',
'../download/ren.php',
'../download/renf.php',
'../download/screen/index.php',
'../download/screen.php',
'../download/search.php',
'../download/select.php',
'../download/trans.php',
'../download/upl/index.php',
'../download/upl.php',
'../download/view.php',
'../download/zip.php',
'../forum/addfile.php',
'../forum/close.php',
'../forum/delpost.php',
'../forum/deltema.php',
'../forum/editpost.php',
'../forum/faq.php',
'../forum/file.php',
'../forum/files/.htaccess',
'../forum/files/index.php',
'../forum/fmoder.php',
'../forum/index.php',
'../forum/loadtem.php',
'../forum/massdel.php',
'../forum/moders.php',
'../forum/new.php',
'../forum/nt.php',
'../forum/per.php',
'../forum/post.php',
'../forum/read.php',
'../forum/ren.php',
'../forum/say.php',
'../forum/search.php',
'../forum/tema.php',
'../forum/temtemp/index.php',
'../forum/trans.php',
'../forum/vip.php',
'../forum/who.php',
'../gallery/addkomm.php',
'../gallery/album.php',
'../gallery/cral.php',
'../gallery/del.php',
'../gallery/delf.php',
'../gallery/delmes.php',
'../gallery/edf.php',
'../gallery/edit.php',
'../gallery/foto/.htaccess',
'../gallery/foto/index.php',
'../gallery/index.php',
'../gallery/komm.php',
'../gallery/load.php',
'../gallery/new.php',
'../gallery/preview.php',
'../gallery/razd.php',
'../gallery/temp/index.php',
'../gallery/trans.php',
'../gallery/upl.php',
'../images/index.php',
'../incfiles/.htaccess',
'../incfiles/ban.php',
'../incfiles/char.php',
'../incfiles/class_ipinit.php',
'../incfiles/class_mainpage.php',
'../incfiles/core.php',
'../incfiles/db.php',
'../incfiles/end.php',
'../incfiles/func.php',
'../incfiles/head.php',
'../incfiles/index.php',
'../incfiles/mp3.php',
'../incfiles/pclzip.php',
'../incfiles/pear.php',
'../incfiles/usersystem.php',
'../library/addkomm.php',
'../library/del.php',
'../library/edit.php',
'../library/files/index.php',
'../library/index.php',
'../library/java.php',
'../library/komm.php',
'../library/load.php',
'../library/mkcat.php',
'../library/moder.php',
'../library/new.php',
'../library/search.php',
'../library/symb.php',
'../library/temp/index.php',
'../library/topread.php',
'../library/trans.php',
'../library/write.php',
'../pages/index.php',
'../pages/mainmenu.php',
'../panel/chat.php',
'../panel/counters.php',
'../panel/editusers.php',
'../panel/forum.php',
'../panel/index.php',
'../panel/ipban.php',
'../panel/main.php',
'../panel/news.php',
'../panel/preg.php',
'../panel/scan_class.php',
'../panel/scan_list.php',
'../panel/set.php',
'../panel/zaban.php',
'../pratt/.htaccess',
'../pratt/index.php',
'../rss/rss.php',
'../sm/adm/index.php',
'../sm/cat/index.php',
'../sm/index.php',
'../sm/prost/index.php',
'../str/anketa.php',
'../str/brd.php',
'../str/cont.php',
'../str/guest.php',
'../str/ignor.php',
'../str/index.php',
'../str/moders.php',
'../str/news.php',
'../str/online.php',
'../str/pradd.php',
'../str/privat.php',
'../str/skl.php',
'../str/smile.php',
'../str/users.php',
'../str/usset.php',
'../.htaccess',
'../auto.php',
'../code.php',
'../exit.php',
'../go.php',
'../in.php',
'../index.php',
'../read.php',
'../registration.php'
);

?>