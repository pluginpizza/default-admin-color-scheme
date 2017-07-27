<?php
/**
 * Main plugin file.
 *
 * @package   Default_Admin_Color_Scheme
 * @author    Barry Ceelen <b@rryceelen.com>
 * @license   GPL-3.0+
 * @link      https://github.com/barryceelen/wp-default-admin-color-scheme
 * @copyright 2013 Barry Ceelen
 *
 * Plugin Name:       Default Admin Color Scheme
 * Plugin URI:        https://github.com/barryceelen/wp-default-admin-color-scheme
 * Description:       Select a default admin color scheme for all users.
 * Version:           1.0.2
 * Author:            Barry Ceelen
 * Author URI:        https://github.com/barryceelen
 * Text Domain:       default-admin-color-scheme
 * License:           GPL-3.0+
 * License URI:       http://www.gnu.org/licenses/gpl-3.0.txt
 * Domain Path:       /languages
 * GitHub Plugin URI: https://github.com/barryceelen/wp-default-admin-color-scheme
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

if ( is_admin() ) {
	require_once( plugin_dir_path( __FILE__ ) . 'class-default-admin-color-scheme.php' );
	add_action( 'plugins_loaded', array( 'Default_Admin_Color_Scheme', 'get_instance' ) );
}

/**
 * Save default settings.
 *
 * Note: When the plugin is deleted, the uninstall.php file is loaded.
 */
register_activation_hook( __FILE__, array( 'Default_Admin_Color_Scheme', 'activate' ) );
