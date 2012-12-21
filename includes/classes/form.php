<?php

class Form
{
    private $_form = array(); // Параметры формы
    private $_fields = array(); // Параметры всех полей формы
    private $_submitButton = array(); // Имена submit кнопок
    private $_token; // Использовать токен для валидации форм

    public $input; // Данные, полученные от $_POST, или $_GET
    public $validInput;
    public $submit = FALSE; // Был ли Submit формы?

    /**
     * @param $action
     * @param string $method
     * @param string $name
     * @param bool $token
     */
    public function __construct($action, $method = '', $name = 'form', $token = TRUE)
    {
        $this->_form['name'] = $name;
        $this->_form['action'] = $action;
        $this->_form['method'] = $method == 'get' ? 'get' : 'post';
        $this->_token = $token ? TRUE : FALSE;

        // Передаем по ссылке значения суперглобальных переменных
        if ($this->_form['method'] == 'get') {
            $this->input =& $_GET;
        } else {
            $this->input =& $_POST;
        }
    }

    /**
     * @param $type
     * @param $id
     * @param array $option
     * @return Form
     */
    public function addField($type, $id, array $option = array())
    {
        $option['type'] = $type;
        $option['id'] = $id;

        if (empty($id)) {
            $option['type'] = 'html';
            $option['value'] = 'ERROR: empty ID';
        }

        if (!isset($option['name']) || empty($option['name'])) {
            $option['name'] = $id;
        }

        if ($type == 'submit') {
            $this->_submitButton[] = $option['name'];
        }

        $this->_fields[] = $option;
        return $this;
    }

    /**
     * @param $str
     * @return Form
     */
    public function addHtml($str)
    {
        $option['type'] = 'html';
        $option['value'] = $str;
        $this->_fields[] = $option;
        return $this;
    }

    /**
     * Посторение формы
     *
     * @return string                  Готовая форма
     */
    public function display()
    {
        // Проверка формы на Submit
        if (count(array_intersect($this->_submitButton, array_keys($this->input)))
            && (!$this->_token || isset($this->input['form_token']))
            && (!$this->_token || isset($_SESSION['form_token']))
            && (!$this->_token || $this->input['form_token'] == $_SESSION['form_token'])
        ) {
            $this->submit = TRUE;
        }

        $i = 1;
        $out = array();
        foreach ($this->_fields as $val) {
            switch ($val['type']) {
                case'file':
                    $this->_form['enctype'] = 'multipart/form-data';
                case'hidden':
                case'password':
                case'reset':
                case'submit':
                case'text':
                    $out[$i] = $this->_buildInput($val);
                    break;

                case'textarea':
                    $out[$i] = $this->_buildTextarea($val);
                    break;

                case'radio':
                    $out[$i] = $this->_buildRadio($val);
                    break;

                case'checkbox':
                    $out[$i] = $this->_buildCheckbox($val);
                    break;

                case'html':
                    $out[$i] = $val['value'];
                    break;

                default:
                    $out[$i] = 'ERROR: unknown type: ' . $val['type'];
            }

            // Добавляем метку label
            $out[$i] = $this->_buildLabel($out[$i], $val);

            // Добавляем описание
            if (isset($val['description'])) {
                $out[$i] .= '<span class="description">' . $val['description'] . '</span>';
            }

            ++$i;
        }

        // Создаем токен для валидации формы
        if ($this->_token) {
            if (!isset($_SESSION['form_token'])) {
                $_SESSION['form_token'] = Functions::generateToken();
            }
            $token = '<input type="hidden" name="form_token" value="' . $_SESSION['form_token'] . '"/>';
        } else {
            $token = '';
        }

        return "\n" . '<form action="' . $this->_form['action'] . '" method="' . $this->_form['method'] . '" name="' . $this->_form['name'] . '">' .
            "\n" . implode("\n", $out) . "\n" .
            $token . "\n" .
            '</form>' . "\n";
    }

    /**
     * Создаем элемент формы input
     *
     * @param array $option            Массив с параметрами
     * @return string                  Готовый элемент формы
     * @uses Validate::checkout        Очистка строки и преобразование в HTML сущности
     */
    private function _buildInput(array $option)
    {
        $this->_setValue($option);
        return '<input id="' . $option['id'] . '" name="' . $option['name'] . '" type="' . $option['type'] . '"' .
            (isset($option['class']) ? ' class="' . $option['class'] . '"' : '') .
            (isset($option['value']) ? ' value="' . Validate::checkout($option['value']) . '"' : '') .
            ($option['type'] == 'text' && isset($option['maxlength']) ? ' maxlength="' . $option['maxlength'] . '"' : '') .
            '/>';
    }

    /**
     * Создаем элемент формы textarea
     *
     * @param array $option            Массив с параметрами
     * @return string                  Готовый элемент формы
     * @uses Validate::checkout        Очистка строки и преобразование в HTML сущности
     */
    private function _buildTextarea(array $option)
    {
        $this->_setValue($option);
        return (isset($option['buttons']) && $option['buttons'] ? TextParser::autoBB($this->_form['name'], $option['name']) : '') .
            '<textarea id="' . $option['id'] . '" name="' . $option['name'] . '"' .
            ' rows="' . Vars::$USER_SET['field_h'] . '"' .
            (isset($option['class']) ? ' class="' . $option['class'] . '"' : '') . '>' .
            (isset($option['value']) ? Validate::checkout($option['value']) : '') .
            '</textarea>';
    }

    /**
     * Создаем группу элементов формы radio
     *
     * @param array $option            Массив с параметрами
     * @return string                  Готовый элемент формы
     */
    private function _buildRadio(array $option)
    {
        $this->_setValue($option);
        $out = array();
        foreach ($option['items'] as $radio_key => $radio_val) {
            $out[] = (!empty($radio_val) ? '<label class="' . (isset($option['label_class']) ? $option['label_class'] : 'inline') . '">' : '') .
                '<input type="radio" name="' . $option['id'] . '" value="' . $radio_key . '" ' .
                (isset($option['checked']) && $option['checked'] == $radio_key ? ' checked="checked"' : '') .
                (isset($option['class']) ? ' class="' . $option['class'] . '"' : '') .
                '/>' .
                (!empty($radio_val) ? $radio_val . '</label>' : '');
        }

        return implode("\n", $out);
    }

    /**
     * Создаем элемент формы checkbox
     *
     * @param array $option            Массив с параметрами
     * @return string                  Готовый элемент формы
     */
    private function _buildCheckbox(array $option)
    {
        $this->_setValue($option);
        return '<input type="checkbox" id="' . $option['id'] . '" name="' . $option['name'] . '" value="1"' .
            (isset($option['checked']) && $option['checked'] ? ' checked="checked"' : '') .
            '/>';
    }

    /**
     * Создаем метку label
     *
     * @param $field                   Элемент формы без меток
     * @param array $option            Массив с параметрами
     * @return string                  Готовый элемент формы с метками label
     */
    private function _buildLabel($field, array $option)
    {
        if (isset($option['label_inline'])) {
            $field = '<label class="' .
                (isset($option['label_inline_class']) ? $option['label_inline_class'] : 'inline') .
                '">' .
                $field .
                $option['label_inline'] .
                '</label>';
        }

        if (isset($option['label'])) {
            $field = '<label' .
                (isset($option['id']) ? ' for="' . $option['id'] . '"' : '') .
                (isset($option['label_class']) ? ' class="' . $option['label_class'] . '"' : '') .
                '>' .
                $option['label'] .
                '</label>' . "\n" . $field;
        }

        return $field;
    }

    private function _setValue(&$option)
    {
        //TODO: Добавить валидатор

        if ($this->submit && $option['type'] == 'checkbox') {
            // Задаем значения для полей checkbox
            $option['checked'] = isset($this->input[$option['id']]) ? 1 : 0;
            $this->validInput[$option['name']] = $option['checked'];
            unset($this->input[$option['name']]);
        } elseif ($this->submit && $option['type'] == 'radio') {
            // Задаем значения для полей radio
            $value = isset($this->input[$option['name']]) ? trim($this->input[$option['name']]) : FALSE;
            if ($value !== FALSE && array_key_exists($this->input[$option['name']], $option['items'])) {
                $option['checked'] = $value;
                $this->validInput[$option['name']] = $value;
            } else {
                $this->validInput[$option['name']] = isset($option['checked']) ? $option['checked'] : '';
            }
            unset($this->input[$option['name']]);
        } elseif ($this->submit && $option['type'] != 'submit') {
            // Задаем значения для текстовых полей
            if (isset($this->input[$option['name']])) {
                $option['value'] = trim($this->input[$option['name']]);
                $this->validInput[$option['name']] = $option['value'];
                unset($this->input[$option['name']]);
            } elseif (isset($option['value'])) {
                $this->validInput[$option['name']] = $option['value'];
            } else {
                $this->validInput[$option['name']] = '';
            }
        }
    }
}