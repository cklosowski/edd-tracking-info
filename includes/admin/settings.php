<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function edd_ti_register_settings( $extension_sections ) {
	$extension_sections['aftership'] = __( 'Aftership', 'edd-tracking-info' );

	return $extension_sections;
}

add_filter( 'edd_settings_sections_extensions', 'edd_ti_register_settings', 10, 1 );


/**
 * Register the aftership settings
 *
 * @access public
 * @since  2.4
 * @param  $gateway_settings array
 * @return array
 */
function register_aftership_settings( $settings ) {

	$settings['aftership'] = array(
		'aftership' => array(
			'id'   => 'aftership',
			'name' => '<strong>' . __( 'Aftership Settings', 'edd-tracking-info' ) . '</strong>',
			'type' => 'header',
		),
		'aftership_api_key' => array(
			'id'   => 'aftership_api_key',
			'name' => __( 'Aftership API Key', 'edd-tracking-info' ),
			'desc' => sprintf( __( 'Found in your <a href="%s">Aftership Account</a>', 'edd-tracking-info' ), 'https://www.aftership.com/apps/api' ),
			'type' => 'text',
			'size' => 'regular',
		),
	);

	return $settings;

}
add_filter( 'edd_settings_extensions', 'register_aftership_settings', 10, 1 );
