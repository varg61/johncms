<?php

/*
////////////////////////////////////////////////////////////////////////////////
// JohnCMS                Mobile Content Management System                    //
// Project site:          http://johncms.com                                  //
// Support site:          http://gazenwagen.com                               //
////////////////////////////////////////////////////////////////////////////////
// Lead Developer:        Oleg Kasyanov   (AlkatraZ)  alkatraz@gazenwagen.com //
// Development Team:      Eugene Ryabinin (john77)    john77@gazenwagen.com   //
//                        Dmitry Liseenko (FlySelf)   flyself@johncms.com     //
////////////////////////////////////////////////////////////////////////////////
*/

defined('_IN_JOHNADM') or die('Error: restricted access');

echo '<div class="phdr"><a href="index.php"><b>Админ панель</b></a> | Список пользователей</div>';
$sort = isset($_GET['sort']) ? trim($_GET['sort']) : '';
echo '<div class="topmenu"><span class="gray">Сортировка:</span> ';
switch ($sort) {
    case 'nick':
        $sort = 'nick';
        echo '<a href="index.php?act=usr_list&amp;sort=id">ID</a> | Ник | <a href="index.php?act=usr_list&amp;sort=ip">IP</a></div>';
        $order = '`name` ASC';
        break;

    case 'ip':
        $sort = 'ip';
        echo '<a href="index.php?act=usr_list&amp;sort=id">ID</a> | <a href="index.php?act=usr_list&amp;sort=nick">Ник</a> | IP</div>';
        $order = '`ip` ASC';
        break;
        default :
    $sort = 'id';
        echo 'ID | <a href="index.php?act=usr_list&amp;sort=nick">Ник</a> | <a href="index.php?act=usr_list&amp;sort=ip">IP</a></div>';
        $order = '`id` ASC';
}
$req = mysql_query("SELECT COUNT(*) FROM `users`");
$total = mysql_result($req, 0);
$req = mysql_query("SELECT * FROM `users` WHERE `preg` = 1 ORDER BY $order LIMIT " . $start . "," . $kmess);
while ($res = mysql_fetch_array($req)) {
    $link = '';
    if ($rights >= 7)
        $link .= '<a href="../str/my_data.php?id=' . $res['id'] . '">Изменть</a> | <a href="index.php?act=usr_del&amp;id=' . $res['id'] . '">Удалить</a> | ';
    $link .= '<a href="../str/users_ban.php?act=ban&amp;id=' . $res['id'] . '">Банить</a>';
    echo $i % 2 ? '<div class="list2">' : '<div class="list1">';
    echo show_user($res, array('header' => ('<b>ID:' . $res['id'] . '</b>'), 'body' => $link));
    echo '</div>';
    ++$i;
}
echo '<div class="phdr">Всего: ' . $total . '</div>';
if ($total > $kmess) {
    echo '<p>' . pagenav('index.php?act=usr_list&amp;sort=' . $sort . '&amp;', $start, $total, $kmess) . '</p>';
    echo '<p><form action="index.php?act=usr_list&amp;sort=' . $sort . '" method="post"><input type="text" name="page" size="2"/><input type="submit" value="К странице &gt;&gt;"/></form></p>';
}
echo '<p><a href="index.php?act=usr_search">Поиск пользователя</a><br /><a href="index.php">Админ панель</a></p>';

?>