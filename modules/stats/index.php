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

$error = '';

if (!Vars::$SYSTEM_SET['stat'] && Vars::$USER_RIGHTS < 7)
    $error = lng('module_is_disabled');
elseif (Vars::$SYSTEM_SET['stat'] == 1 && Vars::$USER_RIGHTS < 7)
    $error = lng('access_denied');
elseif (Vars::$SYSTEM_SET['stat'] == 2 && !Vars::$USER_ID)
    $error = lng('access_denied');

if ($error) {
    echo Functions::displayError(lng('module_is_disabled'));
    exit;
}

$model = isset($_GET['model']) ? htmlspecialchars((string )$_GET['model']) : '';

$tpl = Template::getInstance();

// Дополнительные страницы //
$actions = array(
    'robots'        => 'robots.php',
    'robot_types'   => 'robot_types.php',
    'hosts'         => 'hosts.php',
    'point_in'      => 'point_in.php',
    'opsos'         => 'opsos.php',
    'allstat'       => 'allstat.php',
    'country'       => 'country.php',
    'users'         => 'users.php',
    'referer'       => 'referer.php',
    'siteadr'       => 'siteadr.php',
    'pop'           => 'pop.php',
    'phones'        => 'phones.php',
    'phone'         => 'phone.php',
    'os'            => 'os.php',
    'stat_search'   => 'stat_search.php',
    'search_engine' => 'search_engine.php',
    'ip_base'       => 'ip_base.php'
);

if (isset($actions[Vars::$ACT]) && is_file(MODPATH . Vars::$MODULE .
    DIRECTORY_SEPARATOR . '_inc' . DIRECTORY_SEPARATOR . $actions[Vars::$ACT])
) {

    $back_links = '';
    include_once (MODPATH . Vars::$MODULE . DIRECTORY_SEPARATOR . '_inc' .
        DIRECTORY_SEPARATOR . $actions[Vars::$ACT]);
    echo '<div class="gmenu">' . $back_links . '<a href="' . Vars::$URI . '">' . lng('to_statistics') .
        '</a></div>';

} else {


    $begin_day = strtotime(date("d F y", time()));
    $my_url = parse_url(Vars::$HOME_URL);
    $sql = "
(SELECT COUNT(*) FROM `counter` WHERE `robot` != '') UNION ALL
(SELECT COUNT(DISTINCT `pop`) FROM `counter` WHERE `robot` = '') UNION ALL
(SELECT COUNT(*) FROM `stat_robots` WHERE `date` > '" . $begin_day .
        "') UNION ALL
(SELECT COUNT(DISTINCT `country`) FROM `counter`) UNION ALL
(SELECT COUNT(DISTINCT `operator`, `country`) FROM `counter`) UNION ALL
(SELECT COUNT(DISTINCT `robot`) FROM `counter` WHERE `robot` != '') UNION ALL
(SELECT COUNT(DISTINCT `user`) FROM `counter`) UNION ALL
(SELECT COUNT(DISTINCT `site`) FROM `counter` WHERE `site` NOT LIKE '%" . $my_url['host'] .
        "')
";
    $query = mysql_query($sql);
    $count_stat = array();
    while ($result_array = mysql_fetch_array($query)) {
        $count_stat[] = $result_array[0];
    }
    $hitnorob = statistic::$hity - $count_stat[0];
    //////// Максимум хостов //////
    $maxhost = mysql_query("SELECT `date`, `host` FROM `countersall` ORDER BY `countersall`.`host` DESC LIMIT 0 , 1");
    if (mysql_num_rows($maxhost) > 0) {
        $maxhost = mysql_fetch_array($maxhost);
        $tpl->maxhost = $maxhost;
        /////// Максимум хитов ////////
        $maxhits = mysql_fetch_array(mysql_query("SELECT `date`, `hits` FROM `countersall` ORDER BY `countersall`.`hits` DESC LIMIT 0 , 1"));
        $tpl->maxhits = $maxhits;
        $tpl->max_host_time = date("d M Y", $maxhost['date']);
        $tpl->max_hits_time = date("d M Y", $maxhits['date']);
        $tpl->max_host = true;
    } else {
        $tpl->max_host = false;
    }
    $percent = statistic::$hosty / 100; // 1%
    $tpl->searchpercent = ($percent > 0) ? $count_stat[2] / $percent : '0'; // % поисковиков
    $tpl->my_url = $my_url;
    $tpl->hitnorobot = $hitnorob;
    $tpl->count_stat = $count_stat;
    $tpl->contents = $tpl->includeTpl('index');
}
