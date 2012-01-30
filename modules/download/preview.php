<?php

/**
 * @package     JohnCMS
 * @link        http://johncms.com
 * @copyright   Copyright (C) 2008-2012 JohnCMS Community
 * @license     LICENSE.txt (see attached file)
 * @version     VERSION.txt (see attached file)
 * @author      http://johncms.com/about
 */

defined('_IN_JOHNCMS') or die('Error: restricted access');

if (isset ($_POST['submit'])) {
    if (!empty ($_POST['razmer'])) {
        $razmer = intval($_POST['razmer']);
    }
    $_SESSION['razm'] = $razmer;
    echo $lng_dl['preview_size_set'] . " $razmer*$razmer px<br/>";
}
else {
    echo "<form action='?act=preview' method='post'><p>
	" . $lng_dl['select_preview_size'] . ":<br/><select name='razmer'>";
    if (!empty ($_SESSION['razm'])) {
        $realr = $_SESSION['razm'];
        echo "<option value='" . $realr . "'>" . $realr . "*" . $realr . "</option>";
    }
    echo
    "<option value='32'>32*32</option>
<option value='50'>50*50</option>
<option value='64'>64*64</option>
<option value='80'>80*80</option>
<option value='100'>100*100</option>
<option value='120'>120*120</option>
<option value='160'>160*160</option>
<option value='200'>200*200</option>
</select><input type='submit' name='submit' value='ok'/></p></form>";
}
echo "<p><a href='?'>" . Vars::$LNG['back'] . "</a></p>";