<?php

////////////////////////////////////////////////////////////
// Предварительная проверка системы                       //
// 1) Проверка настроек PHP                               //
// 2) Проверка необходимых расширений PHP                 //
// 2) Проверка прав доступа к файлам и папкам             //
////////////////////////////////////////////////////////////
$err = false;
// Проверка настроек PHP
echo '<h2>Настройки PHP</h2><ul>';
if (version_compare(phpversion(), '5.1.0', '>')) {
    echo '<div><span class="green">OK</span> - Версия PHP ' . phpversion() . '</div>';
} else {
    $err = 1;
    echo '<div><span class="red">ОШИБКА! - Версия PHP ' . phpversion() . ' устаревшая и не поддерживается системой.</span></div>';
}
if (!ini_get('register_globals')) {
    echo '<div><span class="green">OK</span> - register_globals OFF</div>';
} else {
    $err = 2;
    echo '<div><span class="red">Внимание! - register_globals OFF</span><br /><span class="gray">Вы можете продолжить установку, однако система в большей степени будет подвержена уязвимостям.</span></div>';
}
if (ini_get('arg_separator.output') == '&amp;') {
    echo '<div><span class="green">OK</span> - arg_separator.output "&amp;amp;"</div>';
} else {
    $err = 2;
    echo '<div><span class="red">Внимание! - arg_separator.output "' . htmlentities(ini_get('arg_separator.output')) . '"</span><br />';
    echo '<span class="gray">Вы можете продолжить установку, однако настоятельно рекомендуется установить этот параметр на "&amp;amp;",<br /> иначе будут неправильно обрабатываться гиперссылки в xHTML.</span></div>';
}

// Проверка загрузки необходимых расширений PHP
echo '</ul><br /><h2>Расширения PHP</h2><ul>';
if (extension_loaded('mysql')) {
    echo '<div><span class="green">OK</span> - mysql</div>';
} else {
    $err = 1;
    echo '<div><span class="red">ОШИБКА! - расширение "mysql" не загружено</span></div>';
}
if (extension_loaded('gd')) {
    echo '<div><span class="green">OK</span> - gd</div>';
} else {
    $err = 1;
    echo '<div><span class="red">ОШИБКА! - расширение "gd" не загружено</span></div>';
}
if (extension_loaded('zlib')) {
    echo '<div><span class="green">OK</span> - zlib</div>';
} else {
    $err = 1;
    echo '<div><span class="red">ОШИБКА! - расширение "zlib" не загружено</span></div>';
}
if (extension_loaded('iconv')) {
    echo '<div><span class="green">OK</span> - iconv</div>';
} else {
    $err = 1;
    echo '<div><span class="red">ОШИБКА! - расширение "iconv" не загружено</span></div>';
}
if (extension_loaded('mbstring')) {
    echo '<div><span class="green">OK</span> - mb_string</div>';
} else {
    $err = 1;
    echo '<div><span class="red">Ошибка! - расширение "mbstring" не загружено</span><br />';
    echo '<span class="gray">Если Вы тестируете сайт локально на "Денвере", то там, в настройках по умолчанию данное расширение не подключено.<br />';
    echo 'Вам необходимо (для Денвера) открыть файл php.ini, который находится в папке /usr/local/php5 (или php4, в зависимости от версии) и отредактировать строку ;extension=php_mbstring.dll убрав точку с запятой в начале строки.</span></div>';
}

// Проверка прав доступа к файлам и папкам
function permissions($filez) {
    $filez = @decoct(@fileperms("../$filez")) % 1000;
    return $filez;
}
$cherr = '';

// Проверка прав доступа к папкам
$arr = array (
    'files/forum/attach/',
    'files/forum/topics/',
    'files/users/avatar/',
    'files/users/photo/',
    'files/users/pm/',
    'files/cache/',
    'incfiles/',
    'gallery/foto/',
    'gallery/temp/',
    'library/files/',
    'library/temp/',
    'download/arctemp/',
    'download/files/',
    'download/graftemp/',
    'download/screen/',
    'download/mp3temp/',
    'download/upl/'
);
foreach ($arr as $v) {
    if (permissions($v) < 777) {
        $cherr = $cherr . '<div class="smenu"><span class="red">ОШИБКА! - ' . $v . '</span><br /><span class="gray">Необходимо установить права доступа 777.</span></div>';
        $err = 1;
    } else {
        $cherr = $cherr . '<div class="smenu"><span class="green">OK</span> - ' . $v . '</div>';
    }
}

// Проверка прав доступа к файлам
$arr = array (
    'library/java/textfile.txt',
    'library/java/META-INF/MANIFEST.MF'
);
foreach ($arr as $v) {
    if (permissions($v) < 666) {
        $cherr = $cherr . '<div class="smenu"><span class="red">ОШИБКА! - ' . $v . '</span><br/><span class="gray">Необходимо установить права доступа 666.</span></div>';
        $err = 1;
    } else {
        $cherr = $cherr . '<div class="smenu"><span class="green">OK</span> - ' . $v . '</div>';
    }
}
echo '</ul><br /><h2>Права доступа</h2><ul>';
echo $cherr;
echo '</ul><hr />';
switch ($err) {
    case '1':
        echo '<span class="red">Внимание!</span> Имеются критические ошибки!<br />Вы не сможете продолжить инсталляцию, пока не устраните их.';
        echo '<p clss="step"><a class="button" href="index.php?act=check">Проверить заново</a></p>';
        break;

    case '2':
        echo '<span class="red">Внимание!</span> Имеются ошибки в конфигурации!<br />Вы можете продолжить инсталляцию, однако нормальная работа системы не гарантируется.';
        echo '<p class="step"><a class="button" href="index.php?act=check">Проверить заново</a> <a class="button" href="index.php?act=db">Продолжить установку</a></p>';
        break;

    default:
        echo '<span class="green">Отлично!</span><br />Все настройки правильные.<p><a class="button" href="index.php?act=db">Продолжить установку</a></p>';
}
?>