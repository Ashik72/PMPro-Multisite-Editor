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
        'name' => 'Buy page link:',
        'id' => 'buy_page_link',
        'type' => 'text',
        'desc' => 'Page to redirect when no credit available',
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

    global $wpdb;
    $args = [
        'role'           => 'Editor'
    ];
    $editors = get_users($args);
    $super_options = pmpro_mu_editor::getEditorOptions();
    $paid_level_id = $super_options['paid_level_id'];
    $paid_level_id = intval($paid_level_id);

    $get_level_data = $wpdb->get_row("SELECT * FROM `{$wpdb->base_prefix}pmpro_membership_levels` WHERE id = {$paid_level_id}");


    //$pmpro_options = $wpdb->get_results("SELECT * FROM `{$wpdb->base_prefix}options` WHERE option_name LIKE \"%pmpro%\"");

    $pmpro_currency = $wpdb->get_var("SELECT option_value FROM `{$wpdb->base_prefix}options` WHERE option_name = 'pmpro_currency'");


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
        'custom' => get_user_meta(get_current_user_id(), 'set_editor_quantity', true)
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
        'name' => 'Estimated Bill:',
        'type' => 'custom',
        'custom' => "On Development"
    ) );

}


 ?>
