<?php

/**
 * @package     JohnCMS
 * @link        http://johncms.com
 * @copyright   Copyright (C) 2008-2012 JohnCMS Community
 * @license     LICENSE.txt (see attached file)
 * @version     VERSION.txt (see attached file)
 * @author      http://johncms.com/about
 */

class Fields
{
    private $elements = array(
        'checkbox'     => array('<input%s%s type="checkbox" value="1"%s%s/>', 'id,name,class,checked'),
        'description'  => array('<span%s>%s</span>', 'description_class,description'),
        'file'         => array('<input%s%s%s type="file"/>', 'id,name,class'),
        'hidden'       => array('<input%s type="hidden" value="%s"/>', 'name,value'),
        'label'        => array('<label%s%s>%s</label>', 'for,label_class,label'),
        'label_inline' => array('<label%s>%s%s</label>', 'label_inline_class,content,label_inline'),
        'option'       => array('<option value="%s"%s>%s</option>', 'value,selected,label'),
        'password'     => array('<input%s%s type="password" value="%s"%s/>', 'id,name,value,class'),
        'radio'        => array('<input%s type="radio" value="%s"%s%s/>', 'name,value,class,checked'),
        'select'       => array('<select%s%s%s>%s</select>', 'name,class,multiple,content'),
        'submit'       => array('<input%s%s type="submit" value="%s"%s/>', 'id,name,value,class'),
        'text'         => array('<input%s%s type="text" value="%s"%s%s/>', 'id,name,value,class,maxlength'),
        'textarea'     => array('<textarea%s%s%s>%s</textarea>', 'id,name,class,value'),
    );

    private $attributes = array(
        'checked'            => ' checked="checked"',
        'class'              => ' class="%s"',
        'content'            => '%s',
        'description'        => '%s',
        'description_class'  => ' class="%s"',
        'disabled'           => ' disabled="disabled"',
        'for'                => ' for="%s"',
        'id'                 => ' id="%s"',
        'label'              => '%s',
        'label_class'        => ' class="%s"',
        'label_inline'       => '%s',
        'label_inline_class' => ' class="%s"',
        'maxlength'          => ' maxlength="%u"',
        'multiple'           => ' multiple="multiple"',
        'name'               => ' name="%s"',
        'selected'           => ' selected="selected"',
        'value'              => '%s',
    );

    private $type;
    private $option;

    public function __construct($type, $name, array $option = array())
    {
        $this->type = $type;
        $option['name'] = $name;
        $this->option = $option;
    }

    /**
     * Сборка готового элемента с декораторами
     *
     * @return string
     */
    public function __toString()
    {
        $out = array();

        // Добавляем метку LABEL
        if (isset($this->option['label'])) {
            if (!isset($this->option['id'])) {
                $this->option['id'] = $this->option['name'];
            }
            $this->option['for'] = $this->option['id'];
            $out[] = $this->_build('label', $this->option);
        }

        if ($this->type == 'radio') {
            // Добавляем элемент RADIO
            if (!isset($this->option['items']) || !is_array($this->option['items'])) {
                return 'ERROR: missing radio element items';
            }

            foreach ($this->option['items'] as $value => $label) {
                $radio['name'] = $this->option['name'];
                $radio['value'] = $value;

                if (isset($this->option['checked']) && $this->option['checked'] == $value) {
                    $radio['checked'] = TRUE;
                }

                if (empty($label)) {
                    $out[] = $this->_build('radio', $radio);
                } else {
                    $radio['label_inline'] = $label;
                    $radio['label_inline_class'] = isset($this->option['label_inline_class']) ? $this->option['label_inline_class'] : 'inline';
                    $radio['content'] = $this->_build('radio', $radio);
                    $out[] = $this->_build('label_inline', $radio);
                }
                unset($radio, $value, $label);
            }
        } elseif ($this->type == 'select') {
            // Добавляем элемент SELECT
            $multiple = isset($this->option['multiple']) && $this->option['multiple'] ? TRUE : FALSE;
            if (isset($this->option['items']) && is_array($this->option['items'])) {
                $list = array();
                foreach ($this->option['items'] as $value => $label) {
                    if (empty($label)) {
                        $listElement['label'] = $value;
                    }

                    if (isset($this->option['selected'])) {
                        if ($multiple && is_array($this->option['selected'])) {
                            if (in_array($value, $this->option['selected'])) {
                                $listElement['selected'] = TRUE;
                            }
                        } else {
                            if ($this->option['selected'] == $value) {
                                $listElement['selected'] = TRUE;
                            }
                        }
                    }

                    $listElement['value'] = $value;
                    $list[] = $this->_build('option', $listElement);
                    unset($listElement, $value, $label);
                }
                $this->option['content'] = "\n" . implode("\n", $list) . "\n";
            }
            if ($multiple) {
                $this->option['name'] = $this->option['name'] . '[]';
            }
            $out[] = $this->_build('select', $this->option);
        } else {
            // Добавляем простой элемент
            if (isset($this->option['label_inline'])) {
                if (!isset($this->option['label_inline_class'])) {
                    $this->option['label_inline_class'] = 'inline';
                }
                $this->option['content'] = $this->_build($this->option['type'], $this->option);
                $out[] = $this->_build('label_inline', $this->option);
            } else {
                $out[] = $this->_build($this->type, $this->option);
            }
        }

        // Добавляем описание DESCRIPTION
        if (isset($this->option['description'])) {
            if(!isset($this->option['description_class'])){
                $this->option['description_class'] = 'description';
            }
            $out[] = $this->_build('description', $this->option);
        }

        return implode("\n", $out);
    }

    /**
     * Создание элемента
     *
     * @param string $type
     * @param array $option
     * @return string
     */
    private function _build($type, array $option)
    {
        $placeholders = array();
        if(isset($option['value']) && !is_numeric($option['value'])){
            $option['value'] = htmlspecialchars($option['value'], ENT_QUOTES, 'UTF-8');
        }
        foreach (explode(',', $this->elements[$type][1]) as $val) {
            if (isset($option[$val]) && (!empty($option[$val]) || $option[$val] == 0) && isset($this->attributes[$val])) {
                $placeholders[] = sprintf($this->attributes[$val], $option[$val]);
            } else {
                $placeholders[] = '';
            }
        }

        return vsprintf($this->elements[$type][0], $placeholders);
    }
}
