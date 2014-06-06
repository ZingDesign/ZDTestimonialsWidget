<?php
/**
 * Created by PhpStorm.
 * User: Sam
 * Date: 12/03/14
 * Time: 3:40 PM
 */

class FormHelper {

    private $form_name;
    private $form_slug;
    private $form_action;
    private $form_class;
    private $is_ajax;

    public function __construct( $form_settings=null ) {
        $form_name = $form_action = $is_ajax = null;

        if( is_array($form_settings) ) {
            extract( $this->check_defaults(array(
                'form_name'     => 'ZD custom form',
                'form_action'   => $_SERVER['REQUEST_URI'],
                'is_ajax'       => true
            ), $form_settings) );
        }
        else {
            $form_name = $form_settings;
        }

        $this -> form_name = $form_name;

        $this -> form_slug = str_replace(" ", "_", strtolower($form_name));

        $this -> form_action = $form_action;

        $this -> form_class = $is_ajax ? "zd-form zd-form-ajax" : "zd-form";

        $this -> is_ajax = $is_ajax;
    }

    function start_form($echo=1) {
        $html = '<form id="'.$this->form_slug.'" class="'.$this->form_class.'" method="post" action="'.$this->form_action.'">'."\n";

        if( !$echo )
            return $html;

        echo $html;
    }

    function get_start_form() {
        return $this->start_form(0);
    }

    function end_form($echo=1) {
        $html = '</form>';

        if( !$echo ) {
            return $html;
        }

        echo $html;
    }

    function get_end_form() {
        return $this->end_form(0);
    }

    function get_input($args=array()) {
        $echo = $html = $wrapper = $label_class = $id = $label = $type = $name = $wrapper_class = $placeholder =
        $form_name = $value = $options = $required = $error = $rows = $cols = $disabled = $input_class = $multiline =
            $validate = null;

        $before = $after = '';

        extract( $this->check_defaults(
            Settings::get_form_defaults(), $args) );

        $form_name = $this->form_slug;

        // if type is select, but no options have been provided, return error
        if('select' === $type && empty($options)) {
            echo '<p class="zdf-error">Error: Select input requires options</p>';
            return false;
        }

        $is_button = in_array( $type, array('submit', 'button') );

//        $_wrapper_class = ('' !== $wrapper_class) ? " class=\"$wrapper_class\"" : '';

        $_wrapper_class = $wrapper_class ? ' class="' . $wrapper_class . '"' : '';

        if( ($wrapper || $wrapper_class) && !$is_button ) {
            $wrapper .= $_wrapper_class;
            $before = "<{$wrapper}>\n";
            $after = "</{$wrapper}>\n";
        }

        //If the ID is not set, use the label
        //Replace spaces with dashes
        $_id = ( '' === $id || !isset($id) ) ? strtolower(str_replace(" ", "-", $label)) : $id;

        // Strip all non-alphanumeric characters from the ID
        $_id = preg_replace("/[^A-Za-z0-9- ]/", '', $_id);
        // If the label contains the word 'email', make the input type email
        $contains_email = strpos( strtolower($label), "email" );

        if( 'text' === $type && ($contains_email !== false) ) {
            $type = 'email';
        }

        //If the name is not set
        if( '' === $name ) {
            //Replace dashes with underscores
            $_name = str_replace('-', '_', $_id);

            //if the form name has been provided,
            //Format the name as a property of the form name
//            if( '' !== $form_name  ) {
//                $_name = "{$form_name}[{$_name}]";
//            }
//            $_name .= "[{$type}]";

        }
        else {
            $_name = $name;
        }


        // If the label class is still a string (ie. not false), format the class attr.
        $_label_class = is_string($label_class) ? " class=\"{$label_class}\"" : '';

        //If the placeholder has not been set, make it the value of the label
        $_placeholder = ('' !== $placeholder) ? $placeholder : $label;

        // Add HTML5 required attribute if required
        $_required = ($required) ? " required aria-required=\"true\"" : "";

        $_disabled = $disabled ? " disabled" : "";

//        $_input_class = ('' !== $input_class) ? $input_class : '';

        if( $error !== false ) {
            $input_class .= ' zdf-error';
        }

        //Concatenate common attributes for reuse
        $attributes = "class=\"{$input_class}\" type=\"{$type}\" id=\"{$_id}\" name=\"{$_name}\"{$_required}{$_disabled}";

        //If the input type is not select or checkbox, add the placeholder attr.
        if( ! in_array( $type, array('select', 'checkbox' )) ) {
            $attributes .=  " placeholder=\"{$_placeholder}\"";
        }

        // Form mark-up begins:
        $html .= $before;

        //If it is a button, don't add a label
        if( ! $is_button ) {
            $html .= "<label{$_label_class} for=\"{$_id}\">{$label}</label>\n";
        }

        if( $multiline ) {
            $html .= "<br/>\n";
        }

        //If it is a textarea, format as textarea
        $_rows = $rows ? " rows=\"$rows\" style=\"height:auto;\"" : '';
        $_cols = $cols ? " cols=\"$cols\" style=\"width:auto;\"" : '';

        if( 'textarea' === $type ) {
            $attributes .= $_rows . $_cols;
            $html .= "<textarea {$attributes}>{$value}</textarea>";
        }
        else if('select' === $type ) {
            $html .= "<select {$attributes}>\n";
            $html .= "<option value=\"-1\">{$label}</option>\n";

            foreach($options as $opt => $val) {
                $selected = $val === $value ? " selected" : "";
                $html .= "<option value=\"{$val}\"{$selected}>{$opt}</option>\n";
            }

            $html .= "</select>\n";
        }
        else {
            $attributes .= " value=\"{$value}\"";
            $html .= "<input {$attributes} />\n";
        }

//        if( $error ) {
        $html .= "<span id=\"{$id}-error\" class=\"zdf-error\">{$error}</span>\n";
//        }

        $html .= $after;

        if( ! $echo )
            return $html;

        echo $html;
    }

    private function check_defaults( $defaults, $modified ) {
        $out = array();

        foreach($defaults as $name => $default) {
            if ( array_key_exists($name, $modified) )
                $out[$name] = $modified[$name];
            else
                $out[$name] = $default;
        }

        return $out;
    }
}

//class FormHelperFactory {
//
//    static function input( $args=array() ) {
//        $form_helper = new FormHelper();
//        return $form_helper->get_input($args);
//    }
//
//
//}