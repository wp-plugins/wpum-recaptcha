<?php
/**
 * Register scripts
 *
 * @package     wpum-recaptcha
 * @copyright   Copyright (c) 2015, Alessandro Tesoro
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       1.0.0
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Register scripts required for recaptcha
 *
 * @since 1.0.0
 * @return void
 */
function wpumre_scripts() {

	// Define lib url
	$api_url = 'https://www.google.com/recaptcha/api.js';

	// Adjust library url with language string
	if( defined( 'WPUMRE_LANGUAGE' ) ) {
		$api_url = 'https://www.google.com/recaptcha/api.js?hl=' . WPUMRE_LANGUAGE;
	}

	// Register the script
	wp_register_script( 'wpum-recaptcha-lib', $api_url, null, WPUMRE_VERSION, true );

	// Enqueue the script
	wp_enqueue_script( 'wpum-recaptcha-lib' );

}