<?php
/**
 * Activation handler
 *
 * @package     WPUM\ActivationHandler
 * @since       1.0.0
 */

// Exit if accessed directly
if( !defined( 'ABSPATH' ) ) exit;

/**
 * WPUM Extension Activation Handler Class
 *
 * @since       1.0.0
 */
class WPUM_Extension_Activation {

    public $plugin_name, $plugin_path, $plugin_file, $has_wpum, $wpum_base;

    /**
     * Setup the activation class
     *
     * @access      public
     * @since       1.0.0
     * @return      void
     */
    public function __construct( $plugin_path, $plugin_file ) {
        // We need plugin.php!
        require_once( ABSPATH . 'wp-admin/includes/plugin.php' );

        $plugins = get_plugins();

        // Set plugin directory
        $plugin_path = array_filter( explode( '/', $plugin_path ) );
        $this->plugin_path = end( $plugin_path );

        // Set plugin file
        $this->plugin_file = $plugin_file;

        // Set plugin name
        if( isset( $plugins[$this->plugin_path . '/' . $this->plugin_file]['Name'] ) ) {
            $this->plugin_name = str_replace( 'WP User Manager - ', '', $plugins[$this->plugin_path . '/' . $this->plugin_file]['Name'] );
        } else {
            $this->plugin_name = __( 'This plugin', 'wpumre' );
        }

        // Is WPUM installed?
        foreach( $plugins as $plugin_path => $plugin ) {
            if( $plugin['Name'] == 'WP User Manager' ) {
                $this->has_wpum = true;
                $this->wpum_base = $plugin_path;
                break;
            }
        }
    }

    /**
     * Process plugin deactivation
     *
     * @access      public
     * @since       1.0.0
     * @return      void
     */
    public function run() {
        // Display notice
        add_action( 'admin_notices', array( $this, 'missing_wpum_notice' ) );
    }

    /**
     * Display notice if WPUM isn't installed
     *
     * @access      public
     * @since       1.0.0
     * @return      string The notice to display
     */
    public function missing_wpum_notice() {
        if( $this->has_wpum ) {
            $url  = esc_url( wp_nonce_url( admin_url( 'plugins.php?action=activate&plugin=' . $this->wpum_base ), 'activate-plugin_' . $this->wpum_base ) );
            $link = '<a href="' . $url . '">' . __( 'activate it', 'wpumre' ) . '</a>';
        } else {
            $url  = esc_url( wp_nonce_url( self_admin_url( 'update.php?action=install-plugin&plugin=wp-user-manager' ), 'install-plugin_wp-user-manager' ) );
            $link = '<a href="' . $url . '">' . __( 'install it', 'wpumre' ) . '</a>';
        }
        
        echo '<div class="error"><p>' . $this->plugin_name . sprintf( __( ' requires WP User Manager! Please %s to continue!', 'wpumre' ), $link ) . '</p></div>';
    }
}