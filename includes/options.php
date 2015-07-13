<?php
/**
 * Register new options
 *
 * @package     wpum-recaptcha
 * @copyright   Copyright (c) 2015, Alessandro Tesoro
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       1.0.0
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Register new options
 *
 * @since 1.0.0
 * @param array $settings list of registered settings
 * @return array $settings array containing existing settings with new settings
 */
function wpumre_settings( $settings ) {

	$new_settings = array(
		array(
		    'id'    => 'wpumre_settings',
		    'name'  => '<strong>' . __( 'reCAPTCHA Settings', 'wpumre' ) . '</strong>',
		    'type'  => 'header',
		),
		array(
			'id'   => 'recaptcha_site_key',
			'name' => __( 'Site Key', 'wpumre' ),
			'desc' => __( 'Enter your site key.', 'wpumre' ) . ' ' . sprintf( __( 'Get your reCAPTCHA keys from <a href="%s" target="_blank">Google</a>', 'wpumre' ), 'https://www.google.com/recaptcha/' ),
			'type' => 'text',
		),
		array(
			'id'   => 'recaptcha_secret_key',
			'name' => __( 'Secret Key', 'wpumre' ),
			'desc' => __( 'Enter your site secret key.', 'wpumre' ) . ' ' . sprintf( __( 'Get your reCAPTCHA keys from <a href="%s" target="_blank">Google</a>', 'wpumre' ), 'https://www.google.com/recaptcha/' ),
			'type' => 'text',
		),
		array(
			'id'          => 'recaptcha_location',
			'name'        => __( 'Display location:', 'wpumre' ),
			'desc'        => __('Select in which forms you wish to display the recaptcha field.', 'wpumre'),
			'type'        => 'multiselect',
			'placeholder' => __('Select one or more forms from the list.', 'wpumre'),
			'class'       => 'select2',
			'options'     => array(
				'registration'      => __( 'Registration', 'wpumre' ),
				'password_recovery' => __( 'Password recovery', 'wpumre' ),
				'login'             => __( 'Login', 'wpumre' )
			)
		),
	);

	return array_merge( $settings, $new_settings );

}