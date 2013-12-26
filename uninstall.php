<?php
/**
 * Fired when the plugin is uninstalled.
 *
 * @package   Default_Admin_Color_Scheme
 * @author    Barry Ceelen <b@rryceelen.com>
 * @license   GPL-2.0+
 * @link      https://github.com/barryceelen/wp-default-admin-color-scheme
 * @copyright 2013 Barry Ceelen
 */

// If uninstall not called from WordPress, then exit
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

delete_option( 'plugin_default_admin_color_scheme' );
delete_metadata( 'user', 0, 'plugin_default_admin_color_scheme_override', '', true );
