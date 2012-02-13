<?php

/**
 * @package     JohnCMS
 * @link        http://johncms.com
 * @copyright   Copyright (C) 2008-2012 JohnCMS Community
 * @license     LICENSE.txt (see attached file)
 * @version     VERSION.txt (see attached file)
 * @author      http://johncms.com/about
 */

class System extends Vars
{
    function __construct()
    {
        // Определение мобильного браузера
        parent::$IS_MOBILE = $this->_mobileDetect();

        // Получаем системные настройки
        $this->_sysSettings();

        // Автоочистка системы
        $this->_autoClean();

        // Авторизация пользователей
        $this->_authorizeUser();

        // Определяем и загружаем язык
        $this->_lngDetect();
        parent::$LNG = parent::loadLanguage();
        if (parent::$LNG_ISO != 'ru' && parent::$LNG_ISO != 'uk') parent::$USER_SET['translit'] = 0;

        // Принимаем суперглобальные переменные
        parent::$ID = isset($_REQUEST['id']) ? abs(intval($_REQUEST['id'])) : 0;
        parent::$ACT = isset($_REQUEST['act']) ? substr(trim($_REQUEST['act']), 0, 15) : '';
        parent::$MOD = isset($_REQUEST['mod']) ? substr(trim($_REQUEST['mod']), 0, 15) : '';
        parent::$USER = isset($_REQUEST['user']) ? abs(intval($_REQUEST['user'])) : 0;
        if (isset($_REQUEST['page']) && $_REQUEST['page'] > 0) {
            parent::$PAGE = intval($_REQUEST['page']);
            parent::$START = parent::$PAGE * parent::$USER_SET['page_size'] - parent::$USER_SET['page_size'];
        } elseif (isset($_REQUEST['start']) && $_REQUEST['start'] > 0) {
            parent::$START = intval($_REQUEST['start']);
        }

        $this->_router();
    }

    /*
    -----------------------------------------------------------------
    Роутер
    -----------------------------------------------------------------
    */
    private function _router()
    {
        $route = isset($_GET['route']) ? substr(trim($_GET['route']), 0, 50) : '';
        $place = '';
        if (!empty($route)) {
            $path = array();
            $array = explode('/', $route);
            foreach ($array as $val) {
                if (preg_match('/.php$/i', $val)) {
                    $file = $val;
                } else {
                    $path[] = $val;
                }
            }

            if (isset($path[0])) {
                $req = mysql_query("SELECT * FROM `cms_modules` WHERE `module` = '" . mysql_real_escape_string($path[0]) . "'");
                if (mysql_num_rows($req)) {
                    $include = MODPATH . implode(DIRECTORY_SEPARATOR, $path) . DIRECTORY_SEPARATOR . (isset($file) ? $file : 'index.php');
                    if (is_file($include)) {
                        $res = mysql_fetch_assoc($req);
                        parent::$MODULE = $res['module'];
                        parent::$MODULE_INCLUDE = $include;
                        parent::$MODULE_URI = parent::$HOME_URL . '/' . $path[0];
                        parent::$URI = parent::$HOME_URL . '/' . implode('/', $path) . (isset($file) && $file != 'index.php' ? '/' . $file : '');
                        $place = $route;
                    } else {
                        // Ошибка 404
                        parent::$MODULE_INCLUDE = MODPATH . '404' . DIRECTORY_SEPARATOR . 'index.php';
                        parent::$MODULE = '404';
                    }
                } else {
                    // Ошибка 404
                    parent::$MODULE_INCLUDE = MODPATH . '404' . DIRECTORY_SEPARATOR . 'index.php';
                    parent::$MODULE = '404';
                }
            } else {
                // Ошибка 404
                parent::$MODULE_INCLUDE = MODPATH . '404' . DIRECTORY_SEPARATOR . 'index.php';
                parent::$MODULE = '404';
            }
        } else {
            // Главная страница сайта
            parent::$MODULE_INCLUDE = MODPATH . 'homepage' . DIRECTORY_SEPARATOR . 'index.php';
            parent::$MODULE = 'homepage';
            parent::$MODULE_URI = parent::$HOME_URL;
            parent::$URI = parent::$HOME_URL;
        }

        // Фиксируем местоположение на сайте
        if (!empty($place)) {
            $param = array();
            if (!empty(parent::$ACT)) {
                $param[] = 'act=' . parent::$ACT;
            }
            if (!empty(parent::$MOD)) {
                $param[] = 'mod=' . parent::$MOD;
            }
            if (parent::$ID) {
                $param[] = 'id=' . parent::$ID;
            }
            parent::$PLACE = $place . (!empty($param) ? '?' . implode('&', $param) : '');
        }
    }

    /*
    -----------------------------------------------------------------
    Получаем системные настройки
    -----------------------------------------------------------------
    */
    private
    function _sysSettings()
    {
        $set = array();
        $req = mysql_query("SELECT * FROM `cms_settings`");
        while (($res = mysql_fetch_row($req)) !== false) $set[$res[0]] = $res[1];
        if (isset($set['lng']) && !empty($set['lng'])) parent::$LNG_ISO = $set['lng'];
        if (isset($set['lng_list'])) parent::$LNG_LIST = unserialize($set['lng_list']);
        parent::$SYSTEM_SET = $set;
        parent::$HOME_URL = 'http://' . trim($_SERVER['SERVER_NAME'], '/\\') . '/' . trim(ltrim(str_replace('\\', '/', ROOTPATH), $_SERVER['DOCUMENT_ROOT']), '/\\');
    }

    /*
    -----------------------------------------------------------------
    Определяем язык
    -----------------------------------------------------------------
    */
    private
    function _lngDetect()
    {
        $setlng = isset($_POST['setlng']) ? substr(trim($_POST['setlng']), 0, 2) : false;
        if ($setlng && array_key_exists($setlng, parent::$LNG_LIST)) {
            $_SESSION['lng'] = $setlng;
        }
        if (isset($_SESSION['lng']) && array_key_exists($_SESSION['lng'], parent::$LNG_LIST)) {
            parent::$LNG_ISO = $_SESSION['lng'];
        } elseif (parent::$USER_ID && isset(parent::$USER_SET['lng']) && array_key_exists(parent::$USER_SET['lng'], parent::$LNG_LIST)) {
            parent::$LNG_ISO = parent::$USER_SET['lng'];
        } elseif (isset($_SERVER['HTTP_ACCEPT_LANGUAGE'])) {
            $accept = explode(',', strtolower(trim($_SERVER['HTTP_ACCEPT_LANGUAGE'])));
            foreach ($accept as $var) {
                $lng = substr($var, 0, 2);
                if (array_key_exists($lng, parent::$LNG_LIST)) {
                    parent::$LNG_ISO = $lng;
                    break;
                }
            }
        }
    }

    /*
    -----------------------------------------------------------------
    Авторизация пользователя и получение его данных из базы
    -----------------------------------------------------------------
    */
    private
    function _authorizeUser()
    {
        $id = false;
        $token = false;
        $cookie = false;

        if (isset($_SESSION['uid']) && isset($_SESSION['token'])) {
            // Авторизация по сессии
            $id = intval($_SESSION['uid']);
            $token = $_SESSION['token'];
        } elseif (isset($_COOKIE['uid']) && isset($_COOKIE['token'])) {
            // Авторизация по COOKIE
            $id = intval($_COOKIE['uid']);
            $token = trim($_COOKIE['token']);
            if ($id < 1 || $id != $_COOKIE['uid'] || strlen($_COOKIE['token']) != 32) {
                $id = false;
                $token = false;
                Login::userUnset();
            } else {
                $cookie = true;
            }
        }

        if ($id && $token) {
            $req = mysql_query("SELECT * FROM `users` WHERE `id` = " . $id);
            if (mysql_num_rows($req)) {
                $res = mysql_fetch_assoc($req);

                // Допуск на авторизацию с COOKIE
                if ($cookie && $res['login_try'] > 2 && ($res['ip'] != parent::$IP || $res['ip_via_proxy'] != parent::$IP_VIA_PROXY || $res['useragent'] != parent::$USER_AGENT)) {
                    $permit = false;
                } else {
                    $permit = true;
                }

                // Если авторизация прошла успешно
                if ($permit && $token === $res['token']) {
                    parent::$USER_ID = $id;
                    parent::$USER_NICKNAME = $res['nickname'];
                    parent::$USER_RIGHTS = $res['rights'];
                    parent::$USER_DATA = $res;
                    $_SESSION['uid'] = $id;
                    $_SESSION['token'] = $token;

                    // Получаем пользовательские настройки
                    if (isset($_SESSION['user_settings'])) {
                        if ($_SESSION['user_settings'] != '#') {
                            parent::$USER_SET = unserialize($_SESSION['settings']);
                        }
                    } else {
                        if (($user_set = parent::getUserData('set_user')) !== false) {
                            parent::$USER_SET = $user_set;
                            $_SESSION['user_settings'] = serialize(parent::$USER_SET);
                        } else {
                            $_SESSION['user_settings'] = '#';
                        }
                    }

                    // Фиксация времени последнего визита
                    $sql_update[] = "`last_visit` = " . time();

                    // Обработка User Agent
                    if ($res['user_agent'] != parent::$USER_AGENT) {
                        $sql_update[] = "`user_agent` = '" . parent::$USER_AGENT . "'";
                    }

                    // Обработка IP адресов и фиксация истории
                    if ($res['ip'] != parent::$IP || $res['ip_via_proxy'] != parent::$IP_VIA_PROXY) {
                        $sql_update[] = "`ip` = '" . parent::$IP . "'";
                        $sql_update[] = "`ip_via_proxy` = '" . parent::$IP_VIA_PROXY . "'";
                        $this->_userIpHistory();
                    }

                    //TODO: Разобраться с доп. данными, типа кармы
                    // Фиксация данных
                    mysql_query("UPDATE `users` SET " . implode(', ', $sql_update) . " WHERE `id` = " . parent::$USER_ID)
                        or exit(mysql_error());

                    // Проверка на бан
                    if ($res['ban']) $this->_checkUserBan();
                } else {
                    // Если авторизация не прошла
                    mysql_query("UPDATE `users` SET `login_try` = '" . ++$res['login_try'] . "' WHERE `id` = " . $res['user_id']);
                    Login::userUnset();
                }
            } else {
                // Если пользователь не существует
                Login::userUnset();
            }
        } else {
            // Для неавторизованных
        }
    }

    /*
    -----------------------------------------------------------------
    Проверка пользователя на Бан
    -----------------------------------------------------------------
    */
    private
    function _checkUserBan()
    {
        //TODO: Переделать!
        $req = mysql_query("SELECT * FROM `cms_ban_users` WHERE `user_id` = '" . parent::$USER_ID . "' AND `ban_time` > '" . time() . "'");
        if (mysql_num_rows($req)) {
            parent::$USER_RIGHTS = 0;
            while (($res = mysql_fetch_row($req)) !== false) parent::$USER_BAN[$res[4]] = 1;
        }
    }

    /*
    -----------------------------------------------------------------
    Фиксация истории адресов IP
    -----------------------------------------------------------------
    */
    private
    function _userIpHistory()
    {
        $req = mysql_query("SELECT * FROM `cms_user_ip` WHERE `user_id` = " . parent::$USER_ID . " AND `ip` = '" . parent::$IP . "' AND `ip_via_proxy` = '" . parent::$IP_VIA_PROXY . "' LIMIT 1");
        if (mysql_num_rows($req)) {
            // Если текущие адреса найдены, обновляем метку времени
            $res = mysql_fetch_assoc($req);
            mysql_query("UPDATE `cms_user_ip` SET " . ($res['useragent'] != parent::$USER_AGENT ? "`useragent` = '" . mysql_real_escape_string(parent::$USER_AGENT) . "'," : '') . "
                `timestamp` = " . time() . "
                WHERE `id` = " . $res['id']
            );
        } else {
            // Если адреса не найдены, вставляем новую запись в историю
            mysql_query("INSERT INTO `cms_user_ip` SET
                `user_id` = " . parent::$USER_ID . ",
                `ip` = " . parent::$IP . ",
                `ip_via_proxy` = " . parent::$IP_VIA_PROXY . ",
                `useragent` = '" . mysql_real_escape_string(parent::$USER_AGENT) . "',
                `timestamp` = " . time()
            );
        }
    }

    /*
    -----------------------------------------------------------------
    Автоочистка системы
    -----------------------------------------------------------------
    */
    private
    function _autoClean()
    {
        if (parent::$SYSTEM_SET['clean_time'] < time() - 86400) {
            mysql_query("DELETE FROM `cms_sessions` WHERE `lastdate` < '" . (time() - 86400) . "'");
            mysql_query("DELETE FROM `cms_users_iphistory` WHERE `time` < '" . (time() - 2592000) . "'");
            mysql_query("UPDATE `cms_settings` SET  `val` = '" . time() . "' WHERE `key` = 'clean_time' LIMIT 1");
            mysql_query("OPTIMIZE TABLE `cms_sessions` , `cms_users_iphistory`, `cms_users_settings`");
        }
    }

    /*
    -----------------------------------------------------------------
    Определение мобильного браузера
    -----------------------------------------------------------------
    */
    private
    function _mobileDetect()
    {
        if (isset($_SESSION['is_mobile'])) {
            return $_SESSION['is_mobile'] == 1 ? true : false;
        }
        $user_agent = strtolower($_SERVER['HTTP_USER_AGENT']);
        $accept = strtolower($_SERVER['HTTP_ACCEPT']);
        if ((strpos($accept, 'text/vnd.wap.wml') !== false) || (strpos($accept, 'application/vnd.wap.xhtml+xml') !== false)) {
            $_SESSION['is_mobile'] = 1;
            return true;
        }
        if (isset($_SERVER['HTTP_X_WAP_PROFILE']) || isset($_SERVER['HTTP_PROFILE'])) {
            $_SESSION['is_mobile'] = 1;
            return true;
        }
        if (preg_match('/android|avantgo|blackberry|blazer|compal|elaine|fennec|hiptop|iemobile|ip(hone|od)|iris|kindle|lge |maemo|midp|mmp|opera m(ob|in)i|palm( os)?|phone|p(ixi|re)\/|plucker|pocket|psp|symbian|treo|up\.(browser|link)|vodafone|wap|windows (ce|phone)|xda|xiino/i', $user_agent)
            || preg_match('/1207|6310|6590|3gso|4thp|50[1-6]i|770s|802s|a wa|abac|ac(er|oo|s\-)|ai(ko|rn)|al(av|ca|co)|amoi|an(ex|ny|yw)|aptu|ar(ch|go)|as(te|us)|attw|au(di|\-m|r |s )|avan|be(ck|ll|nq)|bi(lb|rd)|bl(ac|az)|br(e|v)w|bumb|bw\-(n|u)|c55\/|capi|ccwa|cdm\-|cell|chtm|cldc|cmd\-|co(mp|nd)|craw|da(it|ll|ng)|dbte|dc\-s|devi|dica|dmob|do(c|p)o|ds(12|\-d)|el(49|ai)|em(l2|ul)|er(ic|k0)|esl8|ez([4-7]0|os|wa|ze)|fetc|fly(\-|_)|g1 u|g560|gene|gf\-5|g\-mo|go(\.w|od)|gr(ad|un)|haie|hcit|hd\-(m|p|t)|hei\-|hi(pt|ta)|hp( i|ip)|hs\-c|ht(c(\-| |_|a|g|p|s|t)|tp)|hu(aw|tc)|i\-(20|go|ma)|i230|iac( |\-|\/)|ibro|idea|ig01|ikom|im1k|inno|ipaq|iris|ja(t|v)a|jbro|jemu|jigs|kddi|keji|kgt( |\/)|klon|kpt |kwc\-|kyo(c|k)|le(no|xi)|lg( g|\/(k|l|u)|50|54|e\-|e\/|\-[a-w])|libw|lynx|m1\-w|m3ga|m50\/|ma(te|ui|xo)|mc(01|21|ca)|m\-cr|me(di|rc|ri)|mi(o8|oa|ts)|mmef|mo(01|02|bi|de|do|t(\-| |o|v)|zz)|mt(50|p1|v )|mwbp|mywa|n10[0-2]|n20[2-3]|n30(0|2)|n50(0|2|5)|n7(0(0|1)|10)|ne((c|m)\-|on|tf|wf|wg|wt)|nok(6|i)|nzph|o2im|op(ti|wv)|oran|owg1|p800|pan(a|d|t)|pdxg|pg(13|\-([1-8]|c))|phil|pire|pl(ay|uc)|pn\-2|po(ck|rt|se)|prox|psio|pt\-g|qa\-a|qc(07|12|21|32|60|\-[2-7]|i\-)|qtek|r380|r600|raks|rim9|ro(ve|zo)|s55\/|sa(ge|ma|mm|ms|ny|va)|sc(01|h\-|oo|p\-)|sdk\/|se(c(\-|0|1)|47|mc|nd|ri)|sgh\-|shar|sie(\-|m)|sk\-0|sl(45|id)|sm(al|ar|b3|it|t5)|so(ft|ny)|sp(01|h\-|v\-|v )|sy(01|mb)|t2(18|50)|t6(00|10|18)|ta(gt|lk)|tcl\-|tdg\-|tel(i|m)|tim\-|t\-mo|to(pl|sh)|ts(70|m\-|m3|m5)|tx\-9|up(\.b|g1|si)|utst|v400|v750|veri|vi(rg|te)|vk(40|5[0-3]|\-v)|vm40|voda|vulc|vx(52|53|60|61|70|80|81|83|85|98)|w3c(\-| )|webc|whit|wi(g |nc|nw)|wmlb|wonu|x700|xda(\-|2|g)|yas\-|your|zeto|zte\-/i', substr($user_agent, 0, 4))
        ) {
            $_SESSION['is_mobile'] = 1;
            return true;
        }
        $_SESSION['is_mobile'] = 2;
        return false;
    }
}