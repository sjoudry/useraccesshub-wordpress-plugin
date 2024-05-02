<?php
/**
 * Settings
 *
 * @package useraccesshub
 * @since 1.0.0
 */

namespace UserAccessHub;

/**
 * Settings class.
 *
 * @since 1.0.0
 */
final class Settings {

	/**
	 * Settings: Register Settings and Options.
	 *
	 * @since 1.0.0
	 */
	public function register_settings() {
		register_setting( Plugin::OPTIONS_AUTHENTICATION, Plugin::OPTIONS_AUTHENTICATION );
		register_setting( Plugin::OPTIONS_ROLES, Plugin::OPTIONS_ROLES );
		register_setting( Plugin::OPTIONS_SETTINGS, Plugin::OPTIONS_SETTINGS );

		add_settings_section( Plugin::OPTIONS_SETTINGS, Plugin::OPTIONS_SETTINGS_NAME, array( $this, 'settings_content' ), Plugin::OPTIONS_SETTINGS_SLUG );
		add_settings_field( Plugin::OPTION_ENABLED, 'Enable all of the User Access Hub functionality.', array( $this, 'option_enabled' ), Plugin::OPTIONS_SETTINGS_SLUG, Plugin::OPTIONS_SETTINGS );
		add_settings_field( Plugin::OPTION_REDIRECT, 'Redirect URL/Path', array( $this, 'option_redirect' ), Plugin::OPTIONS_SETTINGS_SLUG, Plugin::OPTIONS_SETTINGS );

		add_settings_section( Plugin::OPTIONS_ROLES, Plugin::OPTIONS_ROLES_NAME, array( $this, 'roles_content' ), Plugin::OPTIONS_ROLES_SLUG );
		add_settings_field( Plugin::OPTION_ROLES, 'Roles', array( $this, 'option_roles' ), Plugin::OPTIONS_ROLES_SLUG, Plugin::OPTIONS_ROLES );
		add_settings_field( Plugin::OPTION_DEFAULT_ROLE, 'Default Role', array( $this, 'option_default_role' ), Plugin::OPTIONS_ROLES_SLUG, Plugin::OPTIONS_ROLES );
		add_settings_field( Plugin::OPTION_ALLOW_LOCAL, 'Allow local accounts to login (for managed roles above).', array( $this, 'option_allow_local' ), Plugin::OPTIONS_ROLES_SLUG, Plugin::OPTIONS_ROLES );

		add_settings_section( Plugin::OPTIONS_AUTHENTICATION, Plugin::OPTIONS_AUTHENTICATION_NAME, array( $this, 'authentication_content' ), Plugin::OPTIONS_AUTHENTICATION_SLUG );
		add_settings_field( Plugin::OPTION_HANDSHAKE_ENABLED, 'Handshake Enabled', array( $this, 'option_handshake_enabled' ), Plugin::OPTIONS_AUTHENTICATION_SLUG, Plugin::OPTIONS_AUTHENTICATION );
		add_settings_field( Plugin::OPTION_API_KEY, 'API Key', array( $this, 'option_api_key' ), Plugin::OPTIONS_AUTHENTICATION_SLUG, Plugin::OPTIONS_AUTHENTICATION );
		add_settings_field( Plugin::OPTION_PUBLIC_KEY, 'Public Key', array( $this, 'option_public_key' ), Plugin::OPTIONS_AUTHENTICATION_SLUG, Plugin::OPTIONS_AUTHENTICATION );
		add_settings_field( Plugin::OPTION_SITE_ID, 'Site ID', array( $this, 'option_site_id' ), Plugin::OPTIONS_AUTHENTICATION_SLUG, Plugin::OPTIONS_AUTHENTICATION );
	}

	/**
	 * Menu: Add Settings Pages.
	 *
	 * @since 1.0.0
	 */
	public function add_settings_pages() {
		global $submenu;

		require_once ABSPATH . 'wp-admin/includes/class-wp-filesystem-base.php';
		require_once ABSPATH . 'wp-admin/includes/class-wp-filesystem-direct.php';

		// Load the icon.
		$filesystem = new \WP_Filesystem_Direct( true );
		$data       = $filesystem->get_contents( dirname( realpath( Plugin::FILE ) ) . '/includes/images/useraccesshub_icon.svg' );
		$image      = 'data:image/svg+xml;base64,' . base64_encode( $data ); // phpcs:ignore

		add_menu_page( Plugin::OPTIONS_SETTINGS_NAME, Plugin::NAME, Plugin::OPTIONS_CAPABILITY, Plugin::OPTIONS_SETTINGS_SLUG, array( $this, 'settings_page' ), $image );
		add_submenu_page( Plugin::OPTIONS_SETTINGS_SLUG, Plugin::OPTIONS_ROLES_NAME, Plugin::OPTIONS_ROLES_NAME, Plugin::OPTIONS_CAPABILITY, Plugin::OPTIONS_ROLES_SLUG, array( $this, 'roles_page' ) );
		add_submenu_page( Plugin::OPTIONS_SETTINGS_SLUG, Plugin::OPTIONS_AUTHENTICATION_NAME, Plugin::OPTIONS_AUTHENTICATION_NAME, Plugin::OPTIONS_CAPABILITY, Plugin::OPTIONS_AUTHENTICATION_SLUG, array( $this, 'authentication_page' ) );

		// Change the parent link title in the submenu.
		$submenu[ Plugin::SLUG ][0][0] = 'Settings'; // phpcs:ignore
	}

	/**
	 * Settings: Authentication Top Content.
	 *
	 * @since 1.0.0
	 */
	public function authentication_content() {
		print '<p>The following data is for informational purposes and none of these values can be modified here.</p>';
	}

	/**
	 * Settings: Authentication Page.
	 *
	 * @since 1.0.0
	 */
	public function authentication_page() {

		// Check user capabilities.
		if ( ! current_user_can( Plugin::OPTIONS_CAPABILITY ) ) {
			return;
		}

		?>
		<form action="options.php" method="post">
		<?php
		settings_fields( Plugin::OPTIONS_AUTHENTICATION );
		do_settings_sections( Plugin::OPTIONS_AUTHENTICATION_SLUG );
		?>
		</form>
		<?php
	}

	/**
	 * Option: Allow Local.
	 *
	 * @since 1.0.0
	 */
	public function option_allow_local() {
		print '<input id="' . esc_attr( Plugin::OPTION_ALLOW_LOCAL ) . '" name="' . esc_attr( Plugin::OPTIONS_ROLES . '[' . Plugin::OPTION_ALLOW_LOCAL . ']' ) . '" type="checkbox"';
		if ( ! empty( Options::allow_local() ) ) {
			print ' checked="checked"';
		}
		print ' />';
	}

	/**
	 * Option: API Key.
	 *
	 * @since 1.0.0
	 */
	public function option_api_key() {
		$api_key = empty( Options::api_key() ) ? '' : Options::api_key();

		print '<input id="' . esc_attr( Plugin::OPTION_API_KEY ) . '" name="' . esc_attr( Plugin::OPTIONS_AUTHENTICATION . '[' . Plugin::OPTION_API_KEY . ']' ) . '" type="text" value="' . esc_attr( $api_key ) . '" disabled="disabled" class="large-text" />';
		print '<p class="description">The API key is generated on plugin activation. This API key is only used during the handshake process between the User Access Hub and this site. Once the handshake is complete the public key below is used during communication between the two. This value can be regenerated using the WP CLI.</p>';
	}

	/**
	 * Option: Default Role.
	 *
	 * @since 1.0.0
	 */
	public function option_default_role() {
		global $wp_roles;

		$default_role = empty( Options::default_role() ) ? 'editor' : Options::default_role();

		print '<select id="' . esc_attr( Plugin::OPTION_DEFAULT_ROLE ) . '" name="' . esc_attr( Plugin::OPTIONS_ROLES . '[' . Plugin::OPTION_DEFAULT_ROLE . ']' ) . '">';
		foreach ( $wp_roles->roles as $role_id => $wp_role ) {
			print '<option value="' . esc_attr( $role_id ) . '"';
			if ( $role_id === $default_role ) {
				print ' selected="selected"';
			}
			print '>' . esc_html( $wp_role['name'] ) . '</option>';
		}
		print '</select>';
		print '<p class="description">Select which role should be considered the default.</p>';
	}

	/**
	 * Option: Enabled.
	 *
	 * @since 1.0.0
	 */
	public function option_enabled() {
		print '<input id="' . esc_attr( Plugin::OPTION_ENABLED ) . '" name="' . esc_attr( Plugin::OPTIONS_SETTINGS . '[' . Plugin::OPTION_ENABLED . ']' ) . '" type="checkbox"';
		if ( ! empty( Options::enabled() ) ) {
			print ' checked="checked"';
		}
		print ' />';
		print '<p class="description">Toggle all of the functionality of this plugin on or off.</p>';
	}

	/**
	 * Option: Handshake Enabled.
	 *
	 * @since 1.0.0
	 */
	public function option_handshake_enabled() {
		print '<input id="' . esc_attr( Plugin::OPTION_HANDSHAKE_ENABLED ) . '" name="' . esc_attr( Plugin::OPTIONS_AUTHENTICATION . '[' . Plugin::OPTION_HANDSHAKE_ENABLED . ']' ) . '" type="checkbox"';
		if ( ! empty( Options::handshake_enabled() ) ) {
			print ' checked="checked"';
		}
		print ' disabled="disabled" />';
		print '<p class="description">Once the handshake is done, the handshake endpoint is disabled (for security purposes). This value can be enabled with the WP CLI. Only enable if a new handshake is required.</p>';
	}

	/**
	 * Option: Public Key.
	 *
	 * @since 1.0.0
	 */
	public function option_public_key() {
		$public_key = empty( Options::public_key() ) ? '' : Options::public_key();

		print '<textarea id="' . esc_attr( Plugin::OPTION_PUBLIC_KEY ) . '" name="' . esc_attr( Plugin::OPTIONS_AUTHENTICATION . '[' . Plugin::OPTION_PUBLIC_KEY . ']' ) . '" disabled="disabled" style="resize:none" cols="40" rows="5" class="large-text">' . esc_html( $public_key ) . '</textarea>';
		print '<p class="description">The public key is set during the handshake process between the User Access Hub and this site and is used for all communication between this site and the User Access Hub.</p>';
	}

	/**
	 * Option: Redirect.
	 *
	 * @since 1.0.0
	 */
	public function option_redirect() {
		$redirect = empty( Options::redirect() ) ? '' : Options::redirect();

		print '<input id="' . esc_attr( Plugin::OPTION_REDIRECT ) . '" name="' . esc_attr( Plugin::OPTIONS_SETTINGS . '[' . Plugin::OPTION_REDIRECT . ']' ) . '" type="text" value="' . esc_attr( $redirect ) . '" class="regular-text" />';
		print '<p class="description">Where to redirect the user after a login, including a preceeding /. For example: /welcome.</p>';
	}

	/**
	 * Option: Roles.
	 *
	 * @since 1.0.0
	 */
	public function option_roles() {
		global $wp_roles;

		$roles = empty( Options::roles() ) ? array() : Options::roles();

		foreach ( $wp_roles->roles as $role_id => $wp_role ) {
			print '<p><input id="' . esc_attr( Plugin::OPTION_ROLES . '-' . $role_id ) . '" name="' . esc_attr( Plugin::OPTIONS_ROLES . '[' . Plugin::OPTION_ROLES . '][]' ) . '" value="' . esc_attr( $role_id ) . '" type="checkbox"';
			if ( in_array( $role_id, $roles, true ) ) {
				print ' checked="checked"';
			}
			print ' /><label for="' . esc_attr( Plugin::OPTION_ROLES . '-' . $role_id ) . '">' . esc_html( $wp_role['name'] ) . '</label></p>';
		}
		print '<p class="description">Select which roles should have SSO login enabled from the User Access Hub. All roles not selected will still be able to login using the site login form.</p>';
	}

	/**
	 * Option: Site ID.
	 *
	 * @since 1.0.0
	 */
	public function option_site_id() {
		$site_id = empty( Options::site_id() ) ? '' : Options::site_id();

		print '<input id="' . esc_attr( Plugin::OPTION_SITE_ID ) . '" name="' . esc_attr( Plugin::OPTIONS_AUTHENTICATION . '[' . Plugin::OPTION_SITE_ID . ']' ) . '" type="text" value="' . esc_attr( $site_id ) . '" disabled="disabled" class="small-text" />';
		print '<p class="description">The site ID is the unique identifier for this site in the User Access Hub and is using during communication between the and this site. This value is set during the handshake process between the two.</p>';
	}

	/**
	 * Settings: Roles Top Content.
	 *
	 * @since 1.0.0
	 */
	public function roles_content() {
		print '<p>The following are role specific settings for this plugin.</p>';
	}

	/**
	 * Settings: Roles Page.
	 *
	 * @since 1.0.0
	 */
	public function roles_page() {

		// Check user capabilities.
		if ( ! current_user_can( Plugin::OPTIONS_CAPABILITY ) ) {
			return;
		}

		?>
		<form action="options.php" method="post">
		<?php
		settings_fields( Plugin::OPTIONS_ROLES );
		do_settings_sections( Plugin::OPTIONS_ROLES_SLUG );
		submit_button( 'Save Settings' );
		?>
		</form>
		<?php
	}

	/**
	 * Settings: Settings Top Content.
	 *
	 * @since 1.0.0
	 */
	public function settings_content() {
		print '<p>The following are general settings for this plugin.</p>';
	}

	/**
	 * Settings: Settings Page.
	 *
	 * @since 1.0.0
	 */
	public function settings_page() {

		// Check user capabilities.
		if ( ! current_user_can( Plugin::OPTIONS_CAPABILITY ) ) {
			return;
		}

		?>
		<form action="options.php" method="post">
		<?php
		settings_fields( Plugin::OPTIONS_SETTINGS );
		do_settings_sections( Plugin::OPTIONS_SETTINGS_SLUG );
		submit_button( 'Save Settings' );
		?>
		</form>
		<?php
	}
}
