<?php
/**
 * Fired when the plugin is uninstalled.
 *
 * @package PluginPizza\DefaultAdminColorScheme
 */

// If uninstall not called from WordPress, then exit.
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

delete_option( 'plugin_default_admin_color_scheme' );
delete_metadata( 'user', 0, 'plugin_default_admin_color_scheme_override', '', true );
