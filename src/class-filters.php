<?php
/**
 * Filters
 *
 * @package useraccesshub
 * @since 1.0.0
 */

namespace UserAccessHub;

/**
 * Filters class.
 *
 * @since 1.0.0
 */
final class Filters {

	/**
	 * Authenticate: Handle Login.
	 *
	 * @param \WP_User|\WP_Error|NULL $user The user if authenticated.
	 *
	 * @return \WP_User|\WP_Error|NULL
	 *   The user, an error or NULL depending on the state of the login.
	 *
	 * @since 1.0.0
	 */
	public function handle_login( $user ) {
		if ( $user instanceof \WP_User ) {
			if ( ! empty( Options::enabled() ) ) {

				// Prevent login if the user has a role that is managed by User Access Hub,
				// unless local accounts are allowed.
				if ( empty( Options::allow_local() ) ) {
					$managed_roles = array_intersect( Options::roles(), $user->roles );
					if ( count( $managed_roles ) ) {
						return new \WP_Error( 'useraccesshub', 'This user must login using User Access Hub.' );
					}
				}
			}
		}

		return $user;
	}

	/**
	 * Authenticate: Handle Reset.
	 *
	 * @param \WP_User|bool $user   The user to reset.
	 * @param \WP_Error     $errors The errors returned so far from the validation process.
	 *
	 * @return \WP_User|bool The user that was passed in.
	 *
	 * @since 1.0.0
	 */
	public function handle_reset( $user, \WP_Error $errors ) {
		if ( $user ) {
			if ( ! empty( Options::enabled() ) ) {

				// Prevent password retrieval if the user has a role that is managed by User Access Hub, unless local accounts are allowed.
				if ( empty( Options::allow_local() ) ) {
					$managed_roles = array_intersect( Options::roles(), $user->roles );
					if ( count( $managed_roles ) ) {
						$errors->add( 'useraccesshub', 'Password retrieval for this user must be done using User Access Hub.' );
					}
				}
			}
		}

		return $user;
	}

	/**
	 * Query Vars.
	 *
	 * @param array $query_vars The existing query vars to modify.
	 *
	 * @return array The modified query vars.
	 *
	 * @since 1.0.0
	 */
	public function query_vars( array $query_vars ) {
		$query_vars[] = Plugin::QUERY_ENDPOINT;

		return $query_vars;
	}

	/**
	 * Settings Link.
	 *
	 * @param array $action_links The existing action links to modify.
	 *
	 * @return array The modified action links.
	 *
	 * @since 1.0.0
	 */
	public function settings_link( array $action_links ) {
		$action_links[] = '<a href="' . get_admin_url() . 'admin.php?page=' . Plugin::SLUG . '">' . Plugin::OPTIONS_SETTINGS_NAME . '</a>';

		return $action_links;
	}

	/**
	 * Template Include.
	 *
	 * @param string $template The initially selected template.
	 *
	 * @return string The modified template.
	 *
	 * @since 1.0.0
	 */
	public function template_include( string $template ) {
		$endpoint = get_query_var( Plugin::QUERY_ENDPOINT );
		if ( ! empty( $endpoint ) ) {
			switch ( $endpoint ) {
				case 'handshake':
					return plugin_dir_path( Plugin::FILE ) . '/endpoint/handshake.php';
				case 'packages':
					return plugin_dir_path( Plugin::FILE ) . '/endpoint/packages.php';
				case 'roles':
					return plugin_dir_path( Plugin::FILE ) . '/endpoint/roles.php';
				case 'sso':
					return plugin_dir_path( Plugin::FILE ) . '/endpoint/sso.php';
			}
		}

		return $template;
	}
}
