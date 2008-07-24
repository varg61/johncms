<?php
/*
////////////////////////////////////////////////////////////////////////////////
// JohnCMS                             Content Management System              //
// ����������� ���� ���� �������:      http://johncms.com                     //
// �������������� ���� ���������:      http://gazenwagen.com                  //
////////////////////////////////////////////////////////////////////////////////
// JohnCMS core team:                                                         //
// ������� ������� aka john77          john77@gazenwagen.com                  //
// ���� �������� aka AlkatraZ          alkatraz@gazenwagen.com                //
//                                                                            //
// ���������� � ������� �������� � ����������� ����� version.txt              //
////////////////////////////////////////////////////////////////////////////////
*/

defined('_IN_JOHNCMS') or die('Restricted access');

class ipinit
{
    var $ip; // IP ����� � LONG �������
    var $flood_chk = '0'; // ��������� - ���������� ������� IP ���������
    var $flood_interval = '60'; // �������� �������
    var $flood_limit = '20'; // ����� ����������� �������� �� ��������
    var $flood_file = 'flood.dat'; // ������� ���� �������
    var $requests; // ����� �������� � IP ������ �� ������ �������

    function ipinit()
    {
        // ��������� ��������� IP ������
        if (isset($_SERVER['HTTP_X_FORWARDED_FOR']))
        {
            $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
        } elseif (isset($_SERVER['HTTP_VIA']))
        {
            $ip = $_SERVER['HTTP_VIA'];
        } elseif (isset($_SERVER['REMOTE_ADDR']))
        {
            $ip = $_SERVER['REMOTE_ADDR'];
        } else
        {
            die('Unknown IP');
        }
        $this->ip = ip2long($ip);

        // �������� ������ IP �� HTTP ����
        if ($this->flood_chk)
        {
            $this->reqcount();
            if ($this->requests > $this->flood_limit)
                die('Flood!!!');
        }
    }

    function reqcount()
    {
        global $rootpath;
		$tmp = array();
        $requests = 1;
        $in = fopen($rootpath . $this->flood_file, "r+");
        flock($in, LOCK_EX) or die("Cannot flock ANTIFLOOD file.");
        $now = time();
        while ($block = fread($in, 8))
        {
            $arr = unpack("Lip/Ltime", $block);
            if (($now - $arr['time']) > $this->flood_interval)
            {
                continue;
            }
            if ($arr['ip'] == $this->ip)
            {
                $requests++;
            }
            $tmp[] = $arr;
        }
        fseek($in, 0);
        ftruncate($in, 0);
        for ($i = 0; $i < count($tmp); $i++)
        {
            fwrite($in, pack('LL', $tmp[$i]['ip'], $tmp[$i]['time']));
        }
        fwrite($in, pack('LL', $this->ip, $now));
        fclose($in);
        $this->requests = $requests;
    }
}

?>