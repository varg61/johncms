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
    public $news; // Текст новостей
    public $newscount; // Общее к-во новостей
    public $lastnewsdate; // Дата последней новости
    private $settings = array();

    function __construct()
    {
        $this->settings = isset(Vars::$SYSTEM_SET['news'])
            ? unserialize(Vars::$SYSTEM_SET['news'])
            : array(
                'view'     => 1,
                'breaks'   => 1,
                'smilies'  => 1,
                'tags'     => 1,
                'comments' => 1,
                'size'     => 500,
                'quantity' => 3,
                'days'     => 7
            );
        $this->newscount = $this->_newsCount() . $this->_lastNewsCount();
        $this->news = $this->news();
    }

    /**
     * Запрос свежих новостей на Главную
     *
     * @return bool|string
     */
    private function news()
    {
        global $lng;
        if ($this->settings['view'] > 0) {
            $reqtime = $this->settings['days'] ? time() - ($this->settings['days'] * 86400) : 0;
            $STH = DB::PDO()->query('
                SELECT * FROM `cms_news`
                WHERE `time` > ' . $reqtime . '
                ORDER BY `id` DESC
                LIMIT ' . $this->settings['quantity']
            );
            if ($STH->rowCount()) {
                $news = '';
                for ($i = 0; $res = $STH->fetch(); ++$i) {
                    $text = $res['text'];
                    // Если текст больше заданного предела, обрезаем
                    if (mb_strlen($text) > $this->settings['size']) {
                        $text = mb_substr($text, 0, $this->settings['size']);
                        $text = htmlentities($text, ENT_QUOTES, 'UTF-8');
                        $text .= ' <a href="' . Vars::$HOME_URL . 'news/">' . $lng['next'] . '...</a>';
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
                    if ($this->settings['smilies']) {
                        $text = Functions::smilies($text);
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
                        $komm = DB::PDO()->query("SELECT COUNT(*) FROM `forum` WHERE `type` = 'm' AND `refid` = '" . $res['comments'] . "'")->fetchColumn();
                        if ($komm >= 1)
                            $news .= '<br /><a href="../forum/?id=' . $res['comments'] . '">' . $lng['discuss'] . '</a> (' . ($komm - 1) . ')';
                    }
                    $news .= '</div>';
                }

                return $news;
            } else {
                return FALSE;
            }
        }

        return FALSE;
    }

    /**
     * Счетчик всех новостей
     *
     * @return integer
     */
    private function _newsCount()
    {
        return DB::PDO()->query('SELECT COUNT(*) FROM `cms_news`')->fetchColumn();
    }

    /**
     * Счетчик свежих новостей
     *
     * @return bool|string
     */
    private function _lastNewsCount()
    {
        $req = DB::PDO()->query('SELECT COUNT(*) FROM `cms_news` WHERE `time` > ' . (time() - 259200))->fetchColumn();

        return ($req ? '/<span class="red">+' . $req . '</span>' : FALSE);
    }
}