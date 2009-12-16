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

$textl = 'Восстановление пароля';
require_once ("../incfiles/core.php");
require_once ("../incfiles/head.php");
if ($_GET['act'] == "go")
{
    $namm = check(trim($_POST['namm']));
    $q = @mysql_query("select * from `users` where name='" . $namm . "';");
    $arr = @mysql_fetch_array($q);
    $arr2 = mysql_num_rows($q);
    if ($arr2 == 0)
    {
        echo "Этого логина нет в базе данных<br/>";
        echo "<a href=\"?\">Назад</a><br/>";
        require_once ("../incfiles/end.php");
        exit;
    }
    if (isset($_GET['continue']))
    {
        $codepas = intval($_POST['codepas']);
        if ($arr[kod] != $codepas)
        {
            echo "Указан неверный код<br/>";
            echo "<a href=\"?\">Назад</a><br/>";
            require_once ("../incfiles/end.php");
            exit;
        }
        $newpas = rand(100000, 999999);
        $newpass = md5(md5($newpas));
        if ($_SESSION['newpar1'] != 1)
        {
            mysql_query("update `users` set password='" . $newpass . "' where name='" . $namm . "';");
            $subject = "New password: Step 2";
            $mail = "Здравствуйте\r\nВы успешно восстановили пароль на сайте " . $copyright . "\r\nВаш новый пароль" . $newpas . " . \r\n";

            $subject = utfwin($subject);

            $name = utfwin($name);
            $mail = utfwin($mail);

            $name = convert_cyr_string($name, 'w', 'k');
            $subject = convert_cyr_string($subject, 'w', 'k');
            $mail = convert_cyr_string($mail, 'w', 'k');
            $adds = "From: <" . $emailadmina . ">\n";

            $adds .= "X-sender: <" . $emailadmina . ">\n";
            $adds .= "Content-Type: text/plain; charset=koi8-r\n";

            $adds .= "MIME-Version: 1.0\r\n";
            $adds .= "Content-Transfer-Encoding: 8bit\r\n";
            $adds .= "X-Mailer: PHP v." . phpversion();

            mail($arr[mail], $subject, $mail, $adds);

            echo 'Новый пароль выслан по указанному адресу<br/>';
            $_SESSION['newpar1'] = 1;
        } else
        {
            echo "Новый пароль уже выслан<br/>";
        }
    } else
    {
        $email = htmlspecialchars(stripslashes(trim($_POST['email'])));

        if ($arr[mail] == "")
        {
            echo "В анкете не указан e-mail адрес<br/>";
            echo "<a href=\"?\">Назад</a><br/>";
            require_once ("../incfiles/end.php");
            exit;
        }

        if ($arr[mailact] != 1)
        {
            echo "Не активирован e-mail адрес<br/>";
            echo "<a href=\"?\">Назад</a><br/>";
            require_once ("../incfiles/end.php");
            exit;
        }

        if ($arr[mail] != $email)
        {
            echo "Указан неверный e-mail адрес<br/>";
            echo "<a href=\"?\">Назад</a><br/>";
            require_once ("../incfiles/end.php");
            exit;
        }
        $pascod = rand(100000, 999999);
        if ($_SESSION['newpar'] != 1)
        {
            mysql_query("update `users` set kod='" . $pascod . "' where name='" . $namm . "';");
            $subject = "New password: Step 1";
            $mail = "Здравствуйте\r\nКто то,возможно Вы,запустили процедуру восстановления пароля на сайте " . $copyright . "\r\nКод для восстановления пароля " . $pascod . " . Теперь Вы можете продолжить восстановление\r\n";

            $subject = utfwin($subject);

            $name = utfwin($name);
            $mail = utfwin($mail);

            $name = convert_cyr_string($name, 'w', 'k');
            $subject = convert_cyr_string($subject, 'w', 'k');
            $mail = convert_cyr_string($mail, 'w', 'k');
            $adds = "From: <" . $emailadmina . ">\n";

            $adds .= "X-sender: <" . $emailadmina . ">\n";
            $adds .= "Content-Type: text/plain; charset=koi8-r\n";

            $adds .= "MIME-Version: 1.0\r\n";
            $adds .= "Content-Transfer-Encoding: 8bit\r\n";
            $adds .= "X-Mailer: PHP v." . phpversion();

            mail($email, $subject, $mail, $adds);

            echo 'Код для восстановления выслан по указанному адресу<br/>';
            $_SESSION['newpar'] = 1;
        } else
        {
            echo "Код для восстановления уже выслан<br/>";
        }
    }


    require_once ("../incfiles/end.php");
    exit;
}

if (empty($_GET['act']))
{
    if (isset($_GET['continue']))
    {
        echo "Продолжаем восстановление пароля<br/>";
        print '<form action=\'?act=go&amp;continue\' method=\'post\'>Ваш логин:<br/>' . '<input type=\'text\' name=\'namm\' value=\'\' format=\'*N\'/><br/>Код для восстановления:<br/>' . '<input type=\'text\' name=\'codepas\' value=\'\' format=\'*N\'/><br/><br/>' .
            '<input type=\'submit\' value=\'ok\'/></form>';

        require_once ("../incfiles/end.php");
        exit;
    }

    echo "Восстановление пароля<br/>(Для этого у Вас в анкете должен быть указан и активирован e-mail адрес)<br/>";
    print '<form action=\'?act=go\' method=\'post\'>Ваш логин:<br/>' . '<input type=\'text\' name=\'namm\' value=\'\' format=\'*N\'/><br/>Ваш e-mail:<br/>' . '<input type=\'text\' name=\'email\' value=\'\' format=\'*N\'/><br/><br/>' .
        '<input type=\'submit\' value=\'ok\'/></form>';

}


require_once ("../incfiles/end.php");