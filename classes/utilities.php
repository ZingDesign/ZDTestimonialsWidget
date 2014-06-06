<?php
/**
 * Created by PhpStorm.
 * User: Sam
 * Date: 5/06/14
 * Time: 2:43 PM
 */

class ZDTW_Utilities {
    private static $prefix = ZDTW_TEXT_DOMAIN;

    public static function zdtw_get_option( $option_name ) {
        return get_option('_' . self::$prefix . '_' . $option_name );
    }

    public static function zdtw_update_option( $option_name, $new_value ) {
        update_option( '_' . self::$prefix . '_' . $option_name, $new_value );
    }
}