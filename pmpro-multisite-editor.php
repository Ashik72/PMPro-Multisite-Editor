<?php
/*
Plugin Name: PMPro Multisite Editor
Plugin URI: https://ashik72.me
Description: PMPro Multisite Editor Plugin
Version: 1.0.1
Author: ashik72
Author URI: https://ashik72.me
License: GPLv2 or later
Text Domain: pmpro_mu_editor
*/

// ini_set('display_errors', 1);
// ini_set('display_startup_errors', 1);
// error_reporting(E_ALL);

if(!defined('WPINC')) // MUST have WordPress.
    exit('Do NOT access this file directly: '.basename(__FILE__));

if (!class_exists('Kint') && file_exists(dirname( __FILE__ ) . '/kint.phar'))
    include_once ( dirname( __FILE__ ) . '/kint.phar' );


if (!function_exists('d')) {

    function d($data) {

        ob_start();
        var_dump($data);
        $output = ob_get_clean();
        echo $output;
    }
}

define( 'pmpro_mu_editor_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
define( 'pmpro_mu_editor_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
if ( ! defined( 'DS' ) ) define( 'DS', DIRECTORY_SEPARATOR );


require_once( plugin_dir_path( __FILE__ ) . 'plugin_loader.php' );
