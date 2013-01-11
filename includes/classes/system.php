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
        static::$IS_MOBILE = $this->_mobileDetect();

        // Получаем системные настройки
        $this->_sysSettings();

        // Автоочистка системы
        $this->_autoClean();

        // Авторизация пользователей
        $this->_authorizeUser();

        // Определяем и загружаем язык
        $this->_lngDetect();
        if (static::$LNG_ISO != 'ru' && static::$LNG_ISO != 'uk') static::$USER_SET['translit'] = 0;

        // Принимаем суперглобальные переменные
        static::$ID = isset($_REQUEST['id']) ? abs(intval($_REQUEST['id'])) : 0;
        static::$ACT = isset($_REQUEST['act']) ? substr(trim($_REQUEST['act']), 0, 30) : '';
        static::$MOD = isset($_REQUEST['mod']) ? substr(trim($_REQUEST['mod']), 0, 30) : '';
        static::$USER = isset($_REQUEST['user']) ? abs(intval($_REQUEST['user'])) : 0;
        if (isset($_REQUEST['page']) && $_REQUEST['page'] > 0) {
            static::$PAGE = intval($_REQUEST['page']);
            static::$START = static::$PAGE * static::$USER_SET['page_size'] - static::$USER_SET['page_size'];
        } elseif (isset($_REQUEST['start']) && $_REQUEST['start'] > 0) {
            static::$START = intval($_REQUEST['start']);
        }
    }

    /**
     * Получаем системные настройки
     */
    private function _sysSettings()
    {
        $STH = DB::PDO()->query('SELECT * FROM `cms_settings`');
        if ($STH->rowCount()) {
            while ($result = $STH->fetch()) {
                static::$SYSTEM_SET[$result['key']] = $result['val'];
            }
        }

        if (isset(static::$SYSTEM_SET['lng']) && !empty(static::$SYSTEM_SET['lng'])) {
            static::$LNG_ISO = static::$SYSTEM_SET['lng'];
        }
        if (isset(static::$SYSTEM_SET['lng_list'])) {
            static::$LNG_LIST = unserialize(static::$SYSTEM_SET['lng_list']);
        }
        if (isset(static::$SYSTEM_SET['users'])) {
            static::$USER_SYS = unserialize(static::$SYSTEM_SET['users']);
        }
        static::$ACL = isset(static::$SYSTEM_SET['acl']) ? unserialize(static::$SYSTEM_SET['acl']) : array();

        $subpath = trim(ltrim(str_replace('\\', '/', ROOTPATH), $_SERVER['DOCUMENT_ROOT']), '/\\');
        static::$HOME_URL = 'http://' . trim($_SERVER['SERVER_NAME'], '/\\') . (!empty($subpath) ? '/' . $subpath . '/' : '');
    }

    /**
     * Определяем язык
     */
    private function _lngDetect()
    {
        if (isset($_SESSION['lng'])) {
            static::$LNG_ISO = $_SESSION['lng'];
        } elseif (static::$USER_ID && isset(static::$USER_SET['lng']) && array_key_exists(static::$USER_SET['lng'], static::$LNG_LIST)) {
            static::$LNG_ISO = static::$USER_SET['lng'];
        } elseif (isset($_SERVER['HTTP_ACCEPT_LANGUAGE'])) {
            $accept = explode(',', strtolower(trim($_SERVER['HTTP_ACCEPT_LANGUAGE'])));
            foreach ($accept as $var) {
                $lng = substr($var, 0, 2);
                if (in_array($lng, Languages::getInstance()->getLngList())) {
                    static::$LNG_ISO = $lng;
                    break;
                }
            }
        }
    }

    /**
     * Авторизация пользователя и получение его данных из базы
     */
    private function _authorizeUser()
    {
        $id = FALSE;
        $token = FALSE;
        $cookie = FALSE;

        if (isset($_SESSION['uid']) && isset($_SESSION['token'])) {
            // Авторизация по сессии
            $id = intval($_SESSION['uid']);
            $token = $_SESSION['token'];
        } elseif (isset($_COOKIE['uid'])
            && is_numeric($_COOKIE['uid'])
            && $_COOKIE['uid'] > 0
            && isset($_COOKIE['token'])
            && strlen($_COOKIE['token']) == 32
        ) {
            // Авторизация по COOKIE
            $id = intval($_COOKIE['uid']);
            $token = trim($_COOKIE['token']);
            $cookie = TRUE;
        }

        if ($id && $token) {
            $STH = DB::PDO()->prepare('
                SELECT * FROM `users`
                WHERE `id` = :uid
            ');

            $STH->bindParam(':uid', $id, PDO::PARAM_INT);
            $STH->execute();

            if ($STH->rowCount()) {
                $result = $STH->fetch();

                // Допуск на авторизацию с COOKIE
                if ($cookie && $result['login_try'] > 2 && ($result['ip'] != static::$IP || $result['ip_via_proxy'] != static::$IP_VIA_PROXY || $result['useragent'] != static::$USER_AGENT)) {
                    $permit = FALSE;
                } else {
                    $permit = TRUE;
                }

                // Если авторизация прошла успешно
                if ($permit && $token === $result['token']) {
                    static::$USER_ID = $id;
                    static::$USER_NICKNAME = $result['nickname'];
                    static::$USER_RIGHTS = $result['rights'];
                    static::$USER_DATA = $result;
                    $_SESSION['uid'] = $id;
                    $_SESSION['token'] = $token;

                    // Получаем пользовательские настройки
                    if (isset($_SESSION['user_set'])) {
                        if ($_SESSION['user_set'] != '#') {
                            static::$USER_SET = unserialize($_SESSION['user_set']);
                        }
                    } else {
                        if (($user_set = static::getUserData('user_set')) !== FALSE) {
                            static::$USER_SET = $user_set;
                            $_SESSION['user_set'] = serialize(static::$USER_SET);
                        } else {
                            $_SESSION['user_set'] = '#';
                        }
                    }

                    $this->_userIpHistory($result['ip'], $result['ip_via_proxy']);

                    // Фиксация данных
                    $STHF = DB::PDO()->prepare('
                        UPDATE `users` SET
                        `last_visit`   = :time,
                        `ip`           = :ip,
                        `ip_via_proxy` = :ipvia,
                        `user_agent`   = :ua
                        WHERE `id`     = :uid
                    ');

                    $STHF->bindValue(':time', time(), PDO::PARAM_INT);
                    $STHF->bindValue(':ip', static::$IP, PDO::PARAM_INT);
                    $STHF->bindValue(':ipvia', static::$IP_VIA_PROXY, PDO::PARAM_INT);
                    $STHF->bindValue(':ua', static::$USER_AGENT, PDO::PARAM_STR);
                    $STHF->bindParam(':uid', $id, PDO::PARAM_INT);
                    $STHF->execute();

                    // Проверка на бан
                    if ($result['ban']) {
                        $this->_checkUserBan();
                    }
                } else {
                    // Если авторизация не прошла
                    $STHF = DB::PDO()->prepare('
                        UPDATE `users` SET
                        `login_try` = :try
                        WHERE `id`  = :uid
                    ');

                    $STHF->bindValue(':try', ++$result['login_try'], PDO::PARAM_INT);
                    $STHF->bindValue(':uid', $result['id'], PDO::PARAM_INT);
                    $STHF->execute();

                    static::userUnset();
                }
            } else {
                // Если пользователь не существует
                static::userUnset();
            }
        } else {
            // Для неавторизованных
        }
    }

    /**
     * Проверка пользователя на Бан
     */
    private function _checkUserBan()
    {
        $STH = DB::PDO()->prepare('
            SELECT * FROM `cms_ban_users`
            WHERE `user_id` = :uid
            AND `ban_time`  > :time
        ');

        $STH->bindValue(':uid', static::$USER_ID, PDO::PARAM_INT);
        $STH->bindValue(':time', time(), PDO::PARAM_INT);
        $STH->execute();

        if ($STH->rowCount()) {
            static::$USER_RIGHTS = 0;
            while ($result = $STH->fetch()) {
                static::$USER_BAN[$result['ban_type']] = 1;
            }
        }
    }

    /**
     * Фиксация истории адресов IP
     *
     * @param int $ip
     * @param int $ipvia
     */
    private function _userIpHistory($ip, $ipvia)
    {
        if ($ip != static::$IP || $ipvia != static::$IP_VIA_PROXY) {
            $STH = DB::PDO()->prepare('
                SELECT * FROM `cms_user_ip`
                WHERE `user_id`    = :uid
                AND `ip`           = :ip
                AND `ip_via_proxy` = :ipvia
                LIMIT 1
            ');

            $STH->bindValue(':uid', static::$USER_ID, PDO::PARAM_INT);
            $STH->bindValue(':ip', static::$IP, PDO::PARAM_INT);
            $STH->bindParam(':ipvia', static::$IP_VIA_PROXY, PDO::PARAM_INT);
            $STH->execute();

            if ($STH->rowCount()) {
                // Обновляем имеющуюся запись
                $result = $STH->fetch();

                $STHU = DB::PDO()->prepare('
                    UPDATE `cms_user_ip` SET
                    `user_agent` = :ua,
                    `timestamp`  = :time
                    WHERE `id`   = :id
                ');

                $STHU->bindValue(':ua', static::$USER_AGENT, PDO::PARAM_STR);
                $STHU->bindValue(':time', time(), PDO::PARAM_INT);
                $STHU->bindValue(':id', $result['id'], PDO::PARAM_INT);
                $STHU->execute();
            } else {
                // Вставляем новую запись
                $STHI = DB::PDO()->prepare('
                    INSERT INTO `cms_user_ip` SET
                    `user_id`      = :uid,
                    `ip`           = :ip,
                    `ip_via_proxy` = :ipvia,
                    `user_agent`   = :ua,
                    `timestamp`    = :time
                ');

                $STHI->bindValue(':uid', static::$USER_ID, PDO::PARAM_INT);
                $STHI->bindValue(':ip', static::$IP, PDO::PARAM_INT);
                $STHI->bindValue(':ipvia', static::$IP_VIA_PROXY, PDO::PARAM_INT);
                $STHI->bindValue(':ua', static::$USER_AGENT, PDO::PARAM_STR);
                $STHI->bindValue(':time', time(), PDO::PARAM_INT);
                $STHI->execute();
            }
        }
    }

    /**
     * Автоочистка системы
     */
    private function _autoClean()
    {
        if (static::$SYSTEM_SET['clean_time'] < time() - 86400) {
            DB::PDO()->exec("DELETE FROM `cms_sessions` WHERE `last_visit` < '" . (time() - 86400) . "'");
            DB::PDO()->exec("DELETE FROM `cms_users_iphistory` WHERE `time` < '" . (time() - 2592000) . "'");
            DB::PDO()->exec("UPDATE `cms_settings` SET  `val` = '" . time() . "' WHERE `key` = 'clean_time' LIMIT 1");
            DB::PDO()->exec("OPTIMIZE TABLE `cms_sessions` , `cms_users_iphistory`, `cms_users_settings`");
        }
    }

    /**
     * Определение мобильного браузера
     *
     * @return bool
     */
    private function _mobileDetect()
    {
        if (isset($_SESSION['is_mobile'])) {
            return $_SESSION['is_mobile'] == 1 ? TRUE : FALSE;
        }
        $user_agent = strtolower($_SERVER['HTTP_USER_AGENT']);
        $accept = isset($_SERVER['HTTP_ACCEPT']) ? strtolower($_SERVER['HTTP_ACCEPT']) : '';
        if ((strpos($accept, 'text/vnd.wap.wml') !== FALSE) || (strpos($accept, 'application/vnd.wap.xhtml+xml') !== FALSE)) {
            $_SESSION['is_mobile'] = 1;

            return TRUE;
        }
        if (isset($_SERVER['HTTP_X_WAP_PROFILE']) || isset($_SERVER['HTTP_PROFILE'])) {
            $_SESSION['is_mobile'] = 1;

            return TRUE;
        }
        if (preg_match('/android|avantgo|blackberry|blazer|compal|elaine|fennec|hiptop|iemobile|ip(hone|od)|iris|kindle|lge |maemo|midp|mmp|opera m(ob|in)i|palm( os)?|phone|p(ixi|re)\/|plucker|pocket|psp|symbian|treo|up\.(browser|link)|vodafone|wap|windows (ce|phone)|xda|xiino/i', $user_agent)
            || preg_match('/1207|6310|6590|3gso|4thp|50[1-6]i|770s|802s|a wa|abac|ac(er|oo|s\-)|ai(ko|rn)|al(av|ca|co)|amoi|an(ex|ny|yw)|aptu|ar(ch|go)|as(te|us)|attw|au(di|\-m|r |s )|avan|be(ck|ll|nq)|bi(lb|rd)|bl(ac|az)|br(e|v)w|bumb|bw\-(n|u)|c55\/|capi|ccwa|cdm\-|cell|chtm|cldc|cmd\-|co(mp|nd)|craw|da(it|ll|ng)|dbte|dc\-s|devi|dica|dmob|do(c|p)o|ds(12|\-d)|el(49|ai)|em(l2|ul)|er(ic|k0)|esl8|ez([4-7]0|os|wa|ze)|fetc|fly(\-|_)|g1 u|g560|gene|gf\-5|g\-mo|go(\.w|od)|gr(ad|un)|haie|hcit|hd\-(m|p|t)|hei\-|hi(pt|ta)|hp( i|ip)|hs\-c|ht(c(\-| |_|a|g|p|s|t)|tp)|hu(aw|tc)|i\-(20|go|ma)|i230|iac( |\-|\/)|ibro|idea|ig01|ikom|im1k|inno|ipaq|iris|ja(t|v)a|jbro|jemu|jigs|kddi|keji|kgt( |\/)|klon|kpt |kwc\-|kyo(c|k)|le(no|xi)|lg( g|\/(k|l|u)|50|54|e\-|e\/|\-[a-w])|libw|lynx|m1\-w|m3ga|m50\/|ma(te|ui|xo)|mc(01|21|ca)|m\-cr|me(di|rc|ri)|mi(o8|oa|ts)|mmef|mo(01|02|bi|de|do|t(\-| |o|v)|zz)|mt(50|p1|v )|mwbp|mywa|n10[0-2]|n20[2-3]|n30(0|2)|n50(0|2|5)|n7(0(0|1)|10)|ne((c|m)\-|on|tf|wf|wg|wt)|nok(6|i)|nzph|o2im|op(ti|wv)|oran|owg1|p800|pan(a|d|t)|pdxg|pg(13|\-([1-8]|c))|phil|pire|pl(ay|uc)|pn\-2|po(ck|rt|se)|prox|psio|pt\-g|qa\-a|qc(07|12|21|32|60|\-[2-7]|i\-)|qtek|r380|r600|raks|rim9|ro(ve|zo)|s55\/|sa(ge|ma|mm|ms|ny|va)|sc(01|h\-|oo|p\-)|sdk\/|se(c(\-|0|1)|47|mc|nd|ri)|sgh\-|shar|sie(\-|m)|sk\-0|sl(45|id)|sm(al|ar|b3|it|t5)|so(ft|ny)|sp(01|h\-|v\-|v )|sy(01|mb)|t2(18|50)|t6(00|10|18)|ta(gt|lk)|tcl\-|tdg\-|tel(i|m)|tim\-|t\-mo|to(pl|sh)|ts(70|m\-|m3|m5)|tx\-9|up(\.b|g1|si)|utst|v400|v750|veri|vi(rg|te)|vk(40|5[0-3]|\-v)|vm40|voda|vulc|vx(52|53|60|61|70|80|81|83|85|98)|w3c(\-| )|webc|whit|wi(g |nc|nw)|wmlb|wonu|x700|xda(\-|2|g)|yas\-|your|zeto|zte\-/i', substr($user_agent, 0, 4))
        ) {
            $_SESSION['is_mobile'] = 1;

            return TRUE;
        }
        $_SESSION['is_mobile'] = 2;

        return FALSE;
    }
}