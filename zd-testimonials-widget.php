<?php

/*
Plugin Name: Zing Design Testimonial Widget
Plugin URI: http://www.zingdesign.com
Description: This widget allows Administrators to manually add a testimonial to their site
Author: Samuel Holt
Version: 1.0
Author URI: http://www.zingdesign.com
*/

define( 'ZDTW_TEXT_DOMAIN', 'zdtw' );

require_once( plugin_dir_path( __FILE__ ) . 'classes/utilities.php');
require_once( plugin_dir_path( __FILE__ ) . 'classes/widget.php');
require_once( plugin_dir_path( __FILE__ ) . 'classes/settings.php');

//Add action hooks
add_action( 'widgets_init', 'register_testimonial_widget' );

add_action( 'init', 'zdtw_init' );

function register_testimonial_widget() {
    register_widget( 'Testimonial_Widget' );
}

function zdtw_init() {
}
