<?php
/**
 * Main plugin file.
 *
 * Author:            Barry Ceelen
 * Author URI:        https://github.com/barryceelen
 * Description:       Select a default admin color scheme for all users.
 * Domain Path:       /languages
 * License:           GPL-3.0+
 * License URI:       http://www.gnu.org/licenses/gpl-3.0.txt
 * Plugin Name:       Default Admin Color Scheme
 * Plugin URI:        https://github.com/barryceelen/wp-default-admin-color-scheme
 * Text Domain:       default-admin-color-scheme
 * Version:           1.0.2
 * Requires PHP:      5.3.0
 * Requires at least: 3.8.0
 * GitHub Plugin URI: https://github.com/barryceelen/wp-default-admin-color-scheme
 *
 * @package Default_Admin_Color_Scheme
 * @author    Barry Ceelen <b@rryceelen.com>
 * @license   GPL-3.0+
 * @link      https://github.com/barryceelen/wp-default-admin-color-scheme
 * @copyright 2013 Barry Ceelen
 *
 * phpcs:disable WordPressVIPMinimum.Files.IncludingFile.UsingCustomConstant
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

if ( is_admin() ) {
	require_once plugin_dir_path( __FILE__ ) . 'class-default-admin-color-scheme.php';
	add_action( 'plugins_loaded', array( 'Default_Admin_Color_Scheme', 'get_instance' ) );
}

/**
 * Save default settings.
 *
 * Note: When the plugin is deleted, the uninstall.php file is loaded.
 */
register_activation_hook( __FILE__, array( 'Default_Admin_Color_Scheme', 'activate' ) );
