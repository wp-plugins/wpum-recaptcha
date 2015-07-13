<?php
/**
 * Plugin Name: WPUM - reCAPTCHA
 * Plugin URI:  http://wpusermanager.com
 * Description: Stop spam registrations on your website for free. This is an extension for the WP User Manager plugin.
 * Version:     1.0.0
 * Author:      Alessandro Tesoro
 * Author URI:  http://wpusermanager.com
 * License:     GPLv2+
 * Text Domain: wpumre
 * Domain Path: /languages
 */

/**
 * Copyright (c) 2015 Alessandro Tesoro
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License, version 2 or, at
 * your discretion, any later version, as published by the Free
 * Software Foundation.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

if ( ! class_exists( 'WPUM_reCAPTCHA' ) ) :

/**
 * Main WPUM_reCAPTCHA Class
 *
 * @since 1.0.0
 */
class WPUM_reCAPTCHA {

	/**
	 * @var   $instance of the addon
	 * @since 1.0.0
	 */
	private static $instance;

	/**
	 * @var   $where Determine where recaptcha should be displayed
	 * @since 1.0.0
	 */
	public static $where;

	/**
	 * Get active instance
	 *
	 * @access      public
	 * @since       1.0.0
	 * @return      object self::$instance The one true WPUM_reCAPTCHA
	 */
	public static function instance() {
	    if( !self::$instance ) {
	        self::$instance = new WPUM_reCAPTCHA();
	        self::$instance->setup_constants();
	        self::$instance->includes();
	        self::$instance->load_textdomain();
	        self::$where = wpum_get_option( 'recaptcha_location' );
	        self::$instance->hooks();
	    }
	    return self::$instance;
	}

	/**
	 * Setup plugin constants
	 *
	 * @access private
	 * @since 1.0.0
	 * @return void
	 */
	private function setup_constants() {

		// Plugin version
		if ( ! defined( 'WPUMRE_VERSION' ) ) {
			define( 'WPUMRE_VERSION', '1.0.0' );
		}

		// Plugin Folder Path
		if ( ! defined( 'WPUMRE_PLUGIN_DIR' ) ) {
			define( 'WPUMRE_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
		}

		// Plugin Folder URL
		if ( ! defined( 'WPUMRE_PLUGIN_URL' ) ) {
			define( 'WPUMRE_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
		}

		// Plugin Root File
		if ( ! defined( 'WPUMRE_PLUGIN_FILE' ) ) {
			define( 'WPUMRE_PLUGIN_FILE', __FILE__ );
		}

		// Plugin Slug
		if ( ! defined( 'WPUMRE_SLUG' ) ) {
			define( 'WPUMRE_SLUG', plugin_basename( __FILE__ ) );
		}

	}

	/**
	 * Include required files
	 *
	 * @access private
	 * @since 1.0.0
	 * @return void
	 */
	private function includes() {

		require_once WPUMRE_PLUGIN_DIR . 'includes/options.php';
		require_once WPUMRE_PLUGIN_DIR . 'includes/scripts.php';
		require_once WPUMRE_PLUGIN_DIR . 'includes/markup.php';
		require_once WPUMRE_PLUGIN_DIR . 'includes/validation.php';

	}

	/**
	 * Run the plugin hooks
	 *
	 * @access private
	 * @since 1.0.0
	 * @return void
	 */
	private function hooks() {

		// Register settings
		add_filter( 'wpum_settings_extensions', 'wpumre_settings', 1 );

		// Enqueue scripts
		add_action( 'wp_enqueue_scripts', 'wpumre_scripts' );

		// Add recaptcha to registration form if enabled
		if( is_array( self::$where ) && in_array( 'registration' , self::$where ) ) {
			// Add recaptcha markup to registration form
			add_action( 'wpum_after_inside_register_form_template', 'wpumre_add_markup' );
			// Validate registration process
			add_filter( 'wpum/form/validate=register', 'wpum_recaptcha_validation', 10, 3 );
		}

		// Add recaptcha to password recovery form if enabled
		if( is_array( self::$where ) && in_array( 'password_recovery' , self::$where ) ) {
			// Add recaptcha markup to password form
			add_action( 'wpum_after_inside_password_form_template', 'wpumre_add_markup' );
			// Validate password recovery process
			add_filter( 'wpum/form/validate=password', 'wpum_recaptcha_validation', 10, 3 );
		}

		// Add recaptcha to login form if enabled
		if( is_array( self::$where ) && in_array( 'login' , self::$where ) ) {

			// Add markup to the form
			add_action( 'login_form_middle', 'wpumre_add_markup' );

			// Validate recaptcha in login form
			add_action( 'authenticate', 'wpum_recaptcha_login_validation' );

			// Display new error message for recaptcha validation
			add_action( 'wpum_before_login_form', 'wpum_recaptcha_error_message' );

		}

	}

	/**
	 * Load the language files for translation
	 *
	 * @access public
	 * @since 1.0.0
	 * @return void
	 */
	public function load_textdomain() {
		
		// Set filter for plugin's languages directory
		$wpumre_lang_dir = dirname( plugin_basename( WPUMRE_PLUGIN_FILE ) ) . '/languages/';
		$wpumre_lang_dir = apply_filters( 'wpumre_languages_directory', $wpumre_lang_dir );

		// Traditional WordPress plugin locale filter
		$locale = apply_filters( 'plugin_locale',  get_locale(), 'wpumre' );
		$mofile = sprintf( '%1$s-%2$s.mo', 'wpumre', $locale );

		// Setup paths to current locale file
		$mofile_local  = $wpumre_lang_dir . $mofile;
		$mofile_global = WP_LANG_DIR . '/wpumre/' . $mofile;

		if ( file_exists( $mofile_global ) ) {
			// Look in global /wp-content/languages/wpumre folder
			load_textdomain( 'wpumre', $mofile_global );
		} elseif ( file_exists( $mofile_local ) ) {
			// Look in local /wp-content/plugins/wpum-recaptcha/languages/ folder
			load_textdomain( 'wpumre', $mofile_local );
		} else {
			// Load the default language files
			load_plugin_textdomain( 'wpumre', false, $wpumre_lang_dir );
		}

	}

}

endif;

/**
 * The main function responsible for returning the one true WPUM_reCAPTCHA
 * instance to functions everywhere
 */
function WPUM_reCAPTCHA_load() {
    if( ! class_exists( 'WP_User_Manager' ) ) {
        if( ! class_exists( 'WPUM_Extension_Activation' ) ) {
            require_once 'includes/class.extension-activation.php';
        }
        $activation = new WPUM_Extension_Activation( plugin_dir_path( __FILE__ ), basename( __FILE__ ) );
        $activation = $activation->run();
    } else {
        return WPUM_reCAPTCHA::instance();
    }
}
add_action( 'plugins_loaded', 'WPUM_reCAPTCHA_load' );