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
        'desc' => 'How many free contributors an admin may add?',
        'default' => '1'
    ) );


    $tab->createOption( array(
        'name' => 'Free membership level id:',
        'id' => 'free_level_id',
        'type' => 'text',
        'desc' => 'A level is required to let general admin buy access for additional contributors',
        'default' => '1'
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


 ?>
