<?php

/**
 * @author simba
 * @copyright 2011
 */

defined('_IN_JOHNCMS') or die('Restricted access');

$act = isset($_GET['action']) ? $_GET['action'] : '';
if (Vars::$USER_RIGHTS >= 9){

switch ($act){
    ////////////////////////////////////
    //////// Управление базой IP ///////
    ////////////////////////////////////
    case 'base':
    echo'<div class="phdr">'.lng('database_management_ip').'</div>';
    $count_ip = mysql_result(mysql_query("SELECT COUNT(*) FROM `counter_ip_base`;"), 0);
    if($count_ip > 0){
    $ip_base = mysql_query("SELECT * FROM `counter_ip_base` ". Vars::db_pagination());
    $i = 0;
    while($arr = mysql_fetch_array($ip_base)){
    echo ($i % 2) ? '<div class="list1">' : '<div class="list2">';
        ++$i;
    echo ''.long2ip($arr['start']).' - '.long2ip($arr['stop']).' | '.$arr['operator'].' | '.$arr['country'].'
    <div class="sub"><a href="?act=ip_base&amp;action=base_edit&amp;id='.$arr['id'].'">'.lng('edit').'</a> | <a href="?act=ip_base&amp;action=base_delete&amp;id='.$arr['id'].'">'.lng('delete').'</a></div></div>';
    }

    echo '<div class="phdr">'.lng('total_ip').': ' . $count_ip . '</div>';
    if ($count_ip > Vars::$USER_SET['page_size']){
        echo '<div class="topmenu">';
    	echo Functions::displayPagination(Vars::$URI.'?act=ip_base&amp;action=base&amp;', Vars::$START, $count_ip, Vars::$USER_SET['page_size']) . '</div>';
    	echo '<p><form action="'.Vars::$URI.'" method="get"><input type="hidden" name="act" value="ip_base"/><input type="hidden" name="action" value="base"/><input type="text" name="page" size="2"/><input type="submit" value="'.lng('to_page').' &gt;&gt;"/></form></p>';}

    }else{ echo'<div class="rmenu">'.lng('no_data').'!</div>'; }
    echo'<div class="menu"><a href="'.Vars::$URI.'?act=ip_base&amp;action=base_add">'.lng('add_ip').'</a></div>';
    break;

    ////////////////////////////////////
    //////// Изменение IP в базе ///////
    ////////////////////////////////////
    case 'base_edit':
    echo'<div class="phdr">'.lng('edit_ip').'</div>';
    $id = isset($_GET['id']) ? intval($_GET['id']) : 0;

    if (isset($_POST['submit']))
    {
    mysql_query("UPDATE `counter_ip_base` SET
    `start` = '".ip2long($_POST['start'])."',
    `stop` = '".ip2long($_POST['stop'])."',
    `operator` = '".mysql_real_escape_string(htmlspecialchars((string)$_POST['operator']))."',
    `country` = '".mysql_real_escape_string(htmlspecialchars((string)$_POST['country']))."'
    WHERE `id` = '" . $id . "' LIMIT 1;");
    echo '<div class="gmenu">'.lng('saved').'</div>';
    }

    $ip_base = mysql_query("SELECT * FROM `counter_ip_base` WHERE `id` = '".$id."'");
    if (mysql_num_rows($ip_base) > 0) {
    $arr = mysql_fetch_array($ip_base);
    echo '<form action="?act=ip_base&amp;action=base_edit&amp;id='.$id.'" method="post">
    <div class="menu">'.lng('begin_range').':<br/>
    <input type="text" name="start" value="'.long2ip($arr['start']).'"/></div><div class="menu">
    '.lng('end_range').':<br/>
    <input type="text" name="stop" value="'.long2ip($arr['stop']).'"/></div><div class="menu">
    '.lng('operator').':<br/>
    <input type="text" name="operator" value="'.$arr['operator'].'"/></div><div class="menu">
    '.lng('country').':<br/>
    <input type="text" name="country" value="'.$arr['country'].'"/></div><div class="menu">
    <span class="red">'.lng('warn_edit').'</span><br/>
    <input type="submit" name="submit" value="'.lng('apply').'"/></div>
    </form>';
    }else{
        echo'<div class="rmenu">'.lng('no_data').'!</div>';
    }
    echo'<div class="menu"><a href="?act=ip_base&amp;action=base">'.lng('list_ip').'</a></div>';
    break;

    ////////////////////////////////////
    //////// Удаление IP из базы ///////
    ////////////////////////////////////
    case 'base_delete':
    echo'<div class="phdr">'.lng('del_ip').'</div>';
    $id = isset($_GET['id']) ? intval($_GET['id']) : 0;
    $ip_base = mysql_query("SELECT * FROM `counter_ip_base` WHERE `id` = '".$id."'");
    if (mysql_num_rows($ip_base) > 0) {
    mysql_query("DELETE FROM `counter_ip_base` WHERE `id` = '".$id."' LIMIT 1");
    echo'<div class="gmenu">'.lng('deleted_success').'</div>';
    }else{
        echo'<div class="rmenu">'.lng('no_data').'!</div>';
    }
    echo'<div class="menu"><a href="?act=ip_base&amp;action=base">'.lng('list_ip').'</a></div>';
    break;

    ////////////////////////////////////
    //////// Изменение IP в базе ///////
    ////////////////////////////////////
    case 'base_add':
    echo'<div class="phdr">'.lng('add_ip').'</div>';
    $id = isset($_GET['id']) ? intval($_GET['id']) : 0;

    if (isset($_POST['submit']))
    {
    $operator = ($_POST['operator']) ? mysql_real_escape_string(htmlspecialchars((string)$_POST['operator'])) : mysql_real_escape_string(htmlspecialchars((string)$_POST['s_operator']));
    $country = ($_POST['country']) ? mysql_real_escape_string(htmlspecialchars((string)$_POST['country'])) : mysql_real_escape_string(htmlspecialchars((string)$_POST['s_country']));
    $error = '';
    $ip1 = ip2long($_POST['start']);
    $ip2 = ip2long($_POST['stop']);
    if (!$ip1)
    $error = '<div>'.lng('error_add1').'</div>';
    if (!$ip2)
    $error .= '<div>'.lng('error_add2').'</div>';
    if (!$error && $ip1 > $ip2)
    $error = '<div>'.lng('error_add3').'</div>';
    if(empty($operator))
    $error .= '<div>'.lng('error_add4').' "'.lng('operator').'"</div>';
    if(empty($country))
    $error .= '<div>'.lng('error_add4').' "'.lng('country').'"</div>';

    if(!$error){
    mysql_query("INSERT INTO `counter_ip_base` SET
    `start` = '".$ip1."',
    `stop` = '".$ip2."',
    `operator` = '".$operator."',
    `country` = '".$country."';");
    echo '<div class="gmenu">'.lng('added').'</div>';
    }else{
        echo Functions::displayError($error, '<a href="?act=ip_base&amp;action=base_add">'.lng('back').'</a>');
    }

    }else{
    echo '<form action="?act=ip_base&amp;action=base_add" method="post">
    <div class="menu">'.lng('begin_range').':<br/>
    <input type="text" name="start"/><br/>
    <small>'.lng('for_example').': 192.168.192.168</small></div><div class="menu">
    '.lng('end_range').':<br/>
    <input type="text" name="stop"/><br/>
    <small>'.lng('for_example').': 192.189.122.18</small></div><div class="menu">
    '.lng('operator').':<br/>
    <input type="text" name="operator"/><br/>
    <small>'.lng('for_example').': Beeline</small></div>';
    
    echo '<div class="menu">'.lng('select_operator').':<br/>
    <select name="s_operator" class="textbox">';
    $impcat = mysql_query("SELECT * FROM `counter_ip_base` GROUP BY `operator`;");
    echo '<option value="">'.lng('not_select').'</option>';
    while ($arr = mysql_fetch_array($impcat)) {
        echo '<option value="' . $arr['operator'] . '">' . $arr['operator'] . '</option>';
            }
            echo '</select><br/>
            <small>'.lng('for_manual').' "'.lng('not_select').'"</small>
            </div>';
    echo '<div class="menu">
    '.lng('country').':<br/>
    <input type="text" name="country"/><br/>
    <small>'.lng('for_example').': Russia</small></div>';
    
    echo '<div class="menu">'.lng('select_country').':<br/>
    <select name="s_country" class="textbox">';
    $impcat = mysql_query("SELECT * FROM `counter_ip_base` GROUP BY `country`;");
    echo '<option value="">'.lng('not_select1').'</option>';
    while ($arr = mysql_fetch_array($impcat)) {
        echo '<option value="' . $arr['country'] . '">' . $arr['country'] . '</option>';
            }
            echo '</select><br/>
            <small>'.lng('for_manual').' "'.lng('not_select1').'"</small></div>';
    
    echo '<div class="menu">
    <span class="red">'.lng('warn_edit').'</span><br/>
    <input type="submit" name="submit" value="'.lng('add').'"/></div>
    </form>';
    }
    echo'<div class="menu"><a href="?act=ip_base&amp;action=base">'.lng('list_ip').'</a></div>';    
    break;
}



}else{
    echo '<div class="rmenu">'.lng('access_denied').'</div>';
}


?>