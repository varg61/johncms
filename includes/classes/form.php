<?php

class Form
{
    private $_form = array(); // Параметры формы
    private $_fields = array(); // Параметры всех полей формы
    private $_submitButton = array(); // Имена submit кнопок
    private $_input; // Данные, полученные от $_POST, или $_GET
    private $_token; // Использовать токен для валидации форм

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
            $this->_input =& $_GET;
        } else {
            $this->_input =& $_POST;
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
     * @param $label
     * @param null $for
     * @param null $class
     * @return Form
     */
    public function addLabel($label, $for = null, $class = null)
    {
        $option['type'] = 'label';
        $option['label'] = $label;
        if ($for) {
            $option['id'] = $for;
        }
        if ($class) {
            $option['label_class'] = $class;
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
        $this->_isSubmit();

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

                case'label':
                    $out[$i] = '';
                    break;

                case'html':
                    $out[$i] = $val['value'];
                    break;

                default:
                    $out[$i] = 'ERROR: unknown type: ' . $val['type'];
            }

            // Добавляем метку label
            $out[$i] = $this->_buildLabel($out[$i], $val);

            ++$i;
        }
        return '<form action="' . $this->_form['action'] . '" method="' . $this->_form['method'] . '" name="' . $this->_form['name'] . '">' .
            "\n" . implode("\n", $out) . "\n" .
            $this->_buildToken() . "\n" .
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
        return '<input id="' . $option['id'] . '" name="' . $option['name'] . '" type="' . $option['type'] . '"' .
            (isset($option['class']) ? ' class="' . $option['class'] . '"' : '') .
            $this->_setValue($option, 1) .
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
        return '<textarea id="' . $option['id'] . '" name="' . $option['name'] . '"' .
            (isset($option['class']) ? ' class="' . $option['class'] . '"' : '') . '>' .
            $this->_setValue($option) .
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
        if ($this->submit && isset($this->_input[$option['id']])) {
            $option['checked'] = trim($this->_input[$option['id']]);
        }
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
        if ($this->submit) {
            $option['checked'] = isset($this->_input[$option['id']]);
        }
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
                '</label>' . $field;
        }

        return $field;
    }

    /**
     * Присвоение значения value
     *
     * IF submit фориы, то используются суперглобальные значения (если есть)
     * ELSE используются переданные в конструктор форм значения (если были)
     *
     * @param $option                  Массив с параметрами
     * @param bool $valueTag           Добавлять ли параметр value=""
     * @return string                  Значение value
     */
    private function _setValue($option, $valueTag = FALSE)
    {
        if ($this->submit && isset($this->_input[$option['id']])) {
            $value = $this->_input[$option['id']];
        } elseif (isset($option['value'])) {
            $value = $option['value'];
        } else {
            $value = '';
        }

        if ($valueTag && !empty($value)) {
            return ' value="' . Validate::checkout($value) . '"';
        }
        return $value;
    }

    /**
     * Создание скрытого поля с токеном для валидации формы
     *
     * @return string
     */
    private function _buildToken()
    {
        if ($this->_token) {
            if (!isset($_SESSION['form_token'])) {
                $_SESSION['form_token'] = Functions::generateToken();
            }
            return '<input type="hidden" name="form_token" value="' . $_SESSION['form_token'] . '"/>';
        } else {
            return '';
        }
    }

    /**
     * Проверка формы на Submit
     */
    private function _isSubmit()
    {
        if (count(array_intersect($this->_submitButton, array_keys($this->_input)))
            && (!$this->_token || isset($this->_input['form_token']))
            && (!$this->_token || isset($_SESSION['form_token']))
            && (!$this->_token || $this->_input['form_token'] == $_SESSION['form_token'])
        ) {
            $this->submit = 1;
        }
    }
}