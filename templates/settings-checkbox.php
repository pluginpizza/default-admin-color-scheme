<?php
/**
 * Render the checkbox for the settings page.
 *
 * @package   Default_Admin_Color_Scheme
 * @author    Barry Ceelen <b@rryceelen.com>
 * @license   GPL-3.0+
 * @link      https://github.com/barryceelen/wp-default-admin-color-scheme
 * @copyright 2013 Barry Ceelen
 */

?>
<fieldset>
	<legend class="screen-reader-text">
		<span>
			<?php esc_html_e( 'User Color Scheme', 'default-admin-color-scheme' ); ?>
		</span>
	</legend>
	<label>
		<input
			name="plugin_default_admin_color_scheme[users_can_change_color_scheme]"
			type="checkbox"
			id="users_can_change_color_scheme"
			value="1" <?php checked( 1, $option['users_can_change_color_scheme'] ); ?>
		>
		<?php esc_html_e( 'Users can select their own color scheme', 'default-admin-color-scheme' ); ?>
	</label>
</fieldset>
