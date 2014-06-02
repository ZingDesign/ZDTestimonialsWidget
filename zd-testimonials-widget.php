<?php

/*
Plugin Name: Zing Design Testimonial Widget
Plugin URI: http://www.zingdesign.com
Description: This widget allows Administrators to manually add a testimonial to their site
Author: Samuel Holt
Version: 1.0
Author URI: http://www.zingdesign.com
*/

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

function register_testimonial_widget() {
    register_widget( 'Testimonial_Widget' );
}

class Testimonial_Widget extends WP_Widget {

    private $widget_id;
    private $client_name;
    private $version;
    private $is_widget;

    public function __construct() {
        $this -> widget_id = 'ledatw';
        $this -> client_name = 'Leda';
        $this -> version = '0.1.0';
        $this -> is_widget = true;

        $upload_dir = wp_upload_dir();
        $this->uploads_dir_url = $upload_dir['baseurl'];

        load_plugin_textdomain($this->widget_id, false, basename( dirname( __FILE__ ) ) . '/languages' );
        
        parent::__construct(
            $this->widget_id . '_testimonial_widget', // Base ID
            $this->client_name . ' Testimonial', // Name
            array( 'description' => __( $this->client_name . ' Testimonial Widget', 'ledatw' ), ), //Widget Ops
            array( 'width' => 400 ) //Control Ops
        );

        add_action('admin_enqueue_scripts', array($this, 'load_admin_assets') );
        add_action('wp_enqueue_scripts', array($this, 'load_client_assets') );
    }

    public function form( $instance ) {

        $html = '';

        $options = array(
            array(
                'label' => 'Avatar',
                'type' => 'image'
            ),
            array(
                'label' => 'Testimonial',
                'type' => 'textarea'
            ),
            array(
                'label' => 'First name'
            ),
            array(
                'label' => 'Last name'
            ),
            array(
                'label' => 'Job title'
            ),
            array(
                'label' => 'Business name'
            ),
            array(
                'label' => 'Location'
            ),
        );

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

        foreach( $new_instance as $key => $val ) {
            $instance[$key] = strip_tags( $val );
        }
        
        return $instance;
    }

    public function widget( $args, $instance ) {

        $first_name = $last_name = $avatar = $testimonial = $job_title = $business_name = $location = "";

        $wid = $this->widget_id;
        // outputs the content of the widget
//        $defaults = array();

//        $args = wp_parse_args( $args, $defaults );

        extract( $instance );

        $full_name = $first_name . ' ' . $last_name;
        $image_data = wp_get_attachment_image_src( $avatar );
        $image_src = $image_data[0];
        $image_width = $image_data[1];
        $image_height = $image_data[2];

        $html = '';

        $html .= "<div class=\"{$wid}-testimonial\">\n";
//        $html .= "<h1>Testimonial</h1>\n";
        $html .= "<div class=\"{$wid}-avatar-image\">\n";
        $html .= "<img src=\"{$image_src}\" alt=\"{$full_name}'s avatar.\" width=\"{$image_width}\" height=\"{$image_height}\" />\n";
        $html .= "</div><!--avatar-image-->\n";

        $html .= "<div class=\"testimonial\">\n";
        $html .= wpautop( $testimonial );
        $html .= "</div>\n";

        $html .= "</div><!--testimonial-->\n";

        $html .= "<div class=\"testimonial-meta\">\n";
        $html .= "<span>{$full_name} | {$job_title}, {$business_name}</span><span class=\"location\">{$location}</span>\n";
        $html .= "</div><!--testimonial-meta-->\n";

        echo $html;

    }

    function load_admin_assets() {
        $wid = $this->widget_id;

        wp_enqueue_style( $wid . '-admin-stylesheet', plugin_dir_url(__FILE__) . '/css/' . $wid . '-admin.css' );

        wp_enqueue_media();
        wp_enqueue_script( $wid . '-admin-script', plugin_dir_url( __FILE__ ) . 'js/' . $wid . '-admin.js', array(), $this->version, true);
    }

    function load_client_assets() {

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

        if( $is_text_field || "textarea" === $type ) {
            $wrapper = 'p';
            $wrapper_class = $this->widget_id . '-input-group';
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

            $src = $image_src[0];
            $short_src = str_replace( $this->uploads_dir_url, '', $src );

            $bg = ( strlen($src) > 0 ) ? ' style="background: url(' . $src . ') no-repeat; width: ' . $image_src[1] . 'px; height: ' . $image_src[2] . 'px;"' : "";

            $input .= "<div class=\"image-group\">\n";

            $input .= "<input type=\"text\" id=\"zd-image-src-{$random}\" name=\"{$_name}[src]\" value=\"{$short_src}\" />\n";
            $input .= "<input type=\"hidden\" id=\"zd-image-id-{$random}\" name=\"{$_name}\" value=\"{$value}\" />\n";

            $input .= "<button class=\"zd-insert-image-button button button-primary\">{$button_text}</button>\n";

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

//Add action hooks
add_action( 'widgets_init', 'register_testimonial_widget' );