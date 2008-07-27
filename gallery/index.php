<?php
/*
////////////////////////////////////////////////////////////////////////////////
// JohnCMS                             Content Management System              //
// Официальный сайт сайт проекта:      http://johncms.com                     //
// Дополнительный сайт поддержки:      http://gazenwagen.com                  //
////////////////////////////////////////////////////////////////////////////////
// JohnCMS core team:                                                         //
// Евгений Рябинин aka john77          john77@gazenwagen.com                  //
// Олег Касьянов aka AlkatraZ          alkatraz@gazenwagen.com                //
//                                                                            //
// Информацию о версиях смотрите в прилагаемом файле version.txt              //
////////////////////////////////////////////////////////////////////////////////
*/

define('_IN_JOHNCMS', 1);

$headmod = 'gallery';
$textl = 'Галерея сайта';
require_once ("../incfiles/core.php");
require_once ("../incfiles/head.php");

$act = isset($_GET['act']) ? $_GET['act'] : '';
$do = array('new', 'edf', 'delf', 'edit', 'del', 'delmes', 'addkomm', 'trans', 'komm', 'preview', 'load', 'upl', 'cral', 'album', 'razd');
if (in_array($act, $do))
{
    require_once ($act . '.php');
} else
{
    if (!empty($_GET['id']))
    {
        $id = intval($_GET['id']);
        $type = mysql_query("select * from `gallery` where id='" . $id . "';");
        $ms = mysql_fetch_array($type);
        switch ($ms[type])
        {
            case "rz":

                $al = mysql_query("select * from `gallery` where type='al' and  refid='" . $id . "' order by time desc;");
                $count = mysql_num_rows($al);
                if (empty($_GET['page']))
                {
                    $page = 1;
                } else
                {
                    $page = intval($_GET['page']);
                }
                $start = $page * 10 - 10;
                if ($count < $start + 10)
                {
                    $end = $count;
                } else
                {
                    $end = $start + 10;
                }
                echo '<p><b>' . $ms['text'] . '</b></p><hr />';
                while ($al1 = mysql_fetch_array($al))
                {
                    if ($i >= $start && $i < $end)
                    {
                        $fot = mysql_query("select * from `gallery` where type='ft' and  refid='" . $al1['id'] . "';");
                        $countf = mysql_num_rows($fot);
                        echo '<div class="menu"><img alt="" src="../images/arrow.gif" width="7" height="12" />&nbsp;<a href="index.php?id=' . $al1['id'] . '">' . $al1['text'] . '</a> (' . $countf . ')</div>';
                    }
                    ++$i;
                }
                echo "<hr/><p>";
                if ($count > 10)
                {
                    $ba = ceil($count / 10);
                    if ($offpg != 1)
                    {
                        echo "Страницы:<br/>";
                    } else
                    {
                        echo "Страниц: $ba<br/>";
                    }

                    if ($start != 0)
                    {
                        echo '<a href="index.php?id=' . $id . '&amp;page=' . ($page - 1) . '">&lt;&lt;</a> ';
                    }
                    $asd = $start - 10;
                    $asd2 = $start + 20;
                    if ($offpg != 1)
                    {
                        if ($asd < $count && $asd > 0)
                        {
                            echo ' <a href="index.php?id=' . $id . '&amp;page=1">1</a> .. ';
                        }
                        $page2 = $ba - $page;
                        $pa = ceil($page / 2);
                        $paa = ceil($page / 3);
                        $pa2 = $page + floor($page2 / 2);
                        $paa2 = $page + floor($page2 / 3);
                        $paa3 = $page + (floor($page2 / 3) * 2);
                        if ($page > 13)
                        {
                            echo ' <a href="index.php?id=' . $id . '&amp;page=' . $paa . '">' . $paa . '</a> <a href="index.php?id=' . $id . '&amp;page=' . ($paa + 1) . '">' . ($paa + 1) . '</a> .. <a href="index.php?id=' . $id . '&amp;page=' . ($paa * 2) . '">' . ($paa *
                                2) . '</a> <a href="index.php?id=' . $id . '&amp;page=' . ($paa * 2 + 1) . '">' . ($paa * 2 + 1) . '</a> .. ';
                        } elseif ($page > 7)
                        {
                            echo ' <a href="index.php?id=' . $id . '&amp;page=' . $pa . '">' . $pa . '</a> <a href="index.php?id=' . $id . '&amp;page=' . ($pa + 1) . '">' . ($pa + 1) . '</a> .. ';
                        }
                        for ($i = $asd; $i < $asd2; )
                        {
                            if ($i < $count && $i >= 0)
                            {
                                $ii = floor(1 + $i / 10);

                                if ($start == $i)
                                {
                                    echo " <b>$ii</b>";
                                } else
                                {
                                    echo ' <a href="index.php?id=' . $id . '&amp;page=' . $ii . '">' . $ii . '</a> ';
                                }
                            }
                            $i = $i + 10;
                        }
                        if ($page2 > 12)
                        {
                            echo ' .. <a href="index.php?id=' . $id . '&amp;page=' . $paa2 . '">' . $paa2 . '</a> <a href="index.php?id=' . $id . '&amp;page=' . ($paa2 + 1) . '">' . ($paa2 + 1) . '</a> .. <a href="index.php?id=' . $id . '&amp;page=' . ($paa3) . '">' . ($paa3) .
                                '</a> <a href="index.php?id=' . $id . '&amp;page=' . ($paa3 + 1) . '">' . ($paa3 + 1) . '</a> ';
                        } elseif ($page2 > 6)
                        {
                            echo ' .. <a href="index.php?id=' . $id . '&amp;page=' . $pa2 . '">' . $pa2 . '</a> <a href="index.php?id=' . $id . '&amp;page=' . ($pa2 + 1) . '">' . ($pa2 + 1) . '</a> ';
                        }
                        if ($asd2 < $count)
                        {
                            echo ' .. <a href="index.php?id=' . $id . '&amp;page=' . $ba . '">' . $ba . '</a>';
                        }
                    } else
                    {
                        echo "<b>[$page]</b>";
                    }
                    if ($count > $start + 10)
                    {
                        echo ' <a href="index.php?id=' . $id . '&amp;page=' . ($page + 1) . '">&gt;&gt;</a>';
                    }
                    echo "<form action='index.php'>Перейти к странице:<br/><input type='hidden' name='id' value='" . $id .
                        "'/><input type='text' name='page' title='Введите номер страницы'/><br/><input type='submit' title='Нажмите для перехода' value='Go!'/></form>";
                }
                echo "Всего альбомов: $count<br/>";
                if (!empty($_SESSION['uid']) && $ms['user'] == 1)
                {
                    $alb = mysql_query("select * from `gallery` where type='al' and  refid='" . $id . "' and avtor='" . $login . "';");
                    $cnt = mysql_num_rows($alb);
                    if ($cnt == 1)
                    {
                        $alb1 = mysql_fetch_array($alb);
                        echo "<a href='index.php?id=" . $alb1['id'] . "'>В свой альбом</a><br/>";
                    } else
                    {
                        echo "<a href='index.php?act=album&amp;id=" . $id . "'>Создать свой альбом</a><br/>";
                    }
                }
                if ($dostsmod == 1)
                {
                    echo "<a href='index.php?act=cral&amp;id=" . $id . "'>Создать новый альбом</a><br/>";
                    echo "<a href='index.php?act=del&amp;id=" . $id . "'>Удалить раздел</a><br/>";
                    echo "<a href='index.php?act=edit&amp;id=" . $id . "'>Изменить раздел</a><br/>";
                }
                echo "<a href='index.php'>В галерею</a></p>";
                break;

            case "al":
                $delimag = opendir("temp");
                while ($imd = readdir($delimag))
                {
                    if ($imd != "." && $imd != ".." && $imd != "index.php")
                    {
                        $im[] = $imd;
                    }
                }
                closedir($delimag);
                $totalim = count($im);
                for ($imi = 0; $imi < $totalim; $imi++)
                {
                    $filtime[$imi] = filemtime("temp/$im[$imi]");
                    $tim = time();
                    $ftime1 = $tim - 10;
                    if ($filtime[$imi] < $ftime1)
                    {
                        unlink("temp/$im[$imi]");
                    }
                }
                echo "<p>Альбом <b>$ms[text]</b></p><hr/>";
                $fot = mysql_query("select * from `gallery` where type='ft' and  refid='" . $id . "' order by time desc;");
                $count = mysql_num_rows($fot);
                if (empty($_GET['page']))
                {
                    $page = 1;
                } else
                {
                    $page = intval($_GET['page']);
                }
                $start = $page * 10 - 10;
                if ($count < $start + 10)
                {
                    $end = $count;
                } else
                {
                    $end = $start + 10;
                }
                while ($fot1 = mysql_fetch_array($fot))
                {
                    if ($i >= $start && $i < $end)
                    {
                        $d = $i / 2;
                        $d1 = ceil($d);
                        $d2 = $d1 - $d;
                        $d3 = ceil($d2);
                        if ($d3 == 0)
                        {
                            $div = "<div class='c'>";
                        } else
                        {
                            $div = "<div class='b'>";
                        }
                        echo "$div&nbsp;<a href='index.php?id=" . $fot1['id'] . "'>";
                        $infile = "foto/$fot1[name]";
                        if (!empty($_SESSION['frazm']))
                        {
                            $razm = $_SESSION['frazm'];
                        } else
                        {
                            $razm = 50;
                        }
                        $sizs = GetImageSize($infile);
                        $width = $sizs[0];
                        $height = $sizs[1];
                        $quality = 80;
                        $x_ratio = $razm / $width;
                        $y_ratio = $razm / $height;
                        if (($width <= $razm) && ($height <= $razm))
                        {
                            $tn_width = $width;
                            $tn_height = $height;
                        } else
                            if (($x_ratio * $height) < $razm)
                            {
                                $tn_height = ceil($x_ratio * $height);
                                $tn_width = $razm;
                            } else
                            {
                                $tn_width = ceil($y_ratio * $width);
                                $tn_height = $razm;
                            }
                            $format = format($infile);
                        switch ($format)
                        {
                            case "gif":
                                $im = ImageCreateFromGIF($infile);
                                break;
                            case "jpg":
                                $im = ImageCreateFromJPEG($infile);
                                break;
                            case "jpeg":
                                $im = ImageCreateFromJPEG($infile);
                                break;
                            case "png":
                                $im = ImageCreateFromPNG($infile);
                                break;
                        }
                        $im1 = imagecreatetruecolor($tn_width, $tn_height);
                        $namefile = "$fot1[name]";
                        imagecopyresized($im1, $im, 0, 0, 0, 0, $tn_width, $tn_height, $width, $height);
                        switch ($format)
                        {
                            case "gif":
                                $imagnam = "temp/$namefile.temp.gif";
                                ImageGif($im1, $imagnam, $quality);
                                echo "<img src='" . $imagnam . "' alt=''/><br/>";
                                break;
                            case "jpg":
                                $imagnam = "temp/$namefile.temp.jpg";
                                imageJpeg($im1, $imagnam, $quality);
                                echo "<img src='" . $imagnam . "' alt=''/><br/>";
                                break;
                            case "jpeg":
                                $imagnam = "temp/$namefile.temp.jpg";
                                imageJpeg($im1, $imagnam, $quality);
                                echo "<img src='" . $imagnam . "' alt=''/><br/>";

                                break;
                            case "png":
                                $imagnam = "temp/$namefile.temp.png";
                                imagePng($im1, $imagnam, $quality);
                                echo "<img src='" . $imagnam . "' alt=''/><br/>";

                                break;
                        }
                        imagedestroy($im);
                        imagedestroy($im1);
                        $fotsz = filesize("foto/$ms[name]");
                        $vrf = $fot1['time'] + $sdvig * 3600;
                        $vrf1 = date("d.m.y / H:i", $vrf);
                        $kom = mysql_query("select * from `gallery` where type='km' and refid='" . $fot1['id'] . "';");
                        $kom1 = mysql_num_rows($kom);
                        echo '</a>';
                        if (!empty($fot1['text']))
                            echo "$fot1[text]<br/>";
                        if ($kom1 > 0)
                            echo "<a href='index.php?act=komm&amp;id=" . $fot1['id'] . "'>Комментарии</a> ($kom1)<br/>";
                        //echo "<a href='foto/$fot1[name]'>Скачать</a><br />";
                        if ($dostsmod == 1)
                        {
                            echo "<a href='index.php?act=edf&amp;id=" . $fot1['id'] . "'>Изменить</a> | <a href='index.php?act=delf&amp;id=" . $fot1['id'] . "'>Удалить</a><br/>";
                        }
                        echo "</div>";
                    }
                    ++$i;
                }
                echo "<hr/><p>";
                if ($count > 10)
                {
                    $ba = ceil($count / 10);
                    if ($offpg != 1)
                    {
                        echo "Страницы:<br/>";
                    } else
                    {
                        echo "Страниц: $ba<br/>";
                    }

                    if ($start != 0)
                    {
                        echo '<a href="index.php?id=' . $id . '&amp;page=' . ($page - 1) . '">&lt;&lt;</a> ';
                    }

                    $asd = $start - 10;
                    $asd2 = $start + 20;
                    if ($offpg != 1)
                    {
                        if ($asd < $count && $asd > 0)
                        {
                            echo ' <a href="index.php?id=' . $id . '&amp;page=1">1</a> .. ';
                        }
                        $page2 = $ba - $page;
                        $pa = ceil($page / 2);
                        $paa = ceil($page / 3);
                        $pa2 = $page + floor($page2 / 2);
                        $paa2 = $page + floor($page2 / 3);
                        $paa3 = $page + (floor($page2 / 3) * 2);
                        if ($page > 13)
                        {
                            echo ' <a href="index.php?id=' . $id . '&amp;page=' . $paa . '">' . $paa . '</a> <a href="index.php?id=' . $id . '&amp;page=' . ($paa + 1) . '">' . ($paa + 1) . '</a> .. <a href="index.php?id=' . $id . '&amp;page=' . ($paa * 2) . '">' . ($paa *
                                2) . '</a> <a href="index.php?id=' . $id . '&amp;page=' . ($paa * 2 + 1) . '">' . ($paa * 2 + 1) . '</a> .. ';
                        } elseif ($page > 7)
                        {
                            echo ' <a href="index.php?id=' . $id . '&amp;page=' . $pa . '">' . $pa . '</a> <a href="index.php?id=' . $id . '&amp;page=' . ($pa + 1) . '">' . ($pa + 1) . '</a> .. ';
                        }
                        for ($i = $asd; $i < $asd2; )
                        {
                            if ($i < $count && $i >= 0)
                            {
                                $ii = floor(1 + $i / 10);

                                if ($start == $i)
                                {
                                    echo " <b>$ii</b>";
                                } else
                                {
                                    echo ' <a href="index.php?id=' . $id . '&amp;page=' . $ii . '">' . $ii . '</a> ';
                                }
                            }
                            $i = $i + 10;
                        }
                        if ($page2 > 12)
                        {
                            echo ' .. <a href="index.php?id=' . $id . '&amp;page=' . $paa2 . '">' . $paa2 . '</a> <a href="index.php?id=' . $id . '&amp;page=' . ($paa2 + 1) . '">' . ($paa2 + 1) . '</a> .. <a href="index.php?id=' . $id . '&amp;page=' . ($paa3) . '">' . ($paa3) .
                                '</a> <a href="index.php?id=' . $id . '&amp;page=' . ($paa3 + 1) . '">' . ($paa3 + 1) . '</a> ';
                        } elseif ($page2 > 6)
                        {
                            echo ' .. <a href="index.php?id=' . $id . '&amp;page=' . $pa2 . '">' . $pa2 . '</a> <a href="index.php?id=' . $id . '&amp;page=' . ($pa2 + 1) . '">' . ($pa2 + 1) . '</a> ';
                        }
                        if ($asd2 < $count)
                        {
                            echo ' .. <a href="index.php?id=' . $id . '&amp;page=' . $ba . '">' . $ba . '</a>';
                        }
                    } else
                    {
                        echo "<b>[$page]</b>";
                    }
                    if ($count > $start + 10)
                    {
                        echo ' <a href="index.php?id=' . $id . '&amp;page=' . ($page + 1) . '">&gt;&gt;</a>';
                    }
                    echo "<form action='index.php'>Перейти к странице:<br/><input type='hidden' name='id' value='" . $id .
                        "'/><input type='text' name='page' title='Введите номер страницы'/><br/><input type='submit' title='Нажмите для перехода' value='Go!'/></form>";
                }
                if ($count != 0)
                {
                    echo "Всего фотографий: $count<br/>";
                } else
                {
                    echo "В этом альбоме нет фотографий<br/>";
                }
                $rz = mysql_query("select * from `gallery` where type='rz' and  id='" . $ms['refid'] . "';");
                $rz1 = mysql_fetch_array($rz);
                if ((!empty($_SESSION['uid']) && $rz1['user'] == 1 && $ms['text'] == $login) || ($dostsmod == 1))
                {
                    echo "<a href='index.php?act=upl&amp;id=" . $id . "'>Выгрузить фото</a><br/>";
                }
                if ($dostsmod == 1)
                {
                    echo "<a href='index.php?act=del&amp;id=" . $id . "'>Удалить альбом</a><br/>";
                    echo "<a href='index.php?act=edit&amp;id=" . $id . "'>Изменить альбом</a><br/>";
                }
                echo "<a href='index.php?id=" . $ms['refid'] . "'>$rz1[text]</a><br/>";
                echo "<a href='index.php'>В галерею</a></p>";
                break;

            case "ft":
                echo "<br/>&nbsp;";
                $infile = "foto/$ms[name]";

                if (!empty($_SESSION['frazm']))
                {
                    $razm = $_SESSION['frazm'];
                } else
                {
                    $razm = 50;
                }
                $sizs = GetImageSize($infile);
                $width = $sizs[0];
                $height = $sizs[1];
                $quality = 85;
                $format = format($infile);
                switch ($format)
                {
                    case "gif":
                        $im = ImageCreateFromGIF($infile);
                        break;
                    case "jpg":
                        $im = ImageCreateFromJPEG($infile);
                        break;
                    case "jpeg":
                        $im = ImageCreateFromJPEG($infile);
                        break;
                    case "png":
                        $im = ImageCreateFromPNG($infile);
                        break;
                }
                $im1 = imagecreatetruecolor($width, $height);
                $namefile = "$ms[name]";
                imagecopy($im1, $im, 0, 0, 0, 0, $width, $height);
                switch ($format)
                {
                    case "gif":
                        $imagnam = "temp/$namefile.gif";
                        ImageGif($im1, $imagnam, $quality);
                        echo "<img src='" . $imagnam . "' alt=''/><br/>";
                        break;
                    case "jpg":
                        $imagnam = "temp/$namefile.jpg";
                        imageJpeg($im1, $imagnam, $quality);
                        echo "<img src='" . $imagnam . "' alt=''/><br/>";
                        break;
                    case "jpeg":
                        $imagnam = "temp/$namefile.jpg";
                        imageJpeg($im1, $imagnam, $quality);
                        echo "<img src='" . $imagnam . "' alt=''/><br/>";

                        break;
                    case "png":
                        $imagnam = "temp/$namefile.png";
                        imagePng($im1, $imagnam, $quality);
                        echo "<img src='" . $imagnam . "' alt=''/><br/>";

                        break;
                }
                imagedestroy($im);
                imagedestroy($im1);
                $fotsz = filesize("foto/$ms[name]");
                $fotsz = round($fotsz / 1024, 2);
                $sizs = GetImageSize("foto/$ms[name]");
                $fwidth = $sizs[0];
                $fheight = $sizs[1];
                $vrf = $ms[time] + $sdvig * 3600;
                $vrf1 = date("d.m.y / H:i", $vrf);
                echo "<p>Подпись: $ms[text]<br/>";
                $kom = mysql_query("select * from `gallery` where type='km' and refid='" . $id . "';");
                $kom1 = mysql_num_rows($kom);
                echo "<a href='index.php?act=komm&amp;id=" . $id . "'>Комментарии</a> ($kom1)<br/>";
                echo "Размеры: $fwidth*$fheight пкс.<br/>";
                echo "Вес: $fotsz кб.<br/>";
                echo "Добавлено: $vrf1<br/>";
                echo "Разместил: $ms[avtor]<br/>";
                echo "<a href='foto/$ms[name]'>Скачать</a><br /><br />";
                echo "<a href='index.php?id=" . $ms['refid'] . "'>Назад</a><br/>";
                echo "<a href='index.php'>В галерею</a></p>";
                break;

            default:
                header("location: index.php");
                break;
        }
    } else
    {
        // Главная страница Галлереи
        echo '<p><a href="index.php?act=new">Новые фото</a> (' . fgal(1) . ')</p><hr/>';
        $rz = mysql_query("select * from `gallery` where type='rz';");
        $count = mysql_num_rows($rz);
        while ($rz1 = mysql_fetch_array($rz))
        {
            $al = mysql_query("select * from `gallery` where type='al' and  refid='" . $rz1['id'] . "';");
            $countal = mysql_num_rows($al);
            echo '<div class="menu"><img alt="" src="../images/arrow.gif" width="7" height="12" />&nbsp;<a href="index.php?id=' . $rz1['id'] . '">' . $rz1['text'] . '</a> (' . $countal . ')</div>';
        }
        echo '<hr /><p>';
        if ($count != 0)
        {
            echo "Всего разделов: $count<br/>";
        } else
        {
            echo "Разделы не созданы!<br/>";
        }
        if ($dostsmod == 1)
        {
            echo "<a href='index.php?act=razd'>Создать раздел</a><br/>";
        }
        echo "<a href='index.php?act=preview'>Размеры изображений</a></p>";
    }
}

require_once ("../incfiles/end.php");

?>