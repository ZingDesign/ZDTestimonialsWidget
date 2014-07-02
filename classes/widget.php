<?php

/**
 * Created by PhpStorm.
 * User: Sam
 * Date: 23/05/14
 * Time: 10:50 AM
 *
 * Fields:
 * Avatar
 * Testimonial
 * First name
 * Last name
 * Job title
 * Business name
 * Location
 *
 */

class Testimonial_Widget extends WP_Widget {

    private $widget_id;
    private $client_name;
    private $version;
    private $is_widget;

    public function __construct() {
        $prefix = ZDTW_TEXT_DOMAIN;
        $this -> widget_id = $prefix;
        $this -> version = '0.1.0';
        $this -> is_widget = true;

        $client_name = get_option( '_' . $prefix . '_client_name');

        $widget_name = ( strlen($client_name) > 0 ) ? $client_name . ' Testimonial' : 'Testimonial';
        $widget_desc = 'Zing Design\'s testimonial widget';

        $upload_dir = wp_upload_dir();
        $this->uploads_dir_url = $upload_dir['baseurl'];

        load_plugin_textdomain($this->widget_id, false, basename( dirname( __FILE__ ) ) . '/languages' );

        add_action('admin_enqueue_scripts', array($this, 'load_admin_assets') );
        add_action('wp_enqueue_scripts', array($this, 'load_client_assets') );

        parent::__construct(
            $this->widget_id . '_testimonial_widget', // Base ID
            $widget_name, // Name
            array( 'description' => __( $widget_desc, $this -> widget_id ), ), //Widget Ops
            array( 'width' => 410 ) //Control Ops
        );
    }

    public function form( $instance ) {

        $html = '';
        $option_prefix = '_' . $this->widget_id . '_';

//        $admin_options = get_option($option_prefix . 'admin_options');

        $options = array();

//        $options = array(
//            array(
//                'label' => 'Avatar',
//                'type' => 'image'
//            ),
//            array(
//                'label' => 'Testimonial',
//                'type' => 'textarea'
//            ),
//            array(
//                'label' => 'First name'
//            ),
//            array(
//                'label' => 'Last name'
//            ),
//            array(
//                'label' => 'Job title'
//            ),
//            array(
//                'label' => 'Business name'
//            ),
//            array(
//                'label' => 'Location'
//            ),
//        );

        if( get_option($option_prefix . 'show_avatar' )) {
            $options[] = array(
                'label' => 'Avatar',
                'type' => 'image');
        }
        if( get_option($option_prefix . 'show_testimonial') ) {
            $options[] = array(
                'label' => 'Testimonial',
                'type' => 'textarea');
        }
        if( get_option($option_prefix . 'show_first_name') ) {
            $options[] = array( 'label' => 'First name' );
        }
        if( get_option($option_prefix . 'show_last_name') ) {
            $options[] = array( 'label' => 'Last name' );
        }
        if( get_option($option_prefix . 'show_job_title') ) {
            $options[] = array( 'label' => 'Job title' );
        }
        if( get_option($option_prefix . 'show_business_name') ) {
            $options[] = array( 'label' => 'Business name' );
        }
        if( get_option($option_prefix . 'show_location') ) {
            $options[] = array( 'label' => 'Location' );
        }


        foreach( $options as $option ) {

            $option_slug = $this->slugify($option['label'], "_");

            $option['value'] = isset($instance[$option_slug]) ? $instance[$option_slug] : '';

            $html .= $this->form_field( $option );
        }

        echo $html;
    }

    public function update( $new_instance, $old_instance ) {
        // processes widget options to be saved
        $instance = array();

        //        echo "<pre>";
        //        print_r($new_instance);
        //        echo "</pre>";

        //        $instance['avatar'] = $new_instance['avatar'];

        $instance['title'] = $new_instance['first_name'] . ' ' . $new_instance['last_name'];

        foreach( $new_instance as $key => $val ) {
            $instance[$key] = strip_tags( $val );
        }

        return $instance;
    }

    public function widget( $args, $instance ) {

        $first_name = $last_name = $avatar = $testimonial = $job_title = $business_name = $location = "";

        $wid = $this->widget_id;
        // outputs the content of the widget

        extract( $instance );
        extract( $args );

        $full_name = $first_name . ' ' . $last_name;
        $image_data = wp_get_attachment_image_src( $avatar );
        $image_src = $image_data[0];
        $image_width = $image_data[1];
        $image_height = $image_data[2];

        $avatar_html = $html = $before = $after = '';

        $show_avatar_in_testimonial = get_option('_' . $wid . '_show_avatar_inside_testimonial_box');
        $show_multiple = get_option( '_' . $wid . '_show_multiple_testimonials_in_side_bar') ? ' show-multiple' : '';

        if( !$show_multiple )  {
            $before = $before_widget;
            $after = $after_widget;
        }

        if( $image_data ) {
            $avatar_html .= "<div class=\"{$wid}-avatar-image\">\n";
            $avatar_html .= "<img src=\"{$image_src}\" alt=\"{$full_name}'s avatar.\" width=\"{$image_width}\" height=\"{$image_height}\" />\n";
            $avatar_html .= "</div><!--avatar-image-->\n";
        }

        $html .= $before;

        $html .= "<div class=\"{$wid}-testimonial{$show_multiple}\">\n";
        //        $html .= "<h1>Testimonial</h1>\n";

        if( !$show_avatar_in_testimonial ) {
            $html .= $avatar_html;
        }

        $custom_testimonial_class = ZDTW_Utilities::zdtw_get_option('testimonial_class_name');
        $show_hr = ZDTW_Utilities::zdtw_get_option('show_horizontal_rule') ? '<hr/>' : '';

        $html .= "<div class=\"testimonial {$custom_testimonial_class}\">\n";

        if( $show_avatar_in_testimonial ) {
            $html .= $avatar_html;
        }

        $html .= wpautop( $testimonial );
        $html .= "</div><!--.testimonial-->\n";

        $html .= $show_hr;

        $html .= "<div class=\"testimonial-meta\">\n";

        $html .= "<p>";
        if( $full_name ) {
            $html .= $full_name;
        }
        if( $job_title ) {
            $html .= " | {$job_title}";
        }
        if( $business_name ) {
            $html .= ", {$business_name}";
        }
        $html .= "</p>";

        if($location) {
            $html .= "<span class=\"location\">{$location}</span>\n";
        }

        $html .= "</div><!--testimonial-meta-->\n";
        $html .= "</div><!--{$wid}-testimonial-->\n";

        $html .= $after;

        echo $html;

    }

    function load_admin_assets() {
        $wid = $this->widget_id;

        wp_enqueue_style( $wid . '-admin-stylesheet', plugins_url( '/css/'.$wid.'-admin.css', dirname(__FILE__)) );

        wp_enqueue_media();
        wp_enqueue_script( $wid . '-admin-script', plugins_url( 'js/'.$wid.'-admin.js', dirname(__FILE__)), array(), $this->version, true);
    }

    function load_client_assets() {
        $wid = $this->widget_id;

        wp_enqueue_style( $wid . '-client-stylesheet', plugins_url( '/css/'.$wid.'-client.css', dirname(__FILE__)) );

        wp_enqueue_script( $wid . '-client-script', plugins_url( 'js/'.$wid.'-client.js', dirname(__FILE__)), array(), $this->version, true);
    }

    function form_field( $args ) {

        $input = $type = $id = $name = $value = $before = $after = "";

        $placeholder = false;

        $defaults = array(
            'type' => 'text'
        );

        $args = wp_parse_args( $args, $defaults );

        extract( $args );

        if( ! $args['label'] ) {
            return "<span class=\"error\">Error: label key is required</span>";
        }

        $label = $args['label'];

        // If id/name is undefined, generate name from label
        $_id = ($id === "") ? $this->slugify($label) : $id;
        $_name = ($name === "") ? str_replace("-", "_", $_id) : $name;

        //        if( ! $placeholder ) {
        //            $placeholder = $label;
        //        }

        if( $this->is_widget ) {
            $_id = $this->get_field_id($_id);
            $_name = $this->get_field_name($_name);
        }


        $is_text_field = in_array( $type, array( "text", "email", "password", "hidden" ) );

        if( $is_text_field || "textarea" === $type) {
            $wrapper = 'p';
            $wrapper_class = 'zd-input-group';
        }

        if( isset( $wrapper ) ) {

            $after = '</' . $wrapper . '>' . "\n";

            if( isset( $wrapper_class ) ) {
                $wrapper .= ' class="' . $wrapper_class . '"';
            }

            $before = '<' . $wrapper . '>' . "\n";
        }

        $input .= $before;

        $input .= "<label for=\"{$_id}\">{$label}</label>\n";

        //        var_dump( $type );

        if( $is_text_field ) {
            $input .= "<input type=\"{$type}\" id=\"{$_id}\" name=\"{$_name}\" value=\"{$value}\" placeholder=\"{$placeholder}\" />\n";
        }
        else if( "textarea" === $type ) {
            $input .= "<textarea id=\"{$_id}\" name=\"{$_name}\" placeholder=\"{$placeholder}\">{$value}</textarea>\n";
        }
        else if( "image" === $type ) {
            $random = mt_rand(100, 10000);

            $button_text = empty($value) ? __("Insert image", $this->widget_id) : __("Update image", $this->widget_id);

            $image_src = empty($value) ? array('') : wp_get_attachment_image_src($value);

            $hide_remove_button = empty($value) ? ' hidden' : '';

            $src = $image_src[0];
            $short_src = str_replace( $this->uploads_dir_url, '', $src );

            $bg = ( strlen($src) > 0 ) ? ' style="background: url(' . $src . ') no-repeat; width: auto; height: 100px; background-size:contain;"' : "";

            $input .= "<div class=\"image-group\">\n";

            $input .= "<input type=\"text\" id=\"zd-image-src-{$random}\" name=\"{$_name}[src]\" value=\"{$short_src}\" />\n";
            $input .= "<input type=\"hidden\" id=\"zd-image-id-{$random}\" name=\"{$_name}\" value=\"{$value}\" />\n";

            $input .= "<button class=\"zd-insert-image-button button button-primary\">{$button_text}</button>\n";

            $input .= "<button class=\"zd-remove-image-button button button-secondary{$hide_remove_button}\">Remove image</button>\n";

            $input .= "<p class=\"image-preview-label\"><strong>".__("Image Preview", $this->widget_id)."</strong></p>\n";

            $input .= "<div class=\"zd-image-preview\" id=\"zd-image-preview-{$random}\"{$bg}></div><!--image-preview-->\n";

            $input .= "</div><!--image-group-->\n";
        }

        $input .= $after;

        return $input;

    }

    function slugify( $str, $spacer="-" ){
        $str = str_replace(' ', $spacer, trim( strtolower( $str ) ) );

        return preg_replace("/[^A-Za-z0-9$spacer ]/", '', $str);
    }
}