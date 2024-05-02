<?php
/**
 * Options
 *
 * @package useraccesshub
 * @since 1.0.3
 */

namespace UserAccessHub;

/**
 * Options class.
 *
 * @since 1.0.3
 */
final class Options {

	/**
	 * Get Option: Allow Local.
	 *
	 * @return string The option value.
	 *
	 * @since 1.0.3
	 */
	public static function allow_local() {
		return self::determine_option_value( Plugin::OPTIONS_ROLES, Plugin::OPTION_ALLOW_LOCAL, 'USERACCESSHUB_ALLOW_LOCAL' );
	}

	/**
	 * Get Option: API Key.
	 *
	 * @return string The option value.
	 *
	 * @since 1.0.3
	 */
	public static function api_key() {
		return self::determine_option_value( Plugin::OPTIONS_AUTHENTICATION, Plugin::OPTION_API_KEY, 'USERACCESSHUB_API_KEY' );
	}

	/**
	 * Get Option: Default Role.
	 *
	 * @return string The option value.
	 *
	 * @since 1.0.3
	 */
	public static function default_role() {
		return self::determine_option_value( Plugin::OPTIONS_ROLES, Plugin::OPTION_DEFAULT_ROLE, 'USERACCESSHUB_DEFAULT_ROLE' );
	}

	/**
	 * Get Option: Enabled.
	 *
	 * @return string The option value.
	 *
	 * @since 1.0.3
	 */
	public static function enabled() {
		return self::determine_option_value( Plugin::OPTIONS_SETTINGS, Plugin::OPTION_ENABLED, 'USERACCESSHUB_ENABLED' );
	}

	/**
	 * Get Option: Handshake Enabled.
	 *
	 * @return string The option value.
	 *
	 * @since 1.0.3
	 */
	public static function handshake_enabled() {
		return self::determine_option_value( Plugin::OPTIONS_AUTHENTICATION, Plugin::OPTION_HANDSHAKE_ENABLED, 'USERACCESSHUB_HANDSHAKE_ENABLED' );
	}

	/**
	 * Get Option: Public Key.
	 *
	 * @return string The option value.
	 *
	 * @since 1.0.3
	 */
	public static function public_key() {
		return self::determine_option_value( Plugin::OPTIONS_AUTHENTICATION, Plugin::OPTION_PUBLIC_KEY, 'USERACCESSHUB_PUBLIC_KEY' );
	}

	/**
	 * Get Option: Redirect.
	 *
	 * @return string The option value.
	 *
	 * @since 1.0.3
	 */
	public static function redirect() {
		return self::determine_option_value( Plugin::OPTIONS_SETTINGS, Plugin::OPTION_REDIRECT, 'USERACCESSHUB_REDIRECT' );
	}

	/**
	 * Get Option: Roles.
	 *
	 * @return array The option value.
	 *
	 * @since 1.0.3
	 */
	public static function roles() {
		return self::determine_option_value( Plugin::OPTIONS_ROLES, Plugin::OPTION_ROLES, 'USERACCESSHUB_ROLES' );
	}

	/**
	 * Get Option: Site ID.
	 *
	 * @return string The option value.
	 *
	 * @since 1.0.3
	 */
	public static function site_id() {
		return self::determine_option_value( Plugin::OPTIONS_AUTHENTICATION, Plugin::OPTION_SITE_ID, 'USERACCESSHUB_SITE_ID' );
	}

	/**
	 * Determine Option Value.
	 *
	 * @param string $options_name  The name of the options set.
	 * @param string $option_name   The name of the option in the set.
	 * @param string $override_name The name of the constant that may be set to override the option.
	 *
	 * @return string|array The option value.
	 *
	 * @since 1.0.3
	 */
	private static function determine_option_value( $options_name, $option_name, $override_name ) {
		if ( defined( $override_name ) ) {
			return constant( $override_name );
		} else {
			$options = get_option( $options_name );
			return isset( $options[ $option_name ] ) ? $options[ $option_name ] : '';
		}
	}

}
