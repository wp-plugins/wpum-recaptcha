<?php
/**
 * Validates the captcha
 *
 * @package     wpum-recaptcha
 * @copyright   Copyright (c) 2015, Alessandro Tesoro
 * @license     http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since       1.0.0
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Validate recaptcha submission
 *
 * @since 1.0.0
 * @return void
 */
function wpum_recaptcha_validation( $passed, $fields, $values ) {

	// Define the error message
	$message = '';

  // Define the google's api url
  $url = 'https://www.google.com/recaptcha/api/siteverify';

  // Send form response to google's api
  $response = wp_remote_post( $url, array(
  	'method' => 'POST',
  		'body' => array( 
  			'secret'   => wpum_get_option( 'recaptcha_secret_key' ),
  			'response' => $_POST['g-recaptcha-response'],
  			'remoteip' => $_SERVER['REMOTE_ADDR']
  		),
	   )
  );

  // Retrieve body of the response
  $api_response = json_decode( wp_remote_retrieve_body( $response ), true );

  // Display error within the form
  if( ! $api_response['success'] ) {

  	foreach ( $api_response['error-codes'] as $error ) {

  		switch ( $error ) {
  			case 'missing-input-response':
  				$message = __( 'Captcha validation failed.', 'wpumre' );
  				break;
  			case 'missing-input-secret':
  				$message = __( 'Secret key missing.', 'wpumre' );
  				break;
  			case 'invalid-input-secret':
  				$message = __( 'Your secret key is invalid.', 'wpumre' );
  				break;
  			case 'invalid-input-response':
  				$message = __( 'Invalid response.', 'wpumre' );
  				break;
  			default:
  				return new WP_Error( 'recaptcha-error', __( 'Something went wrong during validation of the captcha.', 'wpumre' ) );
  				break;
  		}

  		return new WP_Error( 'recaptcha-error', $message );

  	}
  		
  }

	return $passed;

}

/**
 * Validate recaptcha submission on login form
 *
 * @since 1.0.0
 * @return void
 */
function wpum_recaptcha_login_validation( $user ) {

    if ( !defined( 'DOING_AJAX' ) && isset( $_SERVER['HTTP_REFERER'] ) && isset( $_POST['log'] ) && isset( $_POST['pwd'] ) ) :
      
      // check what page the login attempt is coming from
      $referrer = $_SERVER['HTTP_REFERER'];

      // Verify we're not into the wp-login page
      if ( ! empty( $referrer ) && ! strstr( $referrer, 'wp-login' ) && ! strstr( $referrer, 'wp-admin' ) ) {

          // Define the google's api url
          $url = 'https://www.google.com/recaptcha/api/siteverify';

          // Send form response to google's api
          $response = wp_remote_post( $url, array(
            'method' => 'POST',
              'body' => array( 
                'secret'   => wpum_get_option( 'recaptcha_secret_key' ),
                'response' => $_POST['g-recaptcha-response'],
                'remoteip' => $_SERVER['REMOTE_ADDR']
              ),
             )
          );

          // Retrieve body of the response
          $api_response = json_decode( wp_remote_retrieve_body( $response ), true );

          // Display error within the form
          if( ! $api_response['success'] ) {

              $url = add_query_arg( array(
                'login' => false,
                'captcha' => 'failed_captcha',
              ), $referrer );

              wp_redirect( $url );
              exit;

          }

      }

    endif;

}