<?php
/**
 * Plugin Name.
 *
 * @package   Default_Admin_Color_Scheme
 * @author    Barry Ceelen <b@rryceelen.com>
 * @license   GPL-3.0+
 * @link      https://github.com/barryceelen/wp-default-admin-color-scheme
 * @copyright 2013 Barry Ceelen
 */

/**
 * Plugin class.
 *
 * @package Default_Admin_Color_Scheme
 * @author  Barry Ceelen <b@rryceelen.com>
 */
class Default_Admin_Color_Scheme {

	/**
	 * Plugin version, used for cache-busting of style and script file references.
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	const VERSION = '1.0.2';

	/**
	 * Instance of this class.
	 *
	 * @since 1.0.0
	 *
	 * @var object
	 */
	protected static $instance = null;

	/**
	 * Initialize the plugin by setting localization and loading public scripts
	 * and styles.
	 *
	 * @since 1.0.0
	 */
	private function __construct() {

		// Load plugin text domain.
		add_action( 'init', array( $this, 'load_plugin_textdomain' ) );

		// Activate plugin when new blog is added.
		add_action( 'wpmu_new_blog', array( $this, 'activate_new_site' ) );

		// Register settings.
		add_action( 'admin_init', array( $this, 'register_settings' ) );

		// Save default color scheme in a hacky way.
		add_filter( 'pre_update_option_plugin_default_admin_color_scheme', array( $this, 'save_color_scheme' ), 0 );

		// Save color scheme via ajax.
		add_action( 'wp_ajax_save-default-color-scheme', array( $this, 'ajax_save_color_scheme' ), 1 );

		// Load JavaScript.
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_scripts' ) );

		// Maybe remove color picker from user profile.
		add_action( 'admin_head-profile.php', array( $this, 'maybe_remove_profile_color_picker' ) );

		// Filter 'get_user_option_admin_color' to preselect the default scheme on the "General Settings" page.
		add_action( 'admin_head-options-general.php', array( $this, 'filter_get_user_option_admin_color_on_options_general' ) );

		// Unfortunately, a default value is set on user creation, let's work around this.
		add_filter( 'update_user_metadata', array( $this, 'maybe_set_override' ), 10, 3 );

		// Add an action link pointing to the general options page.
		$plugin_basename = plugin_basename( plugin_dir_path( __FILE__ ) . 'default-admin-color-scheme.php' );
		add_filter( 'plugin_action_links_' . $plugin_basename, array( $this, 'add_action_links' ) );

		// All of the above, solely for this little pièce de résistance.
		add_filter( 'get_user_option_admin_color', array( $this, 'filter_get_user_option_admin_color' ) );
	}

	/**
	 * Return an instance of this class.
	 *
	 * @since 1.0.0
	 *
	 * @return object A single instance of this class.
	 */
	public static function get_instance() {

		// If the single instance hasn't been set, set it now.
		if ( null === self::$instance ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * Fired when the plugin is activated.
	 *
	 * @since 1.0.0
	 *
	 * @param bool $network_wide True if WPMU superadmin uses
	 *                           "Network Activate" action, false if
	 *                           WPMU is disabled or plugin is
	 *                           activated on an individual blog.
	 */
	public static function activate( $network_wide ) {

		if ( function_exists( 'is_multisite' ) && is_multisite() ) {

			if ( $network_wide ) {

				// Get all blog ids.
				$blog_ids = self::get_blog_ids();

				foreach ( $blog_ids as $blog_id ) {

					switch_to_blog( $blog_id );
					self::single_activate();
				}

				restore_current_blog();

			} else {
				self::single_activate();
			}
		} else {
			self::single_activate();
		}

	}

	/**
	 * Fired when the plugin is deactivated.
	 *
	 * @since 1.0.0
	 *
	 * @param boolean $network_wide True if WPMU superadmin uses
	 *                              "Network Deactivate" action, false if
	 *                              WPMU is disabled or plugin is
	 *                              deactivated on an individual blog.
	 */
	public static function deactivate( $network_wide ) {

		if ( function_exists( 'is_multisite' ) && is_multisite() ) {

			if ( $network_wide ) {

				// Get all blog ids.
				$blog_ids = self::get_blog_ids();

				foreach ( $blog_ids as $blog_id ) {

					switch_to_blog( $blog_id );
					self::single_deactivate();

				}

				restore_current_blog();

			} else {
				self::single_deactivate();
			}
		} else {
			self::single_deactivate();
		}

	}

	/**
	 * Fired when a new site is activated with a WPMU environment.
	 *
	 * @since 1.0.0
	 *
	 * @param int $blog_id ID of the new blog.
	 */
	public function activate_new_site( $blog_id ) {

		if ( 1 !== did_action( 'wpmu_new_blog' ) ) {
			return;
		}

		switch_to_blog( $blog_id );
		self::single_activate();
		restore_current_blog();

	}

	/**
	 * Get all blog ids of blogs in the current network that are:
	 * - not archived
	 * - not spam
	 * - not deleted
	 *
	 * @since 1.0.0
	 *
	 * @return array|false The blog ids, false if no matches.
	 */
	private static function get_blog_ids() {

		global $wpdb;

		// get an array of blog ids.
		$sql = "SELECT blog_id FROM $wpdb->blogs WHERE archived = '0' AND spam = '0' AND deleted = '0'";

		return $wpdb->get_col( $sql ); // WPCS: db call ok. unprepared SQL ok.

	}

	/**
	 * Fired for each blog when the plugin is activated.
	 *
	 * @since 1.0.0
	 */
	private static function single_activate() {
		add_option(
			'plugin_default_admin_color_scheme',
			array(
				'users_can_change_color_scheme' => 1,
				'color_scheme' => 'fresh',
			)
		);
	}

	/**
	 * Load the plugin text domain for translation.
	 *
	 * @since 1.0.0
	 */
	public function load_plugin_textdomain() {
		load_plugin_textdomain( 'default-admin-color-scheme', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );
	}

	/**
	 * Add a settings section to the 'General Settings' page.
	 *
	 * @since 1.0.0
	 */
	public function register_settings() {

		$option_name = 'plugin_default_admin_color_scheme';

		register_setting(
			'general',
			$option_name,
			array( $this, 'settings_validate' )
		);
		add_settings_section(
			$option_name,
			__( 'Admin Color Scheme', 'default-admin-color-scheme' ),
			'__return_false',
			'general'
		);
		add_settings_field(
			'user-can-change-color',
			__( 'User Color Scheme', 'default-admin-color-scheme' ),
			array( $this, 'settings_checkbox' ),
			'general',
			$option_name
		);
		add_settings_field(
			'color-picker',
			__( 'Default Color Scheme', 'default-admin-color-scheme' ),
			'admin_color_scheme_picker', // Uses the default color scheme picker.
			'general',
			$option_name
		);
	}

	/**
	 * Validate settings.
	 *
	 * @since 1.0.0
	 *
	 * @param array $input Plugin setting to validate.
	 */
	public function settings_validate( $input ) {
		$input['users_can_change_color_scheme'] = absint( $input['users_can_change_color_scheme'] );
		return $input;
	}

	/**
	 * Display a checkbox for the 'users_can_change_color_scheme' option.
	 *
	 * @since 1.0.0
	 */
	public function settings_checkbox() {

		$option = get_option( 'plugin_default_admin_color_scheme' );

		include( 'templates/settings-checkbox.php' );
	}

	/**
	 * Save color scheme.
	 *
	 * To select the default scheme the color scheme picker of the user profile page is used.
	 * Its value is not picked up by our settings_validate() method as the field values are not namespaced.
	 * To work around that limitation, this function is hooked into saving the 'plugin_default_admin_color_scheme' option.
	 *
	 * Suggestions for improvement welcome!
	 *
	 * @since 1.0.0
	 *
	 * @param string $value Color scheme setting, eg. 'fresh'.
	 */
	public function save_color_scheme( $value ) {

		// Do not filter $value when doing ajax.
		if ( defined( 'DOING_AJAX' ) && DOING_AJAX ) {
			return $value;
		}

		if (
			! empty( $_POST['color-nonce'] ) // WPCS: input var okay.
			&&
			wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['color-nonce'] ) ), 'save-color-scheme' ) // WPCS: input var okay.
		) {
			$value['color_scheme'] = isset( $_POST['admin_color'] ) ? sanitize_key( $_POST['admin_color'] ) : 'fresh'; // WPCS: input var okay.
		}

		return $value;
	}

	/**
	 * Auto-save the selected color scheme.
	 *
	 * @since 1.0.0
	 */
	public function ajax_save_color_scheme() {

		global $_wp_admin_css_colors;

		check_ajax_referer( 'save-color-scheme', 'nonce' );

		if ( empty( $_POST['color_scheme'] ) ) { // WPCS: input var okay.
			wp_send_json_error();
		}

		$color_scheme = sanitize_key( $_POST['color_scheme'] ); // WPCS: input var okay.

		if ( ! isset( $_wp_admin_css_colors[ $color_scheme ] ) ) {
			wp_send_json_error();
		}

		$option = get_option( 'plugin_default_admin_color_scheme' );
		$option['color_scheme'] = $color_scheme;
		update_option( 'plugin_default_admin_color_scheme', $option );
		wp_send_json_success();
	}

	/**
	 * Register and enqueue JavaScript files.
	 *
	 * @since 1.0.0
	 */
	public function enqueue_scripts() {

		$screen = get_current_screen();

		if ( 'options-general' !== $screen->id ) {
			return;
		}

		wp_enqueue_script(
			'default-admin-color-scheme',
			plugins_url( 'js/admin.js', __FILE__ ),
			array( 'jquery' ),
			self::VERSION,
			true
		);
	}

	/**
	 * Remove color picker from profile edit screen if users are not allowed to choose their own admin color scheme,
	 *
	 * @since 1.0.0
	 */
	public function maybe_remove_profile_color_picker() {
		$option = get_option( 'plugin_default_admin_color_scheme' );
		if ( 1 !== $option['users_can_change_color_scheme'] ) {
			remove_action( 'admin_color_scheme_picker', 'admin_color_scheme_picker' );
		}
	}

	/**
	 * Filter 'get_user_option_admin_color' right before showing the color picker in preferences.
	 *
	 * @since  1.0.0
	 */
	public function filter_get_user_option_admin_color_on_options_general() {
		add_filter( 'get_user_option_admin_color', array( $this, 'get_default_admin_color_scheme' ) );
	}

	/**
	 * Get the default admin color scheme.
	 *
	 * Helper for the 'filter_get_user_option_admin_color_on_options_general' function.
	 *
	 * @since  1.0.0
	 *
	 * @return string
	 */
	public function get_default_admin_color_scheme() {
		$option = get_option( 'plugin_default_admin_color_scheme' );
		$color_scheme = isset( $option['color_scheme'] ) ? $option['color_scheme'] : 'fresh';
		return $color_scheme;
	}

	/**
	 * Set flag if the user selects a color scheme.
	 *
	 * @since  1.0.0
	 *
	 * @param null|bool $check      Whether to allow updating metadata for the given type.
	 * @param int       $object_id  Object ID.
	 * @param string    $meta_key   Meta key.
	 */
	public function maybe_set_override( $check, $object_id, $meta_key ) {
		if ( 'admin_color' === $meta_key ) {
			update_user_option( $object_id, 'plugin_default_admin_color_scheme_override', 1 );
		}
	}

	/**
	 * Add settings action link to the plugins page.
	 *
	 * @since  1.0.0
	 *
	 * @param array $actions An array of plugin action links.
	 */
	public function add_action_links( $actions ) {
		return array_merge(
			array(
				'settings' => '<a href="' . admin_url( 'options-general.php?#users_can_change_color_scheme' ) . '">' . __( 'Settings', 'default-admin-color-scheme' ) . '</a>',
			),
			$actions
		);
	}

	/**
	 * Filter 'get_user_option_admin_color' to return the desired color scheme.
	 *
	 * @since 1.0.0
	 *
	 * @param  string $color_scheme User color scheme option.
	 * @return string
	 */
	public function filter_get_user_option_admin_color( $color_scheme ) {

		$option = get_option( 'plugin_default_admin_color_scheme' );
		$default_color_scheme = isset( $option['color_scheme'] ) ? $option['color_scheme'] : $color_scheme;

		if ( isset( $option['users_can_change_color_scheme'] ) && 1 === (int) $option['users_can_change_color_scheme'] ) {
			if ( 1 === (int) get_user_option( 'plugin_default_admin_color_scheme_override' ) ) {
				return $color_scheme;
			} else {
				return $default_color_scheme;
			}
		}

		return $default_color_scheme;
	}
}
