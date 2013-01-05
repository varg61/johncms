<?php

/**
 * @package     JohnCMS
 * @link        http://johncms.com
 * @copyright   Copyright (C) 2008-2012 JohnCMS Community
 * @license     LICENSE.txt (see attached file)
 * @version     VERSION.txt (see attached file)
 * @author      http://johncms.com/about
 */

//TODO: Добавить валидаторы
//TODO: Добавить обработку CAPTCHA

class Form
{
    private $_form;
    private $_fields = array();
    private $_submits = array();
    private $_fieldset = FALSE;

    public $input;
    public $errors = array();
    public $validationToken = TRUE;
    public $isSubmitted = FALSE;
    public $validOutput = array();

    public function __construct($action, $method = '', $name = 'form')
    {
        $this->_form = array(
            'type'   => 'form',
            'name'   => $name,
            'action' => $action,
            'method' => ($method == 'get' ? 'get' : 'post')
        );

        // Передаем по ссылке значения суперглобальных переменных
        if ($this->_form['method'] == 'get') {
            $this->input =& $_GET;
        } else {
            $this->input =& $_POST;
        }
    }

    /**
     * Добавляем элементы формы
     *
     * @param string $type             Тип добавляемого элемента
     * @param string $name             Имя элемента
     * @param array $option            Дополнительные параметры
     * @return Form
     */
    public function add($type, $name, array $option = array())
    {
        if ($type == 'submit') {
            $this->_submits[] = $name;
        } elseif ($type == 'file') {
            $this->_form['enctype'] = TRUE;
        }

        $option['type'] = $type;
        $option['name'] = $name;
        $this->_fields[] = $option;

        unset($option);
        return $this;
    }

    /**
     * Добавляем HTML код
     *
     * Строка ни как не обрабатывается и передается в форму как есть.
     *
     * @param $str
     * @return Form
     */
    public function addHtml($str)
    {
        $option['type'] = 'html';
        $option['content'] = $str;
        $this->_fields[] = $option;
        unset($option);
        return $this;
    }

    public function fieldsetStart($legend = null)
    {
        $option['type'] = 'fs_start';
        if (!is_null($legend)) {
            $option['legend'] .= $legend;
        }
        $this->_fields[] = $option;
        unset($option);
        return $this;
    }

    public function fieldsetEnd()
    {
        $option['type'] = 'fs_end';
        $this->_fields[] = $option;
        unset($option);
        return $this;
    }

    /**
     * Сборка готовой формы
     *
     * @return string                  Готовая форма
     */
    public function display()
    {
        // Проверка формы на корректный Submit
        if (count(array_intersect($this->_submits, array_keys($this->input)))
            && (!$this->validationToken || isset($this->input['form_token']))
            && (!$this->validationToken || isset($_SESSION['form_token']))
            && (!$this->validationToken || $this->input['form_token'] == $_SESSION['form_token'])
        ) {
            $this->isSubmitted = TRUE;
        }

        $out = array();
        foreach ($this->_fields as &$element) {
            switch ($element['type']) {
                case'html':
                    $out[] = $element['content'];
                    break;

                case'fs_start':
                    if ($this->_fieldset) {
                        $out[] = '</fieldset>';
                    }
                    $out[] = '<fieldset>' . (isset($element['legend']) ? '<legend>' . $element['legend'] . '</legend>' : '');
                    $this->_fieldset = TRUE;
                    break;

                case'fs_end':
                    $out[] = '</fieldset>';
                    $this->_fieldset = FALSE;
                    break;

                default:
                    // Если был SUBMIT, то присваиваем VALUE значения из суперглобальных массивов
                    if ($this->isSubmitted === TRUE) {
                        $this->_setValues($element);
                    }

                    // Создаем элемент формы
                    $out[] = new Fields($element['type'], $element['name'], $element);
            }
        }

        if ($this->_fieldset) {
            $out[] = '</fieldset>';
        }

        unset($this->_fields);

        // Добавляем токен валидации
        if ($this->validationToken) {
            if (!isset($_SESSION['form_token'])) {
                $_SESSION['form_token'] = md5(mt_rand(1000, 100000) . microtime(TRUE));
            }
            $out[] = new Fields('hidden', 'form_token', array('value' => $_SESSION['form_token']));
        }

        return sprintf("\n" . '<form action="%s" method="%s" name="%s"%s>%s</form>' . "\n",
            $this->_form['action'],
            $this->_form['method'],
            $this->_form['name'],
            (isset($this->_form['enctype']) ? ' enctype="multipart/form-data"' : ''),
            "\n" . implode("\n", $out) . "\n"
        );
    }

    /**
     * @param array $option
     */
    private function _setValues(array &$option)
    {
        switch ($option['type']) {
            case'text':
            case'password':
            case'hidden':
            case'textarea':
                if (isset($this->input[$option['name']])) {
                    $option['value'] = trim($this->input[$option['name']]);
                    unset($this->input[$option['name']]);
                    if (isset($option['filter'])) {
                        $this->_filter($option);
                    }
                    $this->validOutput[$option['name']] = $option['value'];
                } else {
                    $this->isSubmitted = FALSE;
                }
                break;

            case'radio':
                if (isset($this->input[$option['name']]) && isset($option['items'])) {
                    if (array_key_exists($this->input[$option['name']], $option['items'])) {
                        $option['checked'] = trim($this->input[$option['name']]);
                        $this->validOutput[$option['name']] = $option['checked'];
                        unset($this->input[$option['name']]);
                    } else {
                        $this->isSubmitted = FALSE;
                    }
                }
                break;

            case'select':
                if (isset($this->input[$option['name']]) && isset($option['items'])) {
                    $allow = TRUE;
                    if (isset($option['multiple']) && $option['multiple']) {
                        foreach ($this->input[$option['name']] as $val) {
                            if (!array_key_exists($val, $option['items'])) {
                                $allow = FALSE;
                                break;
                            }
                        }
                    } else {
                        if (!array_key_exists($this->input[$option['name']], $option['items'])) {
                            $allow = FALSE;
                        }
                    }

                    if ($allow) {
                        $option['selected'] = $this->input[$option['name']];
                        $this->validOutput[$option['name']] = $option['selected'];
                        unset($this->input[$option['name']]);
                    } else {
                        $this->isSubmitted = FALSE;
                    }
                }
                break;

            case'checkbox':
                if (isset($this->input[$option['name']])) {
                    unset($this->input[$option['name']]);
                    $option['checked'] = 1;
                    $this->validOutput[$option['name']] = 1;
                } else {
                    unset($option['checked']);
                    $this->validOutput[$option['name']] = 0;
                }
                break;
        }
    }

    /**
     * @param $option
     */
    private function _filter(&$option)
    {
        $min = isset($option['filter']['min']) ? intval($option['filter']['min']) : FALSE;
        $max = isset($option['filter']['max']) ? intval($option['filter']['max']) : FALSE;

        switch ($option['filter']['type']) {
            case'str':
            case'string':
                if (isset($option['filter']['regexp_search'])) {
                    $replace = isset($option['filter']['regexp_replace']) ? $option['filter']['regexp_replace'] : '';
                    $option['value'] = preg_replace($option['filter']['regexp_search'], $replace, $option['value']);
                }
                if ($max && mb_strlen($option['value']) > $max) {
                    $option['value'] = mb_substr($option['value'], 0, $max);
                }
                break;

            case'int':
            case'integer':
                $option['value'] = intval($option['value']);
                if ($min !== FALSE && $option['value'] < $min) {
                    $option['value'] = $min;
                }
                if ($max !== FALSE && $option['value'] > $max) {
                    $option['value'] = $max;
                }
                break;

            default:
                $this->errors[] = 'Unknown filter: ' . $option['filter']['type'];
        }
    }
}