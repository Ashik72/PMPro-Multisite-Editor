<?php

if (!defined('ABSPATH'))
  exit;


add_action( 'tf_create_options', 'pmpro_mu_editor_options', 150 );

function pmpro_mu_editor_options() {

    if (!is_super_admin()) return;

    if (!is_main_site()) return;

	$titan = TitanFramework::getInstance( 'pmpro_mu_editor' );

	$section = $titan->createAdminPanel( array(
		    'name' => __( 'PMPro Multisite Editor Options', 'bp_rtc' ),
		    'icon'	=> 'dashicons-networking'
		) );


    $tab = $section->createTab( array(
        'name' => 'General Options'
    ) );

    $tab->createOption( array(
        'name' => 'General Administrator Can Not Add These User Roles:',
        'id' => 'protected_user_roles',
        'type' => 'textarea',
        'desc' => 'One role per line'
    ) );

    $tab->createOption( array(
        'name' => 'Free credits for first time:',
        'id' => 'free_credits',
        'type' => 'text',
        'desc' => 'How many free editors an admin may add?',
        'default' => '1'
    ) );


    $tab->createOption( array(
        'name' => 'Free membership level id:',
        'id' => 'free_level_id',
        'type' => 'text',
        'desc' => 'A level is required to let general admin buy access for additional editors',
        'default' => '1'
    ) );


    $tab->createOption( array(
        'name' => 'Level ID For Premium Access:',
        'id' => 'paid_level_id',
        'type' => 'text',
        'desc' => 'Level ID that should be charged for paid editors. Payment will be based on active subscription.',
        'default' => '2'
    ) );

    $tab->createOption( array(
        'name' => 'Checkout Page Link:',
        'id' => 'checkout_page_link',
        'type' => 'text',
        'desc' => 'Checkout Page Link for iframe',
        'default' => ''
    ) );

    $tab->createOption( array(
        'name' => 'Checkout Page ID:',
        'id' => 'checkout_page_id',
        'type' => 'text',
        'desc' => 'Checkout Page ID for iframe',
        'default' => ''
    ) );


    $section->createOption( array(
  			  'type' => 'save',
		) );


}

add_action( 'tf_create_options', 'pmpro_mu_editor_options_site_admin', 200 );


function pmpro_mu_editor_options_site_admin()
{

    if (is_super_admin()) return;
    if (!is_admin()) return;
    global $wpdb, $current_user;
    $dir = plugin_dir_path( __DIR__ );

    if (!file_exists($dir."paid-memberships-pro".DIRECTORY_SEPARATOR."paid-memberships-pro.php"))
        wp_die('PMPro does not exists!');

    $pmpro_dir = $dir."paid-memberships-pro";


    require_once($pmpro_dir . "/includes/countries.php");
    require_once($pmpro_dir . "/includes/states.php");
    require_once($pmpro_dir . "/includes/currencies.php");

    include_once $dir."paid-memberships-pro".DIRECTORY_SEPARATOR."paid-memberships-pro.php";

    pmpro_mu_editor::changePMProTables();

    //$current_user = new WP_User(get_current_user_id(), '', 1);
    //require_once($pmpro_dir . "/preheaders/levels.php");
    //require_once($pmpro_dir . "/preheaders/checkout.php");



    //

    $args = [
        'role'           => 'Editor'
    ];
    $editors = get_users($args);
    $super_options = pmpro_mu_editor::getEditorOptions();
    $paid_level_id = $super_options['paid_level_id'];
    $paid_level_id = intval($paid_level_id);

    $get_level_data = $wpdb->get_row("SELECT * FROM `{$wpdb->base_prefix}pmpro_membership_levels` WHERE id = {$paid_level_id}");


    $pmpro_currency = $wpdb->get_var("SELECT option_value FROM `{$wpdb->base_prefix}options` WHERE option_name = 'pmpro_currency'");


    $_REQUEST['level'] = $paid_level_id;

    $titan = TitanFramework::getInstance( 'pmpro_mu_editor_gadmin' );

    $section = $titan->createAdminPanel( array(
        'name' => __( 'Subscription', 'bp_rtc' ),
        'icon'	=> 'dashicons-networking'
    ) );

    $tab = $section->createTab( array(
        'name' => 'Active Subscription'
    ) );

    $tab->createOption( array(
        'name' => 'Current Balance:',
        'type' => 'custom',
        'custom' => empty(get_user_meta(get_current_user_id(), 'set_editor_quantity', true)) ? 0 : get_user_meta(get_current_user_id(), 'set_editor_quantity', true)
    ) );

    $tab->createOption( array(
        'name' => 'Users:',
        'type' => 'custom',
        'custom' => count($editors)
    ) );

    $tab->createOption( array(
        'name' => 'Currency:',
        'type' => 'custom',
        'custom' => $pmpro_currency
    ) );

    $tab->createOption( array(
        'name' => 'Cost:',
        'type' => 'custom',
        'custom' => $get_level_data->billing_amount." {$pmpro_currency} per user"
    ) );


    $tab->createOption( array(
        'name' => 'Estimated Bill:',
        'type' => 'custom',
        'custom' => ($get_level_data->billing_amount * count($editors))." {$pmpro_currency}"
    ) );

    $tab = $section->createTab( array(
        'name' => 'Purchase Access'
    ) );


    $tab->createOption( array(
        'name' => 'Number of Editors:',
        'type' => 'custom',
        'class' => 'neditors',
        'custom' => pmpro_mu_editor::pmpro_admin_checkout()
    ) );

}


 ?>
