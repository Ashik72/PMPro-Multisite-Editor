<?php

if (!defined('ABSPATH'))
  exit;


add_action( 'tf_create_options', 'pmpro_mu_editor_options', 150 );

function pmpro_mu_editor_options() {

    if (!is_super_admin()) return;

    if (!is_main_site()) return;
    return;
	$titan = TitanFramework::getInstance( 'pmpro_mu_editor' );

	$section = $titan->createAdminPanel( array(
		    'name' => __( 'PMPro Multisite Editor Options', 'bp_rtc' ),
		    'icon'	=> 'dashicons-networking'
		) );


    $tab = $section->createTab( array(
        'name' => 'General Options'
    ) );

    $tab->createOption( array(
        'name' => 'Page ID',
        'id' => 'mu_editor_page_id',
        'type' => 'text',
        'desc' => 'Enter page ID where you are selling your membership. Note, use Paid Memberships Pro - Addon Packages along with PMPro.',
        'default' => ''
    ) );
    $tab->createOption( array(
        'name' => 'Profile Images',
        'id' => 'bimber_profile_images',
        'type' => 'textarea',
        'desc' => 'One image link per line'
    ) );


    $section->createOption( array(
  			  'type' => 'save',
		) );


}


 ?>
