<?php

if(!defined('WPINC')) // MUST have WordPress.
    exit('Do NOT access this file directly: '.basename(__FILE__));

if (file_exists( __DIR__.'/vendor/autoload.php' ))
    require __DIR__.'/vendor/autoload.php';

require_once( plugin_dir_path( __FILE__ ) . '/inc/class.pmpro_mu_editor.php' );

require_once( 'titan-framework-checker.php' );
require_once( 'titan-framework-options.php' );


add_action( 'plugins_loaded', function () {
    pmpro_mu_editor::get_instance();
} );

?>
