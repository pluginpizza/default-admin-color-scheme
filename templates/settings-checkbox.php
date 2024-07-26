<?php
/**
 * Render the checkbox for the settings page.
 *
 * @package PluginPizza\DefaultAdminColorScheme
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
			value="1" <?php checked( 1, $option['users_can_change_color_scheme'] ); // phpcs:disable VariableAnalysis.CodeAnalysis.VariableAnalysis.UndefinedVariable?>
		>
		<?php esc_html_e( 'Users can select their own color scheme', 'default-admin-color-scheme' ); ?>
	</label>
</fieldset>
