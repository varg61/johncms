<?php

/**
 * @package     JohnCMS
 * @link        http://johncms.com
 * @copyright   Copyright (C) 2008-2012 JohnCMS Community
 * @license     LICENSE.txt (see attached file)
 * @version     VERSION.txt (see attached file)
 * @author      http://johncms.com/about
 */

class HomePage
{
    public $news;                                                              // Текст новостей
    public $newscount;                                                         // Общее к-во новостей
    public $lastnewsdate;                                                      // Дата последней новости
    private $settings = array();

    function __construct()
    {
        global $set;
        $this->settings = unserialize(Vars::$SYSTEM_SET['news']);
        $this->newscount = $this->_newsCount() . $this->_lastNewsCount();
        $this->news = $this->news();
    }

    // Запрос свежих новостей на Главную
    private function news()
    {
        global $lng;
        if ($this->settings['view'] > 0) {
            $reqtime = $this->settings['days'] ? time() - ($this->settings['days'] * 86400) : 0;
            $req = mysql_query("SELECT * FROM `cms_news` WHERE `time` > '$reqtime' ORDER BY `id` DESC LIMIT " . $this->settings['quantity']);
            if (mysql_num_rows($req) > 0) {
                $i = 0;
                $news = '';
                while (($res = mysql_fetch_array($req)) !== false) {
                    $text = $res['text'];
                    // Если текст больше заданного предела, обрезаем
                    if (mb_strlen($text) > $this->settings['size']) {
                        $text = mb_substr($text, 0, $this->settings['size']);
                        $text = htmlentities($text, ENT_QUOTES, 'UTF-8');
                        $text .= ' <a href="' . Vars::$HOME_URL . '/news">' . $lng['next'] . '...</a>';
                    } else {
                        $text = htmlentities($text, ENT_QUOTES, 'UTF-8');
                    }
                    // Если включены переносы, то обрабатываем
                    if ($this->settings['breaks'])
                        $text = str_replace("\r\n", "<br/>", $text);
                    // Обрабатываем тэги
                    if ($this->settings['tags']) {
                        $text = TextParser::tags($text);
                    } else {
                        $text = TextParser::noTags($text);
                    }
                    // Обрабатываем смайлы
                    if ($this->settings['smileys']) {
                        $text = Functions::smileys($text);
                    }
                    // Определяем режим просмотра заголовка - текста
                    $news .= '<div class="news">';
                    switch ($this->settings['view']) {
                        case 2:
                            $news .= '<a href="news/index.php">' . $res['name'] . '</a>';
                            break;

                        case 3:
                            $news .= $text;
                            break;
                        default :
                            $news .= '<b>' . $res['name'] . '</b><br />' . $text;
                    }
                    // Ссылка на каменты
                    if (!empty($res['comments']) && $this->settings['view'] != 2 && $this->settings['kom'] == 1) {
                        $mes = mysql_query("SELECT COUNT(*) FROM `forum` WHERE `type` = 'm' AND `refid` = '" . $res['comments'] . "'");
                        $komm = mysql_result($mes, 0) - 1;
                        if ($komm >= 0)
                            $news .= '<br /><a href="../forum/?id=' . $res['comments'] . '">' . $lng['discuss'] . '</a> (' . $komm . ')';
                    }
                    $news .= '</div>';
                    ++$i;
                }
                return $news;
            } else {
                return false;
            }
        }
    }

    // Счетчик всех новостей
    private function _newsCount()
    {
        $req = mysql_query("SELECT COUNT(*) FROM `cms_news`");
        $res = mysql_result($req, 0);
        return ($res > 0 ? $res : '0');
    }

    // Счетчик свежих новостей
    private function _lastNewsCount()
    {
        $req = mysql_query("SELECT COUNT(*) FROM `cms_news` WHERE `time` > '" . (time() - 259200) . "'");
        $res = mysql_result($req, 0);
        return ($res > 0 ? '/<span class="red">+' . $res . '</span>' : false);
    }
}