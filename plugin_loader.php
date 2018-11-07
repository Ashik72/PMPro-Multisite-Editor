<?php

if(!defined('WPINC')) // MUST have WordPress.
    exit('Do NOT access this file directly: '.basename(__FILE__));

if (file_exists( __DIR__.'/vendor/autoload.php' ))
    require __DIR__.'/vendor/autoload.php';


//require_once( plugin_dir_path( __FILE__ ) . '/inc/class-wp-list-table-custom.php' );
//
//require_once( plugin_dir_path( __FILE__ ) . '/inc/class-wp-custom-users-list-table.php' );


require_once( plugin_dir_path( __FILE__ ) . '/inc/class.pmpro_mu_editor.php' );
//require_once( plugin_dir_path( __FILE__ ) . '/inc/class.list_table.php' );


require_once( 'titan-framework-checker.php' );


add_action( 'plugins_loaded', function () {
    pmpro_mu_editor::get_instance();
   // list_table::get_instance();

    require_once( 'titan-framework-options.php' );

} );


function your_disable_activation( $user, $user_email, $key, $meta = '' ) {
// Activate the user
    $user_id = wpmu_activate_signup( $key );

    wp_redirect( /*redirect to */ site_url() );
    return false;
}
add_filter( 'wpmu_signup_user_notification', 'your_disable_activation', 10, 4 );





?>
