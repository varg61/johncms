<?php

header((stristr($agn, "msie") && stristr($agn, "windows")) ? 'Content-type: text/html; charset=UTF-8' : 'Content-type: application/xhtml+xml; charset=UTF-8');
echo '<?xml version="1.0" encoding="utf-8"?>' .
    '<!DOCTYPE html PUBLIC "-//WAPFORUM//DTD XHTML Mobile 1.0//EN" "http://www.wapforum.org/DTD/xhtml-mobile10.dtd">' .
    '<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="ru">' .
    '<head><meta http-equiv="content-type" content="application/xhtml+xml; charset=utf-8"/>' .
    '<title>Language TEST</title>' .
    '</head><body><div>';


$language = isset($_POST['language']) ? trim($_POST['language']) : 'ru'; // Язык по-умолчанию
$lng_array = array ();
foreach (glob('languages/*/language.ini') as $var) {
    // Считываем список доступных языков
    $ini = parse_ini_file($var);
    $lng_array[$ini['iso']] = $ini['name'];
}
echo '<h3>Select language</h3>';
echo '<form action="test.php" method="post">';
foreach ($lng_array as $key => $val) {
    // Выбор языка из списка
    echo '<input type="radio" name="language" value="' . $key . '" ' . ($key == $language ? 'checked="checked"' : '') . ' />&#160;' . $val . '<br />';
}
echo '<p><input type="submit" name="submit" value="Select" /></p>';
echo '</form><hr />';
echo '<pre>';
print_r(parse_ini_file('languages/' . $language . '/language.dat', true));
echo '</pre>';
echo '</div></body></html>';
?>