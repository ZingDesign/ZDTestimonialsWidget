<?php
/**
 * Created by PhpStorm.
 * User: Sam
 * Date: 3/06/14
 * Time: 2:13 PM
 */

class ZDTestimonialsSettings {

    private $options;
    private $settings_page_id;
    private $prefix;
    private $option_group;

    function __construct() {
        $prefix = ZDTW_TEXT_DOMAIN;

        $this -> prefix = $prefix;

        if( is_admin() ) {
            add_action( 'admin_menu', array( $this, $prefix . '_init_menu' ));
            add_action( 'admin_init', array( $this, $prefix . '_theme_settings') );
        }
        else {
            add_action( 'wp_head', array( $this, $prefix . '_head_output') );
        }

        $options = array(
            'client_name' => array(
                'id'        => 'client-name',
                'type'      => 'text'
            ),
            'show_avatar' => array(
                'type' => 'checkbox'
            ),
            'show_testimonial' => array(
                'type' => 'checkbox'
            ),
            'show_first_name' => array(
                'type' => 'checkbox'
            ),
            'show_last_name' => array(
                'type' => 'checkbox'
            ),
            'show_job_title' => array(
                'type' => 'checkbox'
            ),
            'show_business_name' => array(
                'type' => 'checkbox'
            ),
            'show_location' => array(
                'type' => 'checkbox'
            ),
            'show_multiple_testimonials_in_side_bar' => array(
                'type' => 'checkbox'
            ),
            'testimonial_class_name' => array(
//                'id' => 'testimonial-class-name',
                'type' => 'text'
            ),
            'show_horizontal_rule' => array(
                'type' => 'checkbox'
            ),
            'button_background_colour' => array(
                'type' => 'text'
            ),
            'button_foreground_colour' => array(
                'type' => 'text'
            )


        );

        $this -> options = $options;
        $this -> settings_page_id = $prefix . '_menu_page';
//        $this -> option_group = $prefix . '_options_group';

    }

    function zdtw_init_menu() {
        // add_options_page( $page_title, $menu_title, $capability, $menu_slug, $function);
        add_options_page(
            'Zing Design Testimonials',
            'ZD Testimonials',
            'manage_options',
            $this->settings_page_id,
            array($this, $this->prefix . '_menu_options_callback')
        );
    }

    /*
     * Called in add_menu_page, called inside zdtw_init_menu
     */
    function zdtw_menu_options_callback() {
        echo '<form id="'.$this->prefix.'-form" class="zd-settings-form" method="POST" action="options.php">'."\n";

//        print_r( $this->options );
//        echo $this->settings_page_id;

        // settings_fields( $option_group )
        settings_fields( $this->settings_page_id );
//        settings_fields( $this->prefix . '-general-section' );

        do_settings_fields( $this->settings_page_id, $this->prefix .'-general-section' );

        submit_button();

        echo '</form>'."\n";
    }

    function zdtw_theme_settings() {
        $prefix = $this->prefix;

        // Add general settings section

        // add_settings_section( $id, $title, $callback, $page );
        add_settings_section(
            $prefix . '-general-section',
            __('General settings', $prefix),
            array($this, $prefix . '_section_callback'),
            $this->settings_page_id
        );

        // Options

        foreach( $this->options as $key => $option ) {
            if( isset( $option['id'] )) {
                $setting_id = $option['id'];
            }
            else {
                $setting_id = str_replace("_", "-", $key);
            }

            $setting_name = '_' . $prefix . '_' . $key;

            $title = str_replace("_", " ", ucfirst( $key ) );
            $option_type = $option['type'];

            $filter = isset($option['filter']) ? $option['filter'] : 'esc_html';

//            $section = isset($option['section']) ? $option['section'] : 'general';

            //add_settings_field( $id, $title, $callback, $page, $section, $args );

            $wrapper_class = 'zd-input-group ' . $option_type . '-group';

            add_settings_field(
                $setting_name,
                '<div class="'.$wrapper_class.'"><label class="'.$option_type.'-label" for="'.$setting_id.'">' . __($title, $prefix) . '</label>',
                array($this, $prefix . '_setting_input'),
                $this->settings_page_id,
                $prefix . '-general-section',
                array(
                    'id' => $setting_id
                    ,'name' => $setting_name
                    ,'type' => $option_type
                )
            );

            // register_setting( $option_group, $option_name, $sanitize_callback );
            register_setting(
                $this->settings_page_id,
                $setting_name,
                $filter
            );
        }
    }

    function zdtw_section_callback() {
        echo '<h2>General settings</h2>';
    }

    function zdtw_setting_input( $args ) {

//        print_r( $args );

        $type = $args['type'];
        $id = $args['id'];
        $name = $args['name'];

        if( ! $type ) {
            $type = 'text';
        }

        $is_checkbox = 'checkbox' === $type;

        if( ! $id ) {
            echo "<p class=\"error\">Error: ID required when initialising new setting</p>\n";
            return;
        }

        $stored_value = get_option( $name );

//        var_dump( $stored_value );

        if( ! $is_checkbox ) {
            $value = esc_attr( $stored_value );
        }


        if( in_array($type, array('text', 'email', 'number', 'checkbox')) ) {
            $checked = '';

            if( $is_checkbox ) {
//                $value = 1;
                $value = 1;
//                var_dump($value);
                $checked = checked( $stored_value, true, false );
            }

            echo "<input type=\"{$type}\" id=\"{$id}\" name=\"{$name}\" value=\"{$value}\"{$checked} />\n";
        }

        else if( 'image' === $type ) {
            $bg = '';
            echo "<div>\n";
            $rand = mt_rand(100, 1000);

            $button_text = empty($value) ? 'Insert image' : 'Change image';
            echo "<button class=\"zd-insert-image-button button button-default\">".__($button_text, 'ledatd')."</button>\n";

            $image_url = '';

            if( !empty($value) ) {
                echo '<button class="zd-remove-image-button button button-default">' . __('Remove image') . '</button>'."\n";
                $image_src = wp_get_attachment_image_src($value);

                $image_url = empty($image_src[0]) ? '' : $image_src[0];

                $bg = ' style="background: url('.$image_url.') no-repeat;width:'.$image_src[1].'px;height:'.$image_src[2].'px;"';
            }


            echo "<input class=\"zd-image-src-output\" id=\"zd-image-src-{$rand}\" value=\"{$image_url}\"/>\n";
            echo "<input type=\"hidden\" id=\"zd-image-id-{$rand}\" name=\"{$id}\" value=\"{$value}\" />\n";

            $hide = empty($image_url) ? ' zd-hide' : '';

            echo "<p class=\"image-preview-label{$hide}\"><strong>" . __('Image Preview', 'ledatd') . "</strong></p>\n";

            echo "<div class=\"zd-image-preview\" id=\"zd-image-preview-{$rand}\"{$bg}>";
            //            echo $value;
            echo "</div>\n";

            echo "</div>\n";
        }

        echo "</div><!--.input-group-->\n";
    }

    function zdtw_head_output() {
        $btn_background = get_option('_' . ZDTW_TEXT_DOMAIN . '_button_background_colour');
        $btn_foreground = get_option('_' . ZDTW_TEXT_DOMAIN . '_button_foreground_colour');

        $html = '';

        if( $btn_background || $btn_foreground ) {
            $html .= '<style type="text/css" media="screen">'."\n";

            if( $btn_background ) {
                $html .= '.' . ZDTW_TEXT_DOMAIN . '-nav { background-color: '.$btn_background.' !important;}'."\n";
            }

            if( $btn_foreground ) {
                $html .= '.' . ZDTW_TEXT_DOMAIN . '-next span { border-left-color: '.$btn_foreground.' !important;}'."\n";
                $html .= '.' . ZDTW_TEXT_DOMAIN . '-prev span { border-right-color: '.$btn_foreground.' !important;}'."\n";
            }

            $html .= '</style>'."\n";
        }

        echo $html;
    }

}
new ZDTestimonialsSettings();